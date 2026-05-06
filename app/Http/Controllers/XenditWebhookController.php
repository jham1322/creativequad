<?php

namespace App\Http\Controllers;

use App\Mail\CoursePaymentConfirmed;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class XenditWebhookController extends Controller
{
    public function invoice(Request $request): JsonResponse
    {
        $configuredToken = (string) config('services.xendit.webhook_token', '');
        $incomingToken = (string) $request->header('x-callback-token', '');

        if ($configuredToken !== '' && ! hash_equals($configuredToken, $incomingToken)) {
            return response()->json(['message' => 'Invalid callback token.'], 403);
        }

        $payload = $request->all();
        $status = strtoupper((string) ($payload['status'] ?? ''));
        $externalId = (string) ($payload['external_id'] ?? '');
        $email = $this->resolveEmail($payload);

        Log::info('Xendit invoice webhook received.', [
            'external_id' => $externalId,
            'status' => $status,
            'email' => $email,
        ]);

        if ($status !== 'PAID' || $externalId === '' || $email === null) {
            return response()->json(['received' => true]);
        }

        $cacheKey = 'xendit-course-paid-mail:' . $externalId;

        if (! Cache::store('file')->add($cacheKey, true, now()->addDays(30))) {
            return response()->json(['received' => true, 'duplicate' => true]);
        }

        $order = Cache::store('file')->get('xendit-order:' . $externalId, []);
        $orderModel = Order::query()
            ->where('xendit_reference', $externalId)
            ->orWhere(function ($query) use ($email): void {
                $query->where('email', $email)->where('status', 'pending');
            })
            ->latest()
            ->first();
        $user = User::query()
            ->where('xendit_reference', $externalId)
            ->orWhere('email', $email)
            ->first();

        if ($user instanceof User) {
            $user->forceFill([
                'xendit_reference' => $externalId,
                'course_slug' => 'build-real-full-stack-web-apps-using-ai-and-codex',
                'purchased_at' => $user->purchased_at ?? now(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        if ($orderModel instanceof Order) {
            $orderModel->forceFill([
                'user_id' => $user?->id ?? $orderModel->user_id,
                'status' => $orderModel->status === 'approved' ? 'approved' : 'paid',
                'payment_method' => (string) ($order['payment_method'] ?? strtoupper((string) ($payload['payment_method'] ?? data_get($payload, 'metadata.payment_method', $orderModel->payment_method ?? 'Xendit')))),
                'paid_at' => $orderModel->paid_at ?? now(),
            ])->save();
        }

        $resolvedName = $user?->first_name
            ?: ($order['name'] ?? $this->resolveName($payload));

        Mail::to($email)->send(new CoursePaymentConfirmed([
            'name' => $resolvedName,
            'email' => $email,
            'amount' => number_format((float) ($payload['paid_amount'] ?? $payload['amount'] ?? config('services.xendit.course_price', 2)), 2),
            'reference' => $externalId,
            'payment_method' => (string) ($order['payment_method'] ?? strtoupper((string) ($payload['payment_method'] ?? data_get($payload, 'metadata.payment_method', 'Xendit')))),
            'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
            'dashboard_url' => route('login'),
        ]));

        return response()->json(['received' => true, 'email_sent' => true]);
    }

    private function resolveEmail(array $payload): ?string
    {
        $candidates = [
            $payload['payer_email'] ?? null,
            $payload['customer']['email'] ?? null,
            $payload['customer_email'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveName(array $payload): string
    {
        $given = trim((string) ($payload['customer']['given_names'] ?? ''));
        $surname = trim((string) ($payload['customer']['surname'] ?? ''));
        $fullName = trim($given . ' ' . $surname);

        if ($fullName !== '') {
            return $fullName;
        }

        $email = $this->resolveEmail($payload);

        return $email !== null ? strtok($email, '@') : 'Student';
    }
}

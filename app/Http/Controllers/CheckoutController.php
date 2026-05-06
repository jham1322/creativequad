<?php

namespace App\Http\Controllers;

use App\Mail\CoursePaymentConfirmed;
use App\Mail\CoursePaymentPending;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(): View
    {
        return view('checkout', [
            'coursePrice' => number_format((float) config('services.xendit.course_price', 2), 2),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username'),
            ],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'payment_method' => ['required', 'string', 'in:gcash,maya,grabpay,qrph'],
        ], [
            'email.unique' => 'Email already exists.',
            'username.unique' => 'Username already exists.',
        ]);

        $secretKey = (string) config('services.xendit.secret_key');

        if ($secretKey === '') {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'checkout' => 'Xendit is not configured yet. Add your secret key first.',
                ]);
        }

        $price = (float) config('services.xendit.course_price', 2);
        $externalId = 'vibe-course-' . Str::uuid();
        $paymentMethodMap = [
            'gcash' => ['GCASH'],
            'maya' => ['PAYMAYA'],
            'grabpay' => ['GRABPAY'],
            'qrph' => ['QRPH'],
        ];

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->post(rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/') . '/v2/invoices', [
                'external_id' => $externalId,
                'amount' => $price,
                'currency' => 'PHP',
                'description' => 'Build Real Full Stack Web Apps using AI and Codex',
                'invoice_duration' => 86400,
                'success_redirect_url' => route('checkout.success', ['reference' => $externalId]),
                'failure_redirect_url' => route('checkout.failed', ['reference' => $externalId]),
                'customer' => [
                    'given_names' => $validated['first_name'],
                    'surname' => $validated['last_name'],
                    'email' => $validated['email'],
                ],
                'items' => [
                    [
                        'name' => 'Build Real Full Stack Web Apps using AI and Codex',
                        'quantity' => 1,
                        'price' => $price,
                        'category' => 'Course',
                        'url' => url('/'),
                    ],
                ],
                'payment_methods' => $paymentMethodMap[$validated['payment_method']],
                'metadata' => [
                    'username' => $validated['username'],
                    'payment_method' => $validated['payment_method'],
                ],
            ]);

        if ($response->failed() || ! $response->json('invoice_url')) {
            report('Xendit checkout creation failed: ' . $response->body());

            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'checkout' => 'We could not create your checkout session right now. Please try again in a moment.',
                ]);
        }

        $user = new User();
        $user->fill([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'xendit_reference' => $externalId,
            'course_slug' => 'build-real-full-stack-web-apps-using-ai-and-codex',
        ]);
        $user->save();

        $order = Order::query()->create([
            'user_id' => $user->id,
            'course_slug' => 'build-real-full-stack-web-apps-using-ai-and-codex',
            'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
            'amount' => $price,
            'currency' => 'PHP',
            'status' => 'pending',
            'payment_method' => strtoupper($validated['payment_method']),
            'xendit_reference' => $externalId,
            'invoice_url' => (string) $response->json('invoice_url'),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'source' => 'xendit',
        ]);

        Cache::store('file')->put('xendit-order:' . $externalId, [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'username' => $validated['username'],
            'payment_method' => strtoupper($validated['payment_method']),
        ], now()->addDays(30));

        try {
            Mail::to($validated['email'])->send(new CoursePaymentPending([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'amount' => number_format($price, 2),
                'reference' => $externalId,
                'payment_method' => strtoupper($validated['payment_method']),
                'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
                'payment_url' => $response->json('invoice_url'),
            ]));
        } catch (\Throwable $exception) {
            Log::warning('Pending checkout email could not be sent.', [
                'external_id' => $externalId,
                'email' => $validated['email'],
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()->away($response->json('invoice_url'));
    }

    public function success(Request $request): RedirectResponse
    {
        $reference = $request->string('reference')->value();

        if ($reference !== '') {
            try {
                $this->sendPaidConfirmationIfNeeded($reference);
            } catch (\Throwable $exception) {
                Log::warning('Paid confirmation fallback failed on checkout success.', [
                    'external_id' => $reference,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $request->session()->put('lms_access_granted', true);
        $request->session()->put('lms_access_reference', $reference);

        return redirect()->to(URL::temporarySignedRoute('lms.dashboard', now()->addHours(2), [
            'reference' => $reference,
            'enrolled' => 1,
        ]));
    }

    public function failed(Request $request): RedirectResponse
    {
        return redirect()
            ->route('checkout')
            ->withInput()
            ->withErrors([
                'checkout' => 'Checkout was not completed. You can review your details and try again.',
            ]);
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Please log in to access your course dashboard.',
                ]);
        }

        /** @var User $user */
        $user = Auth::user();

        $pendingOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $hasPaidAccess = $user->purchased_at !== null;

        if (! $hasPaidAccess && ! $pendingOrder instanceof Order) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('checkout')
                ->withErrors([
                    'checkout' => 'Your account does not have course access yet.',
                ]);
        }

        return view('lms-dashboard', [
            'studentName' => $user->first_name ?: $user->name,
            'hasPaidAccess' => $hasPaidAccess,
            'pendingOrder' => $pendingOrder,
            'lessons' => $this->resolveLessons(),
        ]);
    }

    private function resolveLessons()
    {
        if (! Schema::hasTable('lessons')) {
            return collect();
        }

        return Lesson::query()
            ->orderBy('module_number')
            ->orderBy('id')
            ->get();
    }

    private function sendPaidConfirmationIfNeeded(string $externalId): void
    {
        $secretKey = (string) config('services.xendit.secret_key');

        if ($secretKey === '') {
            return;
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->get(rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/') . '/v2/invoices', [
                'external_id' => $externalId,
            ]);

        if ($response->failed()) {
            Log::warning('Could not verify paid invoice on checkout success.', [
                'external_id' => $externalId,
                'body' => $response->body(),
            ]);

            return;
        }

        $invoice = collect($response->json())
            ->firstWhere('external_id', $externalId);

        if (! is_array($invoice) || strtoupper((string) ($invoice['status'] ?? '')) !== 'PAID') {
            return;
        }

        $email = (string) ($invoice['payer_email'] ?? data_get($invoice, 'customer.email', ''));
        $order = Cache::store('file')->get('xendit-order:' . $externalId, []);
        $resolvedEmail = $email !== '' ? $email : (string) ($order['email'] ?? '');

        if ($resolvedEmail === '') {
            return;
        }

        $user = User::query()
            ->where('xendit_reference', $externalId)
            ->orWhere('email', $resolvedEmail)
            ->first();

        $orderModel = Order::query()
            ->where('xendit_reference', $externalId)
            ->orWhere(function ($query) use ($resolvedEmail): void {
                $query->where('email', $resolvedEmail)->where('status', 'pending');
            })
            ->latest()
            ->first();

        if ($user instanceof User) {
            $user->forceFill([
                'xendit_reference' => $externalId,
                'course_slug' => 'build-real-full-stack-web-apps-using-ai-and-codex',
                'purchased_at' => $user->purchased_at ?? now(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            if (! Auth::check()) {
                Auth::login($user);
                request()->session()->regenerate();
            }
        }

        if ($orderModel instanceof Order) {
            $orderModel->forceFill([
                'user_id' => $user?->id ?? $orderModel->user_id,
                'status' => $orderModel->status === 'approved' ? 'approved' : 'paid',
                'payment_method' => (string) ($order['payment_method'] ?? strtoupper((string) ($invoice['payment_method'] ?? $orderModel->payment_method ?? 'Xendit'))),
                'paid_at' => $orderModel->paid_at ?? now(),
                'invoice_url' => $orderModel->invoice_url ?: (string) ($invoice['invoice_url'] ?? ''),
            ])->save();
        }

        $sentKey = 'xendit-course-paid-mail:' . $externalId;

        if (! Cache::store('file')->add($sentKey, true, now()->addDays(30))) {
            return;
        }

        Mail::to($resolvedEmail)->send(new CoursePaymentConfirmed([
            'name' => (string) ($order['name'] ?? strtok($resolvedEmail, '@') ?: 'Student'),
            'email' => $resolvedEmail,
            'amount' => number_format((float) ($invoice['paid_amount'] ?? $invoice['amount'] ?? config('services.xendit.course_price', 2)), 2),
            'reference' => $externalId,
            'payment_method' => (string) ($order['payment_method'] ?? strtoupper((string) ($invoice['payment_method'] ?? 'Xendit'))),
            'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
            'dashboard_url' => route('login'),
        ]));
    }
}

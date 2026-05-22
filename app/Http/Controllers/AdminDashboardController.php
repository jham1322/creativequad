<?php

namespace App\Http\Controllers;

use App\Mail\AdminManualEnrollmentCredentials;
use App\Mail\AdminStudentAccessNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    private const COURSE_SLUG = 'build-real-full-stack-web-apps-using-ai-and-codex';

    private const COURSE_NAME = 'Build Real Full-Stack Web Apps using AI and Codex';

    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $this->cleanupResolvedPendingOrders();

        $paidStatuses = ['paid', 'approved'];
        $paidOrders = Order::query()
            ->whereIn('status', $paidStatuses)
            ->latest('paid_at')
            ->latest()
            ->get();

        $pendingOrders = Order::query()
            ->where('status', 'pending')
            ->where(function ($query): void {
                $query
                    ->where('payment_method', 'OFFLINE_GCASH')
                    ->orWhere('source', 'offline_gcash');
            })
            ->latest()
            ->get();

        return view('admin.dashboard', [
            'adminEmail' => (string) $request->session()->get('admin_email'),
            'totalSales' => $paidOrders->sum(fn (Order $order) => (float) $order->amount),
            'totalStudents' => User::query()->whereNotNull('purchased_at')->count(),
            'paidOrdersCount' => $paidOrders->count(),
            'pendingOrdersCount' => $pendingOrders->count(),
            'students' => User::query()->whereNotNull('purchased_at')->latest('purchased_at')->get(),
            'pendingOrders' => $pendingOrders,
        ]);
    }

    public function enroll(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:40', 'alpha_dash'],
        ]);

        $email = strtolower(trim($validated['email']));
        $username = strtolower(trim($validated['username']));
        $firstName = trim($validated['first_name']);
        $lastName = trim((string) ($validated['last_name'] ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        $existingUser = User::query()->where('email', $email)->first();
        $usernameOwner = User::query()->where('username', $username)->first();

        if ($usernameOwner instanceof User && (! $existingUser || $usernameOwner->id !== $existingUser->id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'admin' => 'That username is already being used by another student account.',
                ]);
        }

        if ($existingUser instanceof User && $existingUser->purchased_at !== null) {
            return back()
                ->withInput()
                ->withErrors([
                    'admin' => 'That student already has active course access.',
                ]);
        }

        $temporaryPassword = Str::upper(Str::random(4)) . '-' . Str::random(8);
        $reference = 'manual-enroll-' . Str::uuid();
        $coursePrice = (float) config('services.xendit.course_price', 599);

        $user = $existingUser ?? new User();
        $user->forceFill([
            'name' => $fullName !== '' ? $fullName : $firstName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $temporaryPassword,
            'xendit_reference' => $reference,
            'course_slug' => self::COURSE_SLUG,
            'purchased_at' => now(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        Order::query()->create([
            'user_id' => $user->id,
            'course_slug' => self::COURSE_SLUG,
            'course_name' => self::COURSE_NAME,
            'amount' => $coursePrice,
            'currency' => 'PHP',
            'status' => 'approved',
            'payment_method' => 'MANUAL_ENROLL',
            'xendit_reference' => $reference,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'username' => $username,
            'paid_at' => now(),
            'approved_at' => now(),
            'source' => 'admin_manual_enroll',
            'notes' => 'Student manually enrolled by admin and granted direct course access.',
        ]);

        $this->cleanupPendingOrdersFor($user, $email, $reference);

        try {
            Mail::to($email)->send(new AdminManualEnrollmentCredentials([
                'name' => $firstName,
                'email' => $email,
                'username' => $username,
                'temporary_password' => $temporaryPassword,
                'course_name' => self::COURSE_NAME,
                'login_url' => route('login'),
            ]));

            $this->notifyAdminsOfStudentAccess(
                name: $fullName !== '' ? $fullName : $firstName,
                email: $email,
                amount: number_format($coursePrice, 2),
                reference: $reference,
                paymentMethod: 'MANUAL_ENROLL',
                dedupeKey: $reference,
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->with('admin_status', 'Student was enrolled successfully, but the credential email could not be sent.')
                ->with('admin_temp_email', $email)
                ->with('admin_temp_password', $temporaryPassword);
        }

        return back()->with('admin_status', 'Student enrolled successfully and the temporary password was emailed.');
    }

    public function approve(Request $request, Order $order): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        if ($order->status !== 'pending') {
            return back()->withErrors([
                'admin' => 'Only pending orders can be approved.',
            ]);
        }

        $user = $order->user;

        if ($user) {
            $user->forceFill([
                'course_slug' => $order->course_slug,
                'purchased_at' => $user->purchased_at ?? now(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            $this->cleanupPendingOrdersFor($user, $order->email, (string) $order->xendit_reference);
        }

        $order->forceFill([
            'status' => 'approved',
            'approved_at' => now(),
            'paid_at' => $order->paid_at ?? now(),
            'source' => 'manual',
        ])->save();

        $this->notifyAdminsOfStudentAccess(
            name: $order->display_name !== '' ? $order->display_name : ($order->email ?: 'Student'),
            email: (string) $order->email,
            amount: number_format((float) $order->amount, 2),
            reference: (string) ($order->xendit_reference ?: ('manual-approval-' . $order->id)),
            paymentMethod: (string) ($order->payment_method ?: 'OFFLINE_GCASH'),
            dedupeKey: 'approved-order-' . $order->id,
        );

        return back()->with('admin_status', 'Pending order approved successfully.');
    }

    public function destroy(Request $request, Order $order): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $order->forceFill([
            'status' => 'deleted',
            'deleted_at' => now(),
            'notes' => 'Removed from the admin pending list. The linked customer account was retained.',
        ])->save();

        return back()->with('admin_status', 'Order removed from the pending list. The customer account was kept in the database.');
    }

    private function ensureAdmin(Request $request): ?RedirectResponse
    {
        if (! (bool) $request->session()->get('admin_authenticated', false)) {
            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'login' => 'Please log in with your admin account first.',
                ]);
        }

        return null;
    }

    private function cleanupResolvedPendingOrders(): void
    {
        $paidStatuses = ['paid', 'approved'];

        $staleUserOrderIds = Order::query()
            ->where('status', 'pending')
            ->whereHas('user', function ($query): void {
                $query->whereNotNull('purchased_at');
            })
            ->pluck('id');

        if ($staleUserOrderIds->isNotEmpty()) {
            Order::query()
                ->whereIn('id', $staleUserOrderIds)
                ->update([
                    'status' => 'deleted',
                    'deleted_at' => now(),
                    'notes' => 'Automatically hidden after the student already received paid access.',
                ]);
        }

        $staleEmailOrderIds = Order::query()
            ->where('status', 'pending')
            ->whereIn('email', function ($query) use ($paidStatuses): void {
                $query->select('email')
                    ->from('orders')
                    ->whereIn('status', $paidStatuses)
                    ->whereNotNull('email');
            })
            ->pluck('id');

        if ($staleEmailOrderIds->isNotEmpty()) {
            Order::query()
                ->whereIn('id', $staleEmailOrderIds)
                ->update([
                    'status' => 'deleted',
                    'deleted_at' => now(),
                    'notes' => 'Automatically hidden after a paid order was detected for the same email.',
                ]);
        }
    }

    private function cleanupPendingOrdersFor(User $user, ?string $email, string $keepReference): void
    {
        Order::query()
            ->where('status', 'pending')
            ->where(function ($query) use ($user, $email): void {
                $query->where('user_id', $user->id);

                if ($email) {
                    $query->orWhere('email', $email);
                }
            })
            ->where(function ($query) use ($keepReference): void {
                $query
                    ->whereNull('xendit_reference')
                    ->orWhere('xendit_reference', '!=', $keepReference);
            })
            ->update([
                'status' => 'deleted',
                'deleted_at' => now(),
                'notes' => 'Automatically hidden after admin manually granted course access.',
            ]);
    }

    private function notifyAdminsOfStudentAccess(
        string $name,
        string $email,
        string $amount,
        string $reference,
        string $paymentMethod,
        string $dedupeKey,
    ): void {
        $adminEmails = collect(config('admin.emails', []))
            ->filter(fn ($adminEmail) => is_string($adminEmail) && $adminEmail !== '')
            ->values();

        if ($adminEmails->isEmpty()) {
            return;
        }

        try {
            Mail::to($adminEmails->all())->send(new AdminStudentAccessNotification([
                'name' => $name,
                'email' => $email,
                'amount' => $amount,
                'reference' => $reference,
                'payment_method' => $paymentMethod,
                'admin_url' => route('admin.login'),
            ]));
        } catch (Throwable $exception) {
            Log::warning('Admin student access notification could not be sent.', [
                'email' => $email,
                'reference' => $reference,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}

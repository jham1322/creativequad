<?php

namespace App\Http\Controllers;

use App\Mail\AdminManualEnrollmentCredentials;
use App\Mail\AdminStudentAccessNotification;
use App\Models\AnalyticsPageView;
use App\Models\AnalyticsVisitor;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Support\XenditCoursePricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
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
            'analytics' => $this->analyticsSummaryData(),
            'displayCoursePrice' => XenditCoursePricing::displayPrice(),
            'paymentCoursePrice' => XenditCoursePricing::paymentPrice(),
            'coupons' => Coupon::query()->latest()->get(),
        ]);
    }

    public function analyticsSummary(Request $request): JsonResponse|RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        return response()->json($this->analyticsSummaryData());
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
        $coursePrice = XenditCoursePricing::displayPrice();

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

    public function updatePaymentPrice(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $validated = $request->validate([
            'payment_price' => ['required', 'numeric', 'min:1', 'max:999999'],
        ]);

        $amount = XenditCoursePricing::setPaymentPrice((float) $validated['payment_price']);

        return back()->with(
            'admin_status',
            'Xendit payment test price updated to ₱' . number_format($amount, 2) . '. Public landing and checkout display prices were left unchanged.'
        );
    }

    public function storeCoupon(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60'],
            'discount_amount' => ['required', 'numeric', 'min:1', 'max:999999'],
        ]);

        $code = Coupon::normalizeCode($validated['code']);

        if (Coupon::query()->where('code', $code)->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'coupon' => 'That coupon code already exists.',
                ]);
        }

        Coupon::query()->create([
            'code' => $code,
            'discount_amount' => round((float) $validated['discount_amount'], 2),
            'is_active' => true,
        ]);

        return back()->with('admin_status', 'Coupon ' . $code . ' created successfully.');
    }

    public function destroyCoupon(Request $request, Coupon $coupon): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $code = $coupon->code;
        $coupon->delete();

        return back()->with('admin_status', 'Coupon ' . $code . ' removed.');
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

    private function analyticsSummaryData(): array
    {
        if (! Schema::hasTable('analytics_visitors') || ! Schema::hasTable('analytics_page_views')) {
            return [
                'live_visitors' => 0,
                'unique_visitors_today' => 0,
                'page_views_today' => 0,
                'unique_visitors_7d' => 0,
                'total_unique_visitors' => 0,
                'top_pages' => [],
                'recent_activity' => [],
                'updated_at' => now()->toIso8601String(),
                'updated_at_label' => 'Just now',
            ];
        }

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $weekStart = $now->copy()->subDays(6)->startOfDay();
        $liveThreshold = $now->copy()->subMinutes(5);

        $topPages = AnalyticsPageView::query()
            ->select('path', 'route_name')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT visitor_key) as unique_visitors')
            ->where('viewed_at', '>=', $weekStart)
            ->groupBy('path', 'route_name')
            ->orderByDesc('views')
            ->limit(5)
            ->get()
            ->map(function (AnalyticsPageView $pageView): array {
                return [
                    'label' => $this->formatAnalyticsPageLabel($pageView->path, $pageView->route_name),
                    'path' => $pageView->path,
                    'views' => (int) $pageView->views,
                    'unique_visitors' => (int) $pageView->unique_visitors,
                ];
            })
            ->values()
            ->all();

        $recentActivity = AnalyticsPageView::query()
            ->latest('viewed_at')
            ->limit(8)
            ->get()
            ->map(function (AnalyticsPageView $pageView) use ($now): array {
                return [
                    'label' => $this->formatAnalyticsPageLabel($pageView->path, $pageView->route_name),
                    'path' => $pageView->path,
                    'viewed_at' => optional($pageView->viewed_at)->toIso8601String(),
                    'viewed_at_label' => optional($pageView->viewed_at)?->diffForHumans($now, short: true) ?? 'Just now',
                ];
            })
            ->values()
            ->all();

        return [
            'live_visitors' => AnalyticsVisitor::query()->where('last_seen_at', '>=', $liveThreshold)->count(),
            'unique_visitors_today' => AnalyticsPageView::query()->where('viewed_at', '>=', $todayStart)->distinct('visitor_key')->count('visitor_key'),
            'page_views_today' => AnalyticsPageView::query()->where('viewed_at', '>=', $todayStart)->count(),
            'unique_visitors_7d' => AnalyticsPageView::query()->where('viewed_at', '>=', $weekStart)->distinct('visitor_key')->count('visitor_key'),
            'total_unique_visitors' => AnalyticsVisitor::query()->count(),
            'top_pages' => $topPages,
            'recent_activity' => $recentActivity,
            'updated_at' => $now->toIso8601String(),
            'updated_at_label' => 'Updated ' . $now->diffForHumans(),
        ];
    }

    private function formatAnalyticsPageLabel(?string $path, ?string $routeName): string
    {
        return match (true) {
            $routeName === 'checkout' => 'Checkout page',
            $routeName === 'login' => 'Student login',
            $routeName === 'password.request' => 'Forgot password',
            $routeName === 'lms.dashboard' => 'LMS dashboard',
            $path === '/' => 'Homepage',
            $path === '/checkout/success' => 'Checkout success',
            $path === '/checkout/failed' => 'Checkout failed',
            default => $path ?: 'Unknown page',
        };
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

<?php

namespace App\Http\Controllers;

use App\Mail\AdminStudentAccessNotification;
use App\Mail\CoursePaymentConfirmed;
use App\Mail\CoursePaymentPending;
use App\Models\Coupon;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\User;
use App\Support\XenditCoursePricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CheckoutController extends Controller
{
    public function show(Request $request): View|\Symfony\Component\HttpFoundation\Response
    {
        if (Auth::check()) {
            return redirect()
                ->route('lms.dashboard')
                ->with('pending_payment_notice', 'Please complete your pending payment to unlock your course access.');
        }

        $basePrice = XenditCoursePricing::paymentPrice();
        $couponCode = $request->string('coupon_code')->value();
        [$coupon, $discountAmount, $finalPrice, $couponError] = $this->resolveCouponPricing($couponCode, $basePrice);

        return view('checkout', [
            'coursePrice' => number_format($basePrice, 2),
            'finalCoursePrice' => number_format($finalPrice, 2),
            'appliedCoupon' => $coupon,
            'couponCode' => $couponCode,
            'couponDiscount' => number_format($discountAmount, 2),
            'couponError' => $couponError,
            'offlineGcashDetails' => $this->offlineGcashDetails(),
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
            'payment_method' => ['required', 'string', 'in:maya,grabpay,qrph,offline_gcash'],
            'coupon_code' => ['nullable', 'string', 'max:60'],
        ], [
            'email.unique' => 'Email already exists.',
            'username.unique' => 'Username already exists.',
        ]);

        $basePrice = XenditCoursePricing::paymentPrice();
        [$coupon, $discountAmount, $price, $couponError] = $this->resolveCouponPricing((string) ($validated['coupon_code'] ?? ''), $basePrice);

        if ($couponError !== null) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'coupon_code' => $couponError,
                ]);
        }

        $externalId = $validated['payment_method'] === 'offline_gcash'
            ? 'offline-gcash-' . Str::uuid()
            : 'vibe-course-' . Str::uuid();
        $invoiceUrl = null;

        if ($validated['payment_method'] !== 'offline_gcash') {
            $secretKey = (string) config('services.xendit.secret_key');

            if ($secretKey === '') {
                return back()
                    ->withInput($request->except('password'))
                    ->withErrors([
                        'checkout' => 'Xendit is not configured yet. Add your secret key first.',
                    ]);
            }

            $response = $this->createXenditInvoice([
                'external_id' => $externalId,
                'amount' => $price,
                'payment_method' => $validated['payment_method'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'coupon_code' => $coupon?->code,
                'discount_amount' => $discountAmount,
            ]);

            if ($response->failed() || ! $response->json('invoice_url')) {
                report('Xendit checkout creation failed: ' . $response->body());

                return back()
                    ->withInput($request->except('password'))
                    ->withErrors([
                        'checkout' => 'We could not create your checkout session right now. Please try again in a moment.',
                    ]);
            }

            $invoiceUrl = (string) $response->json('invoice_url');
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
            'coupon_id' => $coupon?->id,
            'course_slug' => 'build-real-full-stack-web-apps-using-ai-and-codex',
            'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
            'amount' => $price,
            'coupon_code' => $coupon?->code,
            'discount_amount' => $discountAmount,
            'currency' => 'PHP',
            'status' => 'pending',
            'payment_method' => strtoupper($validated['payment_method']),
            'xendit_reference' => $externalId,
            'invoice_url' => $invoiceUrl,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'source' => $validated['payment_method'] === 'offline_gcash' ? 'offline_gcash' : 'xendit',
            'notes' => $validated['payment_method'] === 'offline_gcash'
                ? 'Waiting for offline GCash confirmation from admin.'
                : null,
        ]);

        Cache::store('file')->put('xendit-order:' . $externalId, [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'username' => $validated['username'],
            'payment_method' => strtoupper($validated['payment_method']),
            'coupon_code' => $coupon?->code,
            'discount_amount' => $discountAmount,
        ], now()->addDays(30));

        if ($validated['payment_method'] !== 'offline_gcash') {
            try {
                Mail::to($validated['email'])->send(new CoursePaymentPending([
                    'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                    'amount' => number_format($price, 2),
                    'reference' => $externalId,
                    'payment_method' => strtoupper($validated['payment_method']),
                    'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
                    'payment_url' => $invoiceUrl,
                ]));
            } catch (\Throwable $exception) {
                Log::warning('Pending checkout email could not be sent.', [
                    'external_id' => $externalId,
                    'email' => $validated['email'],
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($validated['payment_method'] === 'offline_gcash') {
            return redirect()
                ->route('lms.dashboard')
                ->with('pending_payment_notice', 'Your offline GCash payment request has been created. Please send your payment to the masked account below and wait for admin approval.');
        }

        return redirect()->away($invoiceUrl);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $reference = $request->string('reference')->value();

        if (! Auth::check() && $reference !== '') {
            $user = User::query()
                ->where('xendit_reference', $reference)
                ->orWhereHas('orders', function ($query) use ($reference): void {
                    $query->where('xendit_reference', $reference);
                })
                ->first();

            if ($user instanceof User) {
                Auth::login($user);
                $request->session()->regenerate();
            }
        }

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

        $confirmedOrder = $reference !== ''
            ? $this->resolveConfirmedOrderForReference($reference)
            : null;

        $shouldTrackPurchase = $confirmedOrder instanceof Order
            && $reference !== ''
            && $this->consumePurchaseTrackingReadyFlag($reference);

        $purchaseValue = $confirmedOrder instanceof Order
            ? (float) $confirmedOrder->amount
            : XenditCoursePricing::paymentPrice();

        $request->session()->put('lms_access_granted', true);
        $request->session()->put('lms_access_reference', $reference);

        $redirectUrl = URL::temporarySignedRoute('lms.dashboard', now()->addHours(2), [
            'reference' => $reference,
            'enrolled' => 1,
        ]);

        return view('checkout-success', [
            'reference' => $reference,
            'redirectUrl' => $redirectUrl,
            'redirectDelayMs' => 3000,
            'shouldTrackPurchase' => $shouldTrackPurchase,
            'purchaseValue' => $purchaseValue,
            'purchaseCurrency' => $confirmedOrder?->currency ?: 'PHP',
        ]);
    }

    public function legacySuccessRedirect(Request $request): RedirectResponse
    {
        $reference = $request->string('reference')->value();

        return redirect()->route('checkout.thankyou', [
            'reference' => $reference,
        ]);
    }

    public function failed(Request $request): RedirectResponse
    {
        $reference = $request->string('reference')->value();

        if (! Auth::check() && $reference !== '') {
            $user = User::query()
                ->where('xendit_reference', $reference)
                ->orWhereHas('orders', function ($query) use ($reference): void {
                    $query->where('xendit_reference', $reference);
                })
                ->first();

            if ($user instanceof User) {
                Auth::login($user);
                $request->session()->regenerate();
            }
        }

        if (Auth::check()) {
            return redirect()
                ->route('lms.dashboard')
                ->with('pending_payment_notice', 'Your payment is still pending. Please complete your payment to unlock the full course dashboard.');
        }

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
            'paymentMethodOptions' => $this->paymentMethodOptions(),
            'offlineGcashDetails' => $this->offlineGcashDetails(),
            'lessons' => $this->resolveLessons(),
        ]);
    }

    public function resourcePrompt(Request $request): BinaryFileResponse|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Please log in to access your course resources.',
                ]);
        }

        $path = resource_path('prompts/laravel-shared-hosting-setup-prompt.txt');

        if (! File::exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="laravel-shared-hosting-setup-prompt.txt"',
        ]);
    }

    public function retryPendingPayment(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Please log in to continue your payment.',
                ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->purchased_at !== null) {
            return redirect()
                ->route('lms.dashboard')
                ->with('pending_payment_notice', 'Your payment is already complete and your course access is active.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:maya,grabpay,qrph,offline_gcash'],
        ]);

        $pendingOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $pendingOrder instanceof Order) {
            return redirect()
                ->route('checkout')
                ->withErrors([
                    'checkout' => 'No pending payment was found for your account. Please start a new checkout.',
                ]);
        }

        $externalId = $validated['payment_method'] === 'offline_gcash'
            ? 'offline-gcash-' . Str::uuid()
            : 'vibe-course-' . Str::uuid();
        $invoiceUrl = null;

        if ($validated['payment_method'] !== 'offline_gcash') {
            $response = $this->createXenditInvoice([
                'external_id' => $externalId,
                'amount' => (float) $pendingOrder->amount,
                'payment_method' => $validated['payment_method'],
                'first_name' => $pendingOrder->first_name ?: ($user->first_name ?: $user->name),
                'last_name' => $pendingOrder->last_name ?: $user->last_name,
                'email' => $pendingOrder->email ?: $user->email,
                'username' => $pendingOrder->username ?: $user->username,
            ]);

            if ($response->failed() || ! $response->json('invoice_url')) {
                report('Xendit pending payment retry failed: ' . $response->body());

                return redirect()
                    ->route('lms.dashboard')
                    ->with('pending_payment_notice', 'We could not switch your payment method right now. Please try again in a moment.');
            }

            $invoiceUrl = (string) $response->json('invoice_url');
        }

        $newOrder = Order::query()->create([
            'user_id' => $user->id,
            'course_slug' => $pendingOrder->course_slug,
            'course_name' => $pendingOrder->course_name,
            'amount' => $pendingOrder->amount,
            'currency' => $pendingOrder->currency,
            'status' => 'pending',
            'payment_method' => strtoupper($validated['payment_method']),
            'xendit_reference' => $externalId,
            'invoice_url' => $invoiceUrl,
            'first_name' => $pendingOrder->first_name ?: $user->first_name,
            'last_name' => $pendingOrder->last_name ?: $user->last_name,
            'email' => $pendingOrder->email ?: $user->email,
            'username' => $pendingOrder->username ?: $user->username,
            'source' => $validated['payment_method'] === 'offline_gcash' ? 'offline_gcash' : 'xendit',
            'notes' => $validated['payment_method'] === 'offline_gcash'
                ? 'Offline GCash retry created from dashboard pending-payment flow.'
                : 'Retry invoice created from dashboard pending-payment flow.',
        ]);

        $user->forceFill([
            'xendit_reference' => $externalId,
        ])->save();

        Cache::store('file')->put('xendit-order:' . $externalId, [
            'order_id' => $newOrder->id,
            'user_id' => $user->id,
            'name' => trim(($pendingOrder->first_name ?: $user->first_name ?: $user->name) . ' ' . ($pendingOrder->last_name ?: $user->last_name)),
            'email' => $pendingOrder->email ?: $user->email,
            'username' => $pendingOrder->username ?: $user->username,
            'payment_method' => strtoupper($validated['payment_method']),
        ], now()->addDays(30));

        if ($validated['payment_method'] !== 'offline_gcash') {
            try {
                Mail::to($pendingOrder->email ?: $user->email)->send(new CoursePaymentPending([
                    'name' => trim(($pendingOrder->first_name ?: $user->first_name ?: $user->name) . ' ' . ($pendingOrder->last_name ?: $user->last_name)),
                    'amount' => number_format((float) $pendingOrder->amount, 2),
                    'reference' => $externalId,
                    'payment_method' => strtoupper($validated['payment_method']),
                    'course_name' => $pendingOrder->course_name ?: 'Build Real Full-Stack Web Apps using AI and Codex',
                    'payment_url' => $invoiceUrl,
                ]));
            } catch (\Throwable $exception) {
                Log::warning('Pending retry checkout email could not be sent.', [
                    'external_id' => $externalId,
                    'email' => $pendingOrder->email ?: $user->email,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        if ($validated['payment_method'] === 'offline_gcash') {
            return redirect()
                ->route('lms.dashboard')
                ->with('pending_payment_notice', 'Your payment method has been changed to offline GCash. Please send payment to the masked account below and wait for admin approval.');
        }

        return redirect()->away($invoiceUrl);
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

    private function paymentMethodOptions(): array
    {
        return [
            'qrph' => [
                'title' => 'GCash QR Payment',
                'logo' => 'QRPh',
                'logoClass' => 'checkout-method-logo-qrph',
                'description' => 'Pay through QRPH. It will also accept GCash, Maya, and other QRPh-compatible banking or e-wallet apps.',
                'recommended' => true,
            ],
            'maya' => [
                'title' => 'PayMaya',
                'logo' => 'maya',
                'logoClass' => 'checkout-method-logo-maya',
                'description' => 'Pay using your PayMaya wallet through Xendit checkout.',
            ],
            'grabpay' => [
                'title' => 'GrabPay',
                'logo' => 'Grab',
                'logoClass' => 'checkout-method-logo-grabpay',
                'description' => 'Pay directly with GrabPay without showing other unrelated payment channels.',
            ],
            'offline_gcash' => [
                'title' => 'Offline GCash',
                'logo' => 'Manual',
                'logoClass' => 'checkout-method-logo-gcash',
                'description' => 'Send payment to the masked GCash account below, then wait for admin review and approval.',
            ],
        ];
    }

    private function createXenditInvoice(array $payload)
    {
        $secretKey = (string) config('services.xendit.secret_key');

        return Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->post(rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/') . '/v2/invoices', [
                'external_id' => $payload['external_id'],
                'amount' => $payload['amount'],
                'currency' => 'PHP',
                'description' => 'Build Real Full Stack Web Apps using AI and Codex',
                'invoice_duration' => 86400,
                'success_redirect_url' => route('checkout.thankyou', ['reference' => $payload['external_id']]),
                'failure_redirect_url' => route('checkout.failed', ['reference' => $payload['external_id']]),
                'customer' => [
                    'given_names' => $payload['first_name'],
                    'surname' => $payload['last_name'],
                    'email' => $payload['email'],
                ],
                'items' => [
                    [
                        'name' => 'Build Real Full Stack Web Apps using AI and Codex',
                        'quantity' => 1,
                        'price' => $payload['amount'],
                        'category' => 'Course',
                        'url' => url('/'),
                    ],
                ],
                'payment_methods' => $this->paymentMethodMap()[$payload['payment_method']],
                'metadata' => [
                    'username' => $payload['username'],
                    'payment_method' => $payload['payment_method'],
                    'coupon_code' => $payload['coupon_code'] ?? null,
                    'discount_amount' => $payload['discount_amount'] ?? 0,
                ],
            ]);
    }

    private function resolveCouponPricing(string $code, float $basePrice): array
    {
        $normalizedCode = Coupon::normalizeCode($code);

        if ($normalizedCode === '') {
            return [null, 0.00, $basePrice, null];
        }

        $coupon = Coupon::query()
            ->where('code', $normalizedCode)
            ->where('is_active', true)
            ->first();

        if (! $coupon instanceof Coupon) {
            return [null, 0.00, $basePrice, 'Coupon code is invalid or inactive.'];
        }

        $discountAmount = $coupon->discountFor($basePrice);

        return [$coupon, $discountAmount, max(1, round($basePrice - $discountAmount, 2)), null];
    }

    private function paymentMethodMap(): array
    {
        return [
            'maya' => ['PAYMAYA'],
            'grabpay' => ['GRABPAY'],
            'qrph' => ['QRPH'],
        ];
    }

    private function offlineGcashDetails(): array
    {
        return [
            'name' => 'R*****l J*****r',
            'number' => '0930-414-2218',
        ];
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

        $this->markPurchaseTrackingReady($externalId);

        if ($user instanceof User) {
            $this->cleanupResolvedPendingOrders($user, $externalId);
        } elseif ($resolvedEmail !== '') {
            $this->cleanupResolvedPendingOrdersByEmail($resolvedEmail, $externalId);
        }

        $sentKey = 'xendit-course-paid-mail:' . $externalId;

        if (! Cache::store('file')->add($sentKey, true, now()->addDays(30))) {
            return;
        }

        Mail::to($resolvedEmail)->send(new CoursePaymentConfirmed([
            'name' => (string) ($order['name'] ?? strtok($resolvedEmail, '@') ?: 'Student'),
            'email' => $resolvedEmail,
            'amount' => number_format((float) ($invoice['paid_amount'] ?? $invoice['amount'] ?? XenditCoursePricing::paymentPrice()), 2),
            'reference' => $externalId,
            'payment_method' => (string) ($order['payment_method'] ?? strtoupper((string) ($invoice['payment_method'] ?? 'Xendit'))),
            'course_name' => 'Build Real Full-Stack Web Apps using AI and Codex',
            'dashboard_url' => route('login'),
        ]));

        $this->notifyAdminsOfStudentAccess(
            name: (string) ($order['name'] ?? strtok($resolvedEmail, '@') ?: 'Student'),
            email: $resolvedEmail,
            amount: number_format((float) ($invoice['paid_amount'] ?? $invoice['amount'] ?? XenditCoursePricing::paymentPrice()), 2),
            reference: $externalId,
            paymentMethod: (string) ($order['payment_method'] ?? strtoupper((string) ($invoice['payment_method'] ?? 'Xendit'))),
            dedupeKey: $externalId,
        );
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

        $cacheKey = 'admin-student-access-mail:' . $dedupeKey;

        if (! Cache::store('file')->add($cacheKey, true, now()->addDays(30))) {
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
        } catch (\Throwable $exception) {
            Log::warning('Admin student access notification could not be sent.', [
                'email' => $email,
                'reference' => $reference,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function cleanupResolvedPendingOrders(User $user, string $keepReference): void
    {
        Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('xendit_reference', '!=', $keepReference)
            ->update([
                'status' => 'deleted',
                'deleted_at' => now(),
                'notes' => 'Automatically cleaned up after payment was completed on another checkout.',
            ]);
    }

    private function cleanupResolvedPendingOrdersByEmail(string $email, string $keepReference): void
    {
        Order::query()
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('xendit_reference', '!=', $keepReference)
            ->update([
                'status' => 'deleted',
                'deleted_at' => now(),
                'notes' => 'Automatically cleaned up after payment was completed on another checkout.',
            ]);
    }

    private function resolveConfirmedOrderForReference(string $reference): ?Order
    {
        return Order::query()
            ->where('xendit_reference', $reference)
            ->whereIn('status', ['paid', 'approved'])
            ->where(function ($query): void {
                $query->whereNotNull('paid_at')
                    ->orWhereNotNull('approved_at');
            })
            ->latest()
            ->first();
    }

    private function markPurchaseTrackingReady(string $reference): void
    {
        Cache::store('file')->put(
            $this->purchaseTrackingCacheKey($reference),
            true,
            now()->addDay(),
        );
    }

    private function consumePurchaseTrackingReadyFlag(string $reference): bool
    {
        return (bool) Cache::store('file')->pull(
            $this->purchaseTrackingCacheKey($reference),
            false,
        );
    }

    private function purchaseTrackingCacheKey(string $reference): string
    {
        return 'meta-purchase-track-ready:' . $reference;
    }
}

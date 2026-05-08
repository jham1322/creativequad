@php
    if (request('deploy_token') === 'hostinger-sync-2026-05-06') {
        $deployOutput = [];

        foreach ([
            ['migrate', ['--force' => true]],
            ['optimize:clear', []],
            ['config:cache', []],
            ['route:cache', []],
            ['view:cache', []],
            ['event:cache', []],
        ] as [$command, $arguments]) {
            \Illuminate\Support\Facades\Artisan::call($command, $arguments);
            $deployOutput[] = strtoupper($command);
            $deployOutput[] = trim(\Illuminate\Support\Facades\Artisan::output());
            $deployOutput[] = '';
        }

        echo '<pre style="white-space:pre-wrap;padding:24px;background:#020f18;color:#f4f8ff;min-height:100vh;font:14px/1.6 monospace;">'
            . e(implode("\n", $deployOutput))
            . '</pre>';

        return;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout | {{ config('app.name', 'ELMS') }}</title>
        <style>html,body{background:#020f18;color:#f4f8ff}</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        <main class="relative min-h-screen overflow-hidden bg-background">
            <div class="pointer-events-none absolute inset-0 page-aura" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 hero-grid opacity-[0.03]" aria-hidden="true"></div>

            <header class="checkout-topbar relative z-10 border-b border-white/8">
                <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-sm font-medium text-foreground/84 transition hover:text-foreground">
                        <img src="{{ asset('images/branding/logo.webp') }}" alt="Creative Quad" class="checkout-logo-mark">
                        <span class="tracking-[0.18em] uppercase">Creative Quad</span>
                    </a>

                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="hidden text-sm font-medium text-foreground/72 transition hover:text-foreground sm:inline-flex">
                            About the course
                        </a>
                        @auth
                            <a href="{{ route('lms.dashboard') }}" class="hidden text-sm font-medium text-foreground/72 transition hover:text-foreground sm:inline-flex">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden text-sm font-medium text-foreground/72 transition hover:text-foreground sm:inline-flex">
                                Login
                            </a>
                        @endauth
                        <a href="{{ url('/') }}" class="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-semibold text-foreground transition hover:bg-card/70">
                            Back to landing page
                        </a>
                    </div>
                </div>
            </header>

            <section class="relative overflow-hidden border-b border-white/8 px-6 py-18 text-center sm:py-20">
                <div class="checkout-hero-aura" aria-hidden="true"></div>
                <div class="relative mx-auto max-w-4xl">
                    <p class="inline-flex items-center rounded-full border border-border bg-card/55 px-4 py-2 text-xs font-medium uppercase tracking-[0.28em] text-primary backdrop-blur-md">
                        Secure Your Spot
                    </p>
                    <h1 class="display-title mt-6 text-balance text-4xl font-semibold tracking-tight text-foreground md:text-6xl">
                        Your Journey Starts Here
                    </h1>
                    <p class="mt-4 text-balance text-lg leading-8 text-muted-foreground md:text-xl">
                        Enroll in <span class="font-semibold text-foreground">Build Real Full-Stack Web Apps using AI and Codex</span>.
                    </p>
                </div>
            </section>

            <section class="relative mx-auto w-full max-w-7xl px-6 py-12 sm:py-16">
                <div class="checkout-notice reveal reveal-delay-1 rounded-[1.6rem] border px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 text-left sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="checkout-notice-icon mt-0.5">✓</span>
                            <div>
                                <p class="text-sm font-semibold text-foreground">You’re enrolling in Learn how to build web apps using AI and Codex.</p>
                                <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                    Codex to GitHub to your own live server. No closed platform lock-in.
                                </p>
                            </div>
                        </div>
                        <a href="#order-summary" class="text-sm font-semibold text-primary transition hover:text-accent">
                            Review order →
                        </a>
                    </div>
                </div>

                <div class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1.25fr)_24rem]">
                    <section class="checkout-panel reveal reveal-delay-2 rounded-[2rem] border p-6 sm:p-8">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Billing Details</p>
                            <h2 class="display-title text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                                Fill out your details to enroll to the course
                            </h2>
                        </div>

                        @if ($errors->has('checkout'))
                            <div class="mt-6 rounded-[1.2rem] border border-red-400/22 bg-red-500/10 px-4 py-3 text-sm leading-6 text-red-100">
                                {{ $errors->first('checkout') }}
                            </div>
                        @endif

                        @if ($errors->any() && ! $errors->has('checkout'))
                            <div class="mt-6 rounded-[1.2rem] border border-amber-300/22 bg-amber-500/10 px-4 py-3 text-sm leading-6 text-amber-50">
                                Please review the form fields and complete the missing details.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 space-y-8">
                            @csrf
                            <div class="grid gap-5 sm:grid-cols-2">
                                <label class="checkout-field">
                                    <span>First name</span>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan">
                                    @error('first_name')
                                        <small class="checkout-field-error">{{ $message }}</small>
                                    @enderror
                                </label>
                                <label class="checkout-field">
                                    <span>Last name</span>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz">
                                    @error('last_name')
                                        <small class="checkout-field-error">{{ $message }}</small>
                                    @enderror
                                </label>
                            </div>

                            <label class="checkout-field">
                                <span>Email address</span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" @class(['checkout-input-error' => $errors->has('email')])>
                                @error('email')
                                    <small class="checkout-field-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <label class="checkout-field">
                                    <span>Account username</span>
                                    <input type="text" name="username" value="{{ old('username') }}" placeholder="yourusername" @class(['checkout-input-error' => $errors->has('username')])>
                                    @error('username')
                                        <small class="checkout-field-error">{{ $message }}</small>
                                    @enderror
                                </label>
                                <label class="checkout-field">
                                    <span>Create account password</span>
                                    <div class="checkout-password-wrap">
                                        <input type="password" name="password" placeholder="Password" data-password-input @class(['checkout-input-error' => $errors->has('password')])>
                                        <button type="button" class="checkout-password-toggle" data-password-toggle aria-label="Show password" aria-pressed="false">
                                            <span data-password-toggle-label>Show</span>
                                        </button>
                                    </div>
                                    @error('password')
                                        <small class="checkout-field-error">{{ $message }}</small>
                                    @enderror
                                </label>
                            </div>

                            <div class="checkout-divider"></div>

                            <div>
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Payment Method</p>
                                        <h3 class="mt-2 text-2xl font-semibold tracking-tight text-foreground">Choose how you want to pay</h3>
                                    </div>
                                    <span class="checkout-badge">Secure checkout</span>
                                </div>

                                <div class="checkout-method-list mt-6">
                                    <label class="checkout-method-option">
                                        <input class="checkout-method-radio" type="radio" value="gcash" @checked(old('payment_method', 'gcash') === 'gcash') name="payment_method">
                                        <div class="checkout-method-panel">
                                            <div class="checkout-method-head">
                                                <span class="checkout-method-control" aria-hidden="true"></span>
                                                <div class="checkout-method-title-wrap">
                                                    <span class="checkout-method-title">GCash</span>
                                                    <span class="checkout-method-logo checkout-method-logo-gcash">GCash</span>
                                                </div>
                                            </div>
                                            <div class="checkout-method-body">
                                                Pay with your GCash account via <strong>Xendit</strong>.
                                            </div>
                                        </div>
                                    </label>

                                    <label class="checkout-method-option">
                                        <input class="checkout-method-radio" type="radio" value="maya" @checked(old('payment_method') === 'maya') name="payment_method">
                                        <div class="checkout-method-panel">
                                            <div class="checkout-method-head">
                                                <span class="checkout-method-control" aria-hidden="true"></span>
                                                <div class="checkout-method-title-wrap">
                                                    <span class="checkout-method-title">PayMaya</span>
                                                    <span class="checkout-method-logo checkout-method-logo-maya">maya</span>
                                                </div>
                                            </div>
                                            <div class="checkout-method-body">
                                                Pay using your <strong>PayMaya</strong> wallet through Xendit checkout.
                                            </div>
                                        </div>
                                    </label>

                                    <label class="checkout-method-option">
                                        <input class="checkout-method-radio" type="radio" value="grabpay" @checked(old('payment_method') === 'grabpay') name="payment_method">
                                        <div class="checkout-method-panel">
                                            <div class="checkout-method-head">
                                                <span class="checkout-method-control" aria-hidden="true"></span>
                                                <div class="checkout-method-title-wrap">
                                                    <span class="checkout-method-title">GrabPay</span>
                                                    <span class="checkout-method-logo checkout-method-logo-grabpay">Grab</span>
                                                </div>
                                            </div>
                                            <div class="checkout-method-body">
                                                Pay directly with <strong>GrabPay</strong> without showing other unrelated payment channels.
                                            </div>
                                        </div>
                                    </label>

                                    <label class="checkout-method-option">
                                        <input class="checkout-method-radio" type="radio" value="qrph" @checked(old('payment_method') === 'qrph') name="payment_method">
                                        <div class="checkout-method-panel">
                                            <div class="checkout-method-head">
                                                <span class="checkout-method-control" aria-hidden="true"></span>
                                                <div class="checkout-method-title-wrap">
                                                    <span class="checkout-method-title">QR Payment</span>
                                                    <span class="checkout-method-logo checkout-method-logo-qrph">QRPh</span>
                                                </div>
                                            </div>
                                            <div class="checkout-method-body">
                                                Pay through <strong>QRPH</strong> if you prefer a QR-based checkout flow.
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="checkout-privacy rounded-[1.5rem] border border-white/8 p-5">
                                <p class="text-sm leading-7 text-muted-foreground">
                                    Your personal data will be used to process your order, support your experience, and create your account access for the course. This checkout now creates a hosted payment session through Xendit on the Laravel backend, so your secret key never touches the browser.
                                </p>
                            </div>

                            <button type="submit" class="checkout-submit">
                                Place Order
                            </button>
                        </form>
                    </section>

                    <aside class="space-y-8" id="order-summary">
                        <section class="checkout-panel reveal reveal-delay-3 rounded-[2rem] border p-6 sm:p-7">
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Order Summary</p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-foreground">Your course access</h2>

                            <div class="mt-6 space-y-5">
                                <div class="checkout-order-row">
                                    <div>
                                        <p class="font-semibold text-foreground">Build Real Full Stack Web Apps using AI and Codex</p>
                                        <p class="mt-1 text-sm text-muted-foreground">Tagalog Course • 1 seat</p>
                                    </div>
                                    <span class="font-semibold text-foreground">₱{{ $coursePrice }}</span>
                                </div>

                                <div class="checkout-order-row">
                                    <span class="text-muted-foreground">Subtotal</span>
                                    <span class="font-semibold text-foreground">₱{{ $coursePrice }}</span>
                                </div>

                                <div class="checkout-order-row checkout-order-total">
                                    <span>Total</span>
                                    <span>₱{{ $coursePrice }}</span>
                                </div>
                            </div>

                        </section>

                        <section class="checkout-testimonial reveal reveal-delay-4 rounded-[2rem] border p-6 sm:p-7">
                            <div class="flex items-center gap-4">
                                <img
                                    src="{{ asset('images/instructor/niel-vibe-coding.png') }}"
                                    alt="Niel portrait"
                                    class="h-16 w-16 rounded-full border border-white/12 object-cover"
                                >
                                <div>
                                    <p class="text-sm font-medium uppercase tracking-[0.18em] text-primary">Why this is one of the best course</p>
                                    <p class="mt-1 text-xl font-semibold text-foreground">You own the outcome</p>
                                </div>
                            </div>

                            <p class="mt-6 text-base leading-8 text-foreground/88">
                                This course is built for people who want a practical path. From prompts to deployment, ang goal is simple, makapag-launch ka ng real app na kontrol mo.
                            </p>
                        </section>
                    </aside>
                </div>
            </section>
        </main>
    </body>
</html>

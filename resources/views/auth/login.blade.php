<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | {{ config('app.name', 'ELMS') }}</title>
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

                    <a href="{{ url('/') }}" class="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-semibold text-foreground transition hover:bg-card/70">
                        Back to landing page
                    </a>
                </div>
            </header>

            <section class="relative mx-auto flex min-h-[calc(100vh-88px)] max-w-7xl items-center px-6 py-16">
                <div class="grid w-full gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                    <div class="max-w-2xl">
                        <p class="text-sm font-medium uppercase tracking-[0.28em] text-primary">Member Login</p>
                        <h1 class="display-title mt-5 text-balance text-4xl font-semibold tracking-tight text-foreground md:text-6xl">
                            Access your purchased course dashboard
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-muted-foreground">
                            Log in using the email and password you used during checkout. Only paid students can open the LMS dashboard and continue the course.
                        </p>
                    </div>

                    <section class="checkout-panel rounded-[2rem] border p-6 sm:p-8">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Student Access</p>
                            <h2 class="display-title text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                                Login to continue learning
                            </h2>
                        </div>

                        @if ($errors->has('login'))
                            <div class="mt-6 rounded-[1.2rem] border border-red-400/22 bg-red-500/10 px-4 py-3 text-sm leading-6 text-red-100">
                                {{ $errors->first('login') }}
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="mt-6 rounded-[1.2rem] border border-emerald-400/22 bg-emerald-500/10 px-4 py-3 text-sm leading-6 text-emerald-100">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-6">
                            @csrf
                            <label class="checkout-field">
                                <span>Email address</span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                            </label>

                            <label class="checkout-field">
                                <span>Password</span>
                                <input type="password" name="password" placeholder="Password" required>
                            </label>

                            <label class="inline-flex items-center gap-3 text-sm text-muted-foreground">
                                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-white/15 bg-background/80 text-primary focus:ring-primary/40">
                                <span>Keep me logged in</span>
                            </label>

                            <a href="{{ route('password.request') }}" class="inline-flex text-sm font-medium text-primary transition hover:text-accent">
                                Already paid but can’t log in? Reset your password
                            </a>

                            <button type="submit" class="checkout-submit">
                                Login
                            </button>
                        </form>

                        <div class="mt-6 border-t border-white/8 pt-5 text-sm leading-7 text-muted-foreground">
                            Doesn’t have an account yet?
                            <a href="{{ route('checkout') }}" class="font-semibold text-primary transition hover:text-accent">
                                Buy the course now
                            </a>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </body>
</html>

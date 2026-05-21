<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.meta-head', [
            'title' => 'Reset Password | Creative Quad Vibe Coding Course',
            'description' => 'Reset your Creative Quad Vibe Coding Course account password.',
            'robots' => 'noindex,nofollow',
            'canonical' => route('password.request'),
        ])
        <style>html,body{background:#020f18;color:#f4f8ff}</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        <main class="relative min-h-screen overflow-hidden bg-background">
            <div class="pointer-events-none absolute inset-0 page-aura" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 hero-grid opacity-[0.03]" aria-hidden="true"></div>

            <section class="relative mx-auto flex min-h-screen max-w-7xl items-center px-6 py-16">
                <div class="mx-auto w-full max-w-2xl">
                    <section class="checkout-panel rounded-[2rem] border p-6 sm:p-8">
                        <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Password Recovery</p>
                        <h1 class="display-title mt-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                            Reset your course account password
                        </h1>
                        <p class="mt-4 text-base leading-7 text-muted-foreground">
                            Enter the email you used during checkout. If that paid student account exists, we’ll send you a reset link.
                        </p>

                        @if (session('status'))
                            <div class="mt-6 rounded-[1.2rem] border border-emerald-400/22 bg-emerald-500/10 px-4 py-3 text-sm leading-6 text-emerald-100">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mt-6 rounded-[1.2rem] border border-red-400/22 bg-red-500/10 px-4 py-3 text-sm leading-6 text-red-100">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
                            @csrf
                            <label class="checkout-field">
                                <span>Email address</span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                            </label>

                            <button type="submit" class="checkout-submit">
                                Send reset link
                            </button>
                        </form>

                        <div class="mt-6 border-t border-white/8 pt-5 text-sm leading-7 text-muted-foreground">
                            Remembered your password?
                            <a href="{{ route('login') }}" class="font-semibold text-primary transition hover:text-accent">
                                Go back to login
                            </a>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </body>
</html>

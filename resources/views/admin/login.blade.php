<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Login | {{ config('app.name', 'ELMS') }}</title>
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
                <div class="grid w-full gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                    <div class="max-w-2xl">
                        <p class="text-sm font-medium uppercase tracking-[0.28em] text-primary">Admin Access</p>
                        <h1 class="display-title mt-5 text-balance text-4xl font-semibold tracking-tight text-foreground md:text-6xl">
                            Manage sales, enrollments, and pending orders
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-muted-foreground">
                            This private admin area is only for the whitelisted account. Review paid students, approve offline payments, and clean up incomplete orders from one place.
                        </p>
                    </div>

                    <section class="checkout-panel rounded-[2rem] border p-6 sm:p-8">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Private Console</p>
                            <h2 class="display-title text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">
                                Login to the admin dashboard
                            </h2>
                        </div>

                        @if ($errors->has('login'))
                            <div class="mt-6 rounded-[1.2rem] border border-red-400/22 bg-red-500/10 px-4 py-3 text-sm leading-6 text-red-100">
                                {{ $errors->first('login') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-6">
                            @csrf
                            <label class="checkout-field">
                                <span>Whitelisted email</span>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required>
                            </label>

                            <label class="checkout-field">
                                <span>Password</span>
                                <input type="password" name="password" placeholder="Password" required>
                            </label>

                            <button type="submit" class="checkout-submit">
                                Open admin dashboard
                            </button>
                        </form>
                    </section>
                </div>
            </section>
        </main>
    </body>
</html>

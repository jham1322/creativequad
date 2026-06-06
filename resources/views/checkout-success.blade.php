<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.meta-head', [
            'title' => 'Thank You | Creative Quad Vibe Coding Course',
            'description' => 'Thank you for purchasing the Creative Quad Vibe Coding Course.',
            'robots' => 'noindex,nofollow',
            'canonical' => route('checkout.thankyou', ['reference' => $reference]),
        ])
        <style>html,body{background:#020f18;color:#f4f8ff}</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <meta http-equiv="refresh" content="{{ max(1, (int) ceil(($redirectDelayMs ?? 3000) / 1000)) }};url={{ $redirectUrl }}">
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        @include('partials.meta-pixel-noscript')
        <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-background px-6 py-16">
            <div class="pointer-events-none absolute inset-0 page-aura" aria-hidden="true"></div>

            <section class="checkout-panel relative z-10 mx-auto w-full max-w-3xl rounded-[2.2rem] border p-8 text-center shadow-[var(--shadow-elegant)] sm:p-12">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-border bg-card/70 text-3xl text-primary">
                    ✓
                </div>

                <p class="mt-6 text-sm font-medium uppercase tracking-[0.28em] text-primary">Thank You</p>
                <h1 class="display-title mt-4 text-balance text-4xl font-semibold tracking-tight text-foreground sm:text-5xl">
                    Thank you for purchasing
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-muted-foreground">
                    Your slot for <span class="font-semibold text-foreground">Build Real Full Stack Web Apps using AI and Codex</span> is now secured. We’re preparing your student area now, and you’ll be redirected to your dashboard automatically in a few seconds.
                </p>

                @if ($reference)
                    <div class="mx-auto mt-8 max-w-xl rounded-[1.4rem] border border-white/10 bg-black/18 px-5 py-4 text-left backdrop-blur-md">
                        <p class="text-xs font-medium uppercase tracking-[0.24em] text-primary">Reference</p>
                        <p class="mt-2 break-all text-sm text-foreground/88">{{ $reference }}</p>
                    </div>
                @endif

                <div class="mx-auto mt-8 max-w-2xl rounded-[1.4rem] border border-white/10 bg-black/18 px-5 py-4 text-center backdrop-blur-md">
                    <p class="text-sm leading-7 text-muted-foreground">
                        Redirecting to your dashboard in <span class="font-semibold text-foreground" data-thank-you-countdown>{{ max(1, (int) ceil(($redirectDelayMs ?? 3000) / 1000)) }}</span> seconds...
                    </p>
                </div>

                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="{{ $redirectUrl }}" class="group relative inline-flex items-center gap-2 overflow-hidden rounded-full bg-primary px-8 py-4 text-sm font-semibold text-primary-foreground transition-all hover:scale-[1.02]" style="box-shadow: var(--shadow-elegant)">
                        <span class="relative z-10">Open My Dashboard Now</span>
                    </a>
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-full border border-border px-6 py-4 text-sm font-medium text-foreground/90 transition hover:bg-card/70">
                        Back to Landing Page
                    </a>
                </div>
            </section>
        </main>
        <script>
            (() => {
                const shouldTrackPurchase = @json((bool) ($shouldTrackPurchase ?? false));
                const purchaseReference = @json((string) ($reference ?? ''));
                const purchaseStorageKey = `purchaseTracked:${purchaseReference || 'thank-you'}`;
                const purchaseValue = Number(@json((float) ($purchaseValue ?? 599)));
                const purchaseCurrency = @json((string) ($purchaseCurrency ?? 'PHP'));
                const countdown = document.querySelector('[data-thank-you-countdown]');
                const redirectUrl = @json($redirectUrl);
                let remaining = Math.max(1, Math.ceil(({{ (int) ($redirectDelayMs ?? 3000) }}) / 1000));

                if (
                    shouldTrackPurchase &&
                    ! sessionStorage.getItem(purchaseStorageKey)
                ) {
                    if (window.fbq) {
                        fbq('track', 'Purchase', {
                            value: purchaseValue,
                            currency: purchaseCurrency
                        });
                        console.log('Meta Purchase Event Fired');
                        sessionStorage.setItem(purchaseStorageKey, 'true');
                    }
                }

                if (!countdown) {
                    return;
                }

                const interval = window.setInterval(() => {
                    remaining -= 1;

                    if (remaining <= 0) {
                        countdown.textContent = '0';
                        window.clearInterval(interval);
                        window.location.href = redirectUrl;
                        return;
                    }

                    countdown.textContent = String(remaining);
                }, 1000);
            })();
        </script>
    </body>
</html>

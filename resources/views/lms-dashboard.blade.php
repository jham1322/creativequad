<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LMS Dashboard | {{ config('app.name', 'ELMS') }}</title>
        <style>html,body{background:#020f18;color:#f4f8ff}</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        <main class="relative min-h-screen overflow-hidden bg-background">
            <div class="pointer-events-none absolute inset-0 page-aura" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 hero-grid opacity-[0.035]" aria-hidden="true"></div>

            <div class="lms-shell">
                <aside class="lms-sidebar">
                    <div>
                        <div class="lms-brand">
                            <img src="{{ asset('images/branding/logo.webp') }}" alt="Creative Quad" class="lms-brand-mark">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Student Area</p>
                                @isset($studentName)
                                    <p class="mt-1 text-sm text-muted-foreground">Logged in as {{ $studentName }}</p>
                                @endisset
                            </div>
                        </div>

                        <nav class="mt-8 space-y-3">
                            <a href="#overview" class="lms-nav-link is-active">Overview</a>
                            <a href="#curriculum" class="lms-nav-link">Curriculum</a>
                            <a href="#resources" class="lms-nav-link">Resources</a>
                            <a href="{{ url('/') }}" class="lms-nav-link">Back to landing</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="lms-nav-link w-full text-left">
                                    Log out
                                </button>
                            </form>
                        </nav>
                    </div>
                </aside>

                <section class="lms-main">
                    <header class="lms-topbar" id="overview">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Welcome back</p>
                            <h2 class="display-title mt-3 text-balance text-3xl font-semibold tracking-tight text-foreground md:text-4xl">
                                {{ $hasPaidAccess ? 'Your course dashboard is ready' : 'Your dashboard is ready, but payment is still pending' }}
                            </h2>
                            <p class="mt-4 max-w-3xl text-base leading-8 text-muted-foreground">
                                @if ($hasPaidAccess)
                                    Start learning right away. Your videos, lesson flow, and resources are all in one place so you can move from prompts to a real web app without getting lost.
                                @else
                                    Your account is already created. To unlock the videos and course modules, complete your payment using the same checkout you started earlier.
                                @endif
                            </p>
                        </div>

                        @if (request('enrolled'))
                            <div class="lms-success-banner">
                                <span class="lms-success-dot"></span>
                                <div>
                                    <p class="font-semibold text-foreground">Enrollment successful</p>
                                    <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                        Your payment was confirmed and your dashboard access is now open.
                                    </p>
                                </div>
                            </div>
                        @endif

                        @unless ($hasPaidAccess)
                            <div class="lms-pending-banner">
                                <div>
                                    <p class="font-semibold text-foreground">Payment still needed to unlock the course</p>
                                    <p class="mt-1 text-sm leading-6 text-muted-foreground">
                                        {{ session('pending_payment_notice', 'Please complete your pending payment to unlock all lessons, videos, and resources in your dashboard.') }}
                                    </p>
                                    @if ($pendingOrder?->payment_method)
                                        <p class="mt-3 text-xs font-medium uppercase tracking-[0.2em] text-primary/85">
                                            Current payment gateway: {{ $pendingOrder->payment_method }}
                                        </p>
                                    @endif
                                </div>

                                <div class="lms-pending-actions">
                                    @if ($pendingOrder?->invoice_url)
                                        <a href="{{ $pendingOrder->invoice_url }}" class="lms-pending-button">
                                            Continue payment
                                        </a>
                                    @endif
                                    <p class="text-sm leading-6 text-muted-foreground">
                                        Need a different gateway? Choose another payment method below and we’ll open a new checkout for you.
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('lms.pending-payment.retry') }}" class="lms-pending-retry-form">
                                    @csrf
                                    <div class="lms-pending-method-list">
                                        @foreach ($paymentMethodOptions as $key => $method)
                                            <label class="checkout-method-option">
                                                <input
                                                    class="checkout-method-radio"
                                                    type="radio"
                                                    name="payment_method"
                                                    value="{{ $key }}"
                                                    @checked(old('payment_method', strtolower((string) $pendingOrder?->payment_method ?: 'qrph')) === $key)
                                                >
                                                <div class="checkout-method-panel">
                                                    <div class="checkout-method-head">
                                                        <span class="checkout-method-control" aria-hidden="true"></span>
                                                        <div class="checkout-method-title-wrap">
                                                            <span class="checkout-method-title">{{ $method['title'] }}</span>
                                                            <span class="checkout-method-logo {{ $method['logoClass'] }}">{{ $method['logo'] }}</span>
                                                            @if (! empty($method['recommended']))
                                                                <span class="checkout-method-recommend-badge">Recommended</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="checkout-method-body">
                                                        {{ $method['description'] }}
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    @error('payment_method')
                                        <small class="checkout-field-error">{{ $message }}</small>
                                    @enderror

                                    <div class="checkout-payment-note checkout-payment-note-compact">
                                        <p class="checkout-payment-note-title">QRPH can still accept GCash and Maya</p>
                                        <p class="checkout-payment-note-body">
                                            Scan the QRPH code using <strong>GCash</strong>, <strong>Maya</strong>, or other
                                            <strong>QRPh-compatible banking and e-wallet apps</strong>.
                                        </p>
                                    </div>

                                    <button type="submit" class="lms-pending-secondary-button">
                                        Change gateway and pay again
                                    </button>
                                </form>
                            </div>
                        @endunless
                    </header>

                    <section class="lms-section" id="curriculum">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Curriculum</p>
                                <h3 class="mt-2 text-3xl font-semibold tracking-tight text-foreground">Watch the course videos</h3>
                            </div>
                            <div class="lms-curriculum-pill">
                                {{ $hasPaidAccess ? 'Follow the lessons in order and open each module as you progress.' : 'The lesson list is visible, but full access unlocks only after successful payment.' }}
                            </div>
                        </div>

                        <div class="mt-8 grid gap-4 @unless($hasPaidAccess) lms-is-locked @endunless" id="lms-curriculum-list">
                            @foreach ($lessons as $lesson)
                                <article
                                    class="lms-lesson-row {{ $loop->first ? 'is-current' : '' }}"
                                    data-lesson
                                >
                                    <button type="button" class="lms-lesson-trigger" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" @disabled(! $hasPaidAccess)>
                                        <div class="lms-lesson-index">{{ $lesson->lesson_number }}</div>
                                        <div class="lms-lesson-content">
                                            <h4 class="text-xl font-semibold text-foreground">{{ $lesson->title }}</h4>
                                            <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                                {{ $lesson->description }}
                                            </p>
                                        </div>
                                        <div class="lms-lesson-meta">
                                            <span class="{{ $lesson->status_badge_class }}">{{ $lesson->status_label }}</span>
                                            <span class="lms-lesson-time">{{ $lesson->duration }}</span>
                                            <span class="lms-lesson-chevron">⌄</span>
                                        </div>
                                    </button>
                                    <div class="lms-lesson-panel {{ $loop->first ? 'is-open' : '' }}">
                                        <p>{{ $lesson->description }}</p>

                                        @if ($lesson->embed_video_url)
                                            @if (str_contains($lesson->embed_video_url, 'youtube'))
                                                <div class="video-wrapper video-wrapper-locked" data-video-shell>
                                                    <div class="video-placeholder" data-video-placeholder>
                                                        <button
                                                            type="button"
                                                            class="video-play-trigger"
                                                            data-video-play
                                                            data-embed-src="{{ $lesson->embed_video_url }}"
                                                            data-video-title="{{ $lesson->title }}"
                                                        >
                                                            <span class="video-play-icon" aria-hidden="true">▶</span>
                                                            <span>Play lesson video</span>
                                                        </button>
                                                    </div>
                                                    <iframe
                                                        title="{{ $lesson->title }}"
                                                        loading="lazy"
                                                        referrerpolicy="strict-origin-when-cross-origin"
                                                        allow="autoplay; encrypted-media; picture-in-picture; fullscreen"
                                                        allowfullscreen
                                                        data-video-frame
                                                    ></iframe>
                                                </div>
                                            @else
                                                <div class="video-wrapper">
                                                    <iframe
                                                        src="{{ $lesson->embed_video_url }}"
                                                        title="{{ $lesson->title }}"
                                                        loading="lazy"
                                                        referrerpolicy="strict-origin-when-cross-origin"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    ></iframe>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <section class="lms-hosting-offer reveal reveal-delay-2" id="hosting-offer">
                        <div class="lms-hosting-orb lms-hosting-orb-a" aria-hidden="true"></div>
                        <div class="lms-hosting-orb lms-hosting-orb-b" aria-hidden="true"></div>
                        <div class="lms-hosting-grid">
                            <div class="lms-hosting-copy">
                                <div class="lms-hosting-badges" aria-label="Hosting offer badges">
                                    <span class="lms-hosting-badge">Student Exclusive</span>
                                    <span class="lms-hosting-badge lms-hosting-badge-off">75% OFF</span>
                                    <span class="lms-hosting-badge">FREE Domain</span>
                                    <span class="lms-hosting-badge">Recommended</span>
                                </div>

                                <p class="lms-hosting-kicker">Exclusive Student Hosting Offer</p>
                                <h3 class="lms-hosting-title">
                                    Launch your projects online with
                                    <span>DreamHost</span>
                                </h3>
                                <p class="lms-hosting-body">
                                    After building your Laravel apps and portfolio projects inside the course, you’ll need hosting to deploy them online. DreamHost is beginner-friendly and perfect for launching real-world projects.
                                </p>

                                <div class="lms-hosting-price-row">
                                    <div class="lms-hosting-price-card">
                                        <span class="lms-hosting-price-label">Limited student pricing</span>
                                        <strong>Only around ₱2,000/year</strong>
                                    </div>
                                    <div class="lms-hosting-price-card">
                                        <span class="lms-hosting-price-label">Built for this course</span>
                                        <strong>Recommended for Vibe Coding Students</strong>
                                        <small class="lms-hosting-price-note">Build and host as much as 30 web applications on one plan.</small>
                                    </div>
                                </div>

                                <ul class="lms-hosting-features">
                                    <li>Deploy Laravel projects</li>
                                    <li>Host portfolio websites</li>
                                    <li>Launch AI web apps</li>
                                    <li>WordPress ready</li>
                                    <li>Beginner friendly</li>
                                    <li>Fast setup</li>
                                </ul>

                                <div class="lms-hosting-actions">
                                    <a
                                        href="https://click.dreamhost.com/aff_c?offer_id=8&aff_id=18335&url_id=145"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="lms-hosting-cta"
                                    >
                                        <span>Claim Student Discount</span>
                                        <span class="lms-hosting-cta-icon" aria-hidden="true">→</span>
                                    </a>
                                    <p class="lms-hosting-note">FREE Domain Included</p>
                                </div>
                            </div>

                            <div class="lms-hosting-visual" aria-hidden="true">
                                <div class="lms-hosting-particles">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div class="lms-hosting-cloud">
                                    <span class="lms-hosting-cloud-dot"></span>
                                    <span class="lms-hosting-cloud-text">DreamHost cloud</span>
                                </div>
                                <div class="lms-hosting-server-stack">
                                    <div class="lms-hosting-server">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="lms-hosting-server">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="lms-hosting-server">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                                <div class="lms-hosting-signal lms-hosting-signal-a"></div>
                                <div class="lms-hosting-signal lms-hosting-signal-b"></div>
                                <div class="lms-hosting-terminal">
                                    <div class="lms-hosting-terminal-bar">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="lms-hosting-terminal-line"></div>
                                    <div class="lms-hosting-terminal-line short"></div>
                                    <div class="lms-hosting-terminal-line"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="lms-section lms-resource-grid @unless($hasPaidAccess) lms-is-locked @endunless" id="resources">
                        <article class="lms-resource-card lms-project-card">
                            <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Project File</p>
                            <h4 class="mt-3 text-2xl font-semibold tracking-tight text-foreground">Featured Stitch project reference</h4>
                            <p class="mt-5 text-sm leading-7 text-muted-foreground">
                                Open the shared Stitch project reference for this course and use it as a visual guide while building your own version inside the LMS flow.
                            </p>
                            <a
                                href="https://stitch.withgoogle.com/projects/6321885894347484253"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="lms-project-link"
                            >
                                Open Stitch project →
                            </a>
                        </article>

                        <article class="lms-resource-card">
                            <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Support</p>
                            <h4 class="mt-3 text-2xl font-semibold tracking-tight text-foreground">Stay unstuck faster</h4>
                            <p class="mt-5 text-sm leading-7 text-muted-foreground">
                                Keep your questions organized, revisit the lesson sequence, and continue building without losing momentum.
                            </p>
                            <a href="{{ url('/') }}#instructor" class="mt-6 inline-flex text-sm font-semibold text-primary transition hover:text-accent">
                                Return to the course page →
                            </a>
                        </article>
                    </section>
                </section>
            </div>
        </main>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ELMS') }}</title>
        <style>html,body{background:#020f18;color:#f4f8ff}</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        <main class="relative min-h-screen overflow-hidden bg-background">
            <div class="pointer-events-none absolute inset-0 page-aura" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 hero-glow" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 hero-grid opacity-[0.04]" aria-hidden="true"></div>

            <section class="hero-overlap-section relative flex min-h-screen w-full flex-col items-center justify-center px-6 py-24 text-center">
                <div class="pointer-events-none absolute inset-x-0 top-0 bottom-0 overflow-hidden border-y border-white/6 hero-stage opacity-90 lg:border-x-0" aria-hidden="true">
                    <div class="hero-stage-blob hero-stage-blob-one"></div>
                    <div class="hero-stage-blob hero-stage-blob-two"></div>
                    <div class="hero-stage-blob hero-stage-blob-three"></div>
                    <div class="hero-stage-blob hero-stage-blob-four"></div>
                    <div class="hero-stage-noise"></div>
                </div>

                <div class="relative mx-auto flex w-full max-w-6xl flex-col">
                    <div class="mb-6 flex w-full justify-end">
                        @auth
                            <a href="{{ route('lms.dashboard') }}" class="inline-flex items-center rounded-full border border-border bg-card/45 px-5 py-2.5 text-sm font-semibold text-foreground transition hover:bg-card/70">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full border border-border bg-card/45 px-5 py-2.5 text-sm font-semibold text-foreground transition hover:bg-card/70">
                                Login
                            </a>
                        @endauth
                    </div>

                    <div class="relative mx-auto flex max-w-5xl flex-col items-center">
                        <div class="reveal reveal-delay-1 inline-flex items-center gap-2 rounded-full border border-border bg-card/40 px-4 py-1.5 text-xs font-medium tracking-wide text-muted-foreground backdrop-blur-md">
                            <svg class="h-3.5 w-3.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z" />
                                <path d="M20 2v4" />
                                <path d="M22 4h-4" />
                                <circle cx="4" cy="20" r="2" />
                            </svg>
                            <span class="uppercase">Now Enrolling</span>
                        </div>

                        <h1 class="display-title hero-display reveal reveal-delay-1 mt-8 max-w-5xl text-balance text-5xl font-semibold leading-[0.98] tracking-tight text-foreground md:text-7xl xl:text-[5.4rem]">
                            Learn How to Build Web Apps<br>
                            Using <span class="text-primary">AI</span> and
                            <span class="text-accent">Codex</span>
                        </h1>

                        <p class="reveal reveal-delay-2 mt-6 text-2xl font-medium text-primary md:text-3xl">(Tagalog Course)</p>

                        <p class="reveal reveal-delay-2 hero-support-text mt-8 max-w-3xl text-balance text-lg leading-relaxed md:text-xl">
                            Step by step Tagalog course that shows you how to build real web apps with database using AI without writing any code and host it on your own server.
                        </p>

                        <div class="reveal reveal-delay-3 mt-10 flex flex-col items-center gap-4 sm:flex-row">
                            <a
                                href="{{ auth()->check() ? route('lms.dashboard') : route('checkout') }}"
                                class="hero-enroll-button group relative inline-flex min-w-[220px] items-center justify-center gap-2 overflow-hidden rounded-full px-8 py-4 text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                                style="background: linear-gradient(115deg, #5c36ff 0%, #a855f7 38%, #ff5db1 68%, #6f59ff 100%); background-size: 220% 220%; box-shadow: 0 22px 52px -20px rgba(117, 55, 255, 0.88), 0 0 36px rgba(168, 85, 247, 0.22), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;"
                            >
                                <span class="hero-enroll-button-glow" aria-hidden="true"></span>
                                <span class="relative z-10">Enroll now</span>
                                <svg class="relative z-10 h-4 w-4 text-white transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 12h14" />
                                    <path d="m12 5 7 7-7 7" />
                                </svg>
                            </a>

                            <a href="#curriculum" class="hero-secondary-link text-sm font-medium transition-colors hover:text-foreground">
                                View curriculum →
                            </a>
                        </div>

                        <div class="hero-video-stage reveal reveal-delay-4 mt-18 w-full max-w-6xl" data-hero-video-stage>
                            <div class="hero-video-shell" data-hero-video-shell>
                                <div class="hero-video-orb hero-video-orb-left" aria-hidden="true"></div>
                                <div class="hero-video-orb hero-video-orb-right" aria-hidden="true"></div>
                                <div class="hero-video-rim" aria-hidden="true"></div>
                                <div class="hero-video-frame" data-hero-video-frame>
                                    <button
                                        type="button"
                                        class="hero-video-poster"
                                        data-hero-video-play
                                        data-embed-src="https://player.mediadelivery.net/play/657301/0d1f5560-68ca-4f13-bc23-2929271a84ca"
                                        style="background-image:
                                            linear-gradient(180deg, rgba(3, 9, 18, 0.04), rgba(3, 9, 18, 0.48)),
                                            linear-gradient(135deg, rgba(92, 54, 255, 0.18), rgba(255, 93, 177, 0.14)),
                                            url('{{ asset('images/hero/fgg.webp') }}');"
                                    >
                                        <span class="hero-video-poster-glow" aria-hidden="true"></span>
                                        <span class="hero-video-play-button" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M8 6.82v10.36c0 .79.87 1.27 1.54.84l8.02-5.18a1 1 0 0 0 0-1.68L9.54 5.98A1 1 0 0 0 8 6.82Z" />
                                            </svg>
                                        </span>
                                    </button>
                                    <iframe
                                        title="Creative Quad course preview"
                                        loading="lazy"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                        data-hero-video-iframe
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="editorial-section editorial-overlap-section relative mx-auto max-w-6xl px-6 pt-14 pb-24 md:pt-20">
                <div class="beginner-stage relative overflow-hidden rounded-[2rem] border border-border px-6 py-12 shadow-[var(--shadow-elegant)] sm:px-8 lg:px-10">
                    <div class="pointer-events-none absolute inset-0 beginner-stage-glow" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-x-[10%] top-0 h-20 beginner-stage-rim" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-3xl text-center">
                    <p class="beginner-kicker reveal reveal-delay-1 inline-flex items-center rounded-full border border-border px-4 py-2 text-sm font-medium uppercase tracking-[0.28em] text-primary">Tailored For Beginners</p>
                    <h2 class="display-title reveal reveal-delay-2 mt-4 text-4xl font-semibold leading-tight tracking-tight text-foreground md:text-5xl">
                        Ikaw ba ’to?<br>
                        <span class="text-gradient">Then para sayo ang course na ’to!</span>
                    </h2>
                </div>

                <div class="relative mt-14 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <article class="beginner-card reveal reveal-delay-1 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <circle cx="12" cy="12" r="7.5" />
                                <path d="M9.6 9.6a2.8 2.8 0 1 1 4.3 2.4c-.95.62-1.65 1.18-1.65 2.2" />
                                <circle cx="12" cy="17.3" r=".7" fill="currentColor" stroke="none" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Nahihirapan ka magsimula gumawa ng website kasi parang ang hirap ng coding?
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-2 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <circle cx="12" cy="12" r="7.5" />
                                <path d="M9 9.4h.01" />
                                <path d="M15 9.4h.01" />
                                <path d="M8.8 15c1.2-1.6 5.2-1.6 6.4 0" />
                                <path d="m7 5 2.1 2.1" />
                                <path d="m17 5-2.1 2.1" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Nao-overwhelm ka sa sobrang daming tutorials pero hindi mo alam saan magsisimula?
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-3 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="4" y="5" width="16" height="11" rx="2" />
                                <path d="M8 20h8" />
                                <path d="m10 9 2 2-2 2" />
                                <path d="M14 13h2.5" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            May idea ka na gagawing web app project pero hindi mo alam paano siya gawin at ihost sa server mo?
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-4 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M9 15c4.3-1.1 7.9-4.7 9-9-3.7 0-6.9 1.1-9.1 3.4C6.8 11.4 6 14.2 6 18c3.8 0 6.6-.8 8.6-2.9" />
                                <path d="M9 15 6 18" />
                                <path d="M6 11c-1.5 1-2 4-2 4s3-.5 4-2" />
                                <path d="M13 6c1.5-1.5 4-2 4-2s-.5 2.5-2 4" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Gusto mong gumawa ng sariling website or system na pwede mong i-offer sa clients?
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-5 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M8 8.8A4.8 4.8 0 0 1 12.8 4H13a5 5 0 0 1 5 5c0 1.5-.6 2.9-1.7 3.9A4.5 4.5 0 0 1 14.5 17H12a4 4 0 0 1-4-4V8.8Z" />
                                <path d="M8.2 10.5H6.5a2.5 2.5 0 0 0 0 5H8" />
                                <path d="M11 8.5c.7.2 1.2.8 1.2 1.6 0 .9-.6 1.6-1.5 1.7" />
                                <path d="M13.5 9.4h.01" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Sobrang namamahalan ka sa Lovable at gusto makatipid at gusto mo ng web app kung saan ikaw ang may control
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-1 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <circle cx="12" cy="12" r="7.5" />
                                <circle cx="12" cy="12" r="3.5" />
                                <path d="M12 4.5V2.5" />
                                <path d="M19.5 12h2" />
                                <path d="M12 21.5v-2" />
                                <path d="M2.5 12h2" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Gusto mong magkaroon ng skill para gumawa ng web app anytime na mayroon kang idea
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-2 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M5 18V8" />
                                <path d="M10 18V11" />
                                <path d="M15 18V6" />
                                <path d="M20 18v-4" />
                                <path d="M4 18h17" />
                                <path d="m8 7 3-3 3 2 4-2" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Isa kang VA or freelancer na gustong mag-upgrade ng skills at income para magroon ng valuable web dev skills
                        </p>
                    </article>

                    <article class="beginner-card reveal reveal-delay-3 rounded-3xl border border-border p-6 text-left">
                        <div class="beginner-card-accent" aria-hidden="true"></div>
                        <div class="beginner-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M12 22a1 1 0 0 1 0-20 10 9 0 0 1 10 9 5 5 0 0 1-5 5h-2.25a1.75 1.75 0 0 0-1.4 2.8l.3.4a1.75 1.75 0 0 1-1.4 2.8z" />
                                <circle cx="13.5" cy="6.5" r=".5" fill="currentColor" stroke="none" />
                                <circle cx="17.5" cy="10.5" r=".5" fill="currentColor" stroke="none" />
                                <circle cx="6.5" cy="12.5" r=".5" fill="currentColor" stroke="none" />
                                <circle cx="8.5" cy="7.5" r=".5" fill="currentColor" stroke="none" />
                            </svg>
                        </div>
                        <p class="mt-5 text-base leading-6 text-foreground">
                            Isa kang designer or creative na gustong gawin yung design or app idea mo
                        </p>
                    </article>
                </div>
                </div>
            </section>

            <section class="editorial-section relative mx-auto max-w-6xl px-6 pb-28" id="instructor">
                <div class="grid items-stretch gap-10 lg:grid-cols-[1fr_1.15fr]">
                    <div class="reveal reveal-delay-1">
                        <div class="instructor-panel relative h-full min-h-[42rem] overflow-hidden rounded-[2rem] border border-border shadow-[var(--shadow-elegant)]">
                            <img
                                src="{{ asset('images/instructor/niel-vibe-coding.png') }}"
                                alt="Niel portrait"
                                class="instructor-portrait h-full min-h-[42rem] w-full object-cover"
                            >
                            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(6,10,24,0.02),rgba(6,10,24,0.28)_58%,rgba(6,10,24,0.68)_100%)]"></div>
                            <div class="absolute inset-x-0 bottom-0 p-7">
                                <div class="rounded-[1.6rem] border border-white/12 bg-black/30 p-5 backdrop-blur-md">
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-primary">Vibe Coding Mentor</p>
                                    <p class="mt-3 text-xl font-semibold text-white">Turning ideas into launch-ready apps with AI</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reveal reveal-delay-2 glass-surface glass-glow flex h-full flex-col justify-center rounded-[2rem] border border-border p-7 lg:p-8">
                        <p class="text-sm font-medium uppercase tracking-[0.28em] text-primary">About The Instructor</p>
                        <h2 class="display-title mt-4 max-w-2xl text-4xl font-semibold leading-tight tracking-tight text-foreground md:text-5xl">About the Instructor</h2>

                        <div class="mt-6 space-y-5 text-base leading-6 text-foreground">
                            <p>
                                Hey! Welcome to my Vibe Coding Course. I am Niel, and I help beginners turn their ideas into real web applications using AI. Passion ko ang mag-share ng knowledge, especially sa mga gustong matuto gumawa ng websites and systems kahit walang coding background.
                            </p>

                            <p>
                                Sa journey na ito, I’ll teach you everything you need to know, from idea, to design, to building your web app using AI, hanggang sa pag-launch nito sa live website. Updated tools, practical workflows, and a beginner-friendly approach ang focus natin dito.
                            </p>

                            <p>
                                Take note, hindi ito Lovable or kahit anong closed-source na platform. Ang workflow natin dito is Codex to GitHub, then ide-deploy natin sa sarili mong live server para mas cheaper, mas flexible, at hindi ka mabigla sa sobrang mahal na credits or platform fees.
                            </p>

                            <p>
                                This is your chance na makagawa ng sarili mong web applications — whether for your own ideas or for clients. Real system ito na control mo, own mo, at pwede mong i-update anytime without being locked into a platform na hindi mo hawak.
                            </p>

                            <p>
                                My teaching approach is simple: I always think from a beginner’s perspective. I explain things clearly, step-by-step, and focus on what actually works in real-world projects.
                            </p>

                            <p>
                                Kaya don’t overthink it — start building your first web app today.
                            </p>
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="{{ auth()->check() ? route('lms.dashboard') : route('checkout') }}"
                                class="group relative inline-flex items-center justify-center gap-3 overflow-hidden rounded-full bg-primary px-8 py-4 text-sm font-semibold text-primary-foreground transition-all hover:scale-[1.02]"
                                style="box-shadow: var(--shadow-elegant)"
                            >
                                <span class="relative z-10">Start Vibe Coding</span>
                                <span class="relative z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/22 text-base transition-transform group-hover:translate-x-0.5">
                                    →
                                </span>
                                <span class="absolute inset-0 opacity-0 transition-opacity group-hover:opacity-100 hero-cta-glow"></span>
                            </a>

                            <a href="#curriculum" class="inline-flex items-center justify-center rounded-full border border-border px-6 py-4 text-sm font-medium text-foreground/90 transition hover:bg-card/70">
                                See how the lessons flow
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="editorial-section relative mx-auto max-w-[1200px] px-6 pb-28" id="why-enroll">
                <div class="learning-stage mb-28 relative overflow-hidden rounded-[2rem] border border-border px-6 py-14 shadow-[var(--shadow-elegant)] sm:px-8 lg:px-10" id="how-you-learn">
                    <div class="pointer-events-none absolute inset-0 learning-stage-glow" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-x-[8%] top-0 h-24 learning-stage-rim" aria-hidden="true"></div>

                    <div class="relative">
                        <div class="space-y-8">
                            <article class="problem-solution-card problem-solution-problem reveal reveal-delay-1 rounded-[1.8rem] border border-border p-8 lg:p-10">
                                <div class="problem-solution-border" aria-hidden="true"></div>
                                <div class="problem-solution-layout problem-solution-layout-problem gap-8 xl:grid-cols-[minmax(0,1.08fr)_20rem] xl:gap-12">
                                    <div class="problem-solution-copy">
                                        <div class="problem-solution-header problem-solution-header-problem">
                                            <div class="problem-solution-icon">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                    <path d="M12 3a4.5 4.5 0 0 1 4.5 4.5c0 1.7-.8 3-2 4l-1 1v1.5h-3V12.5l-1-1a4.8 4.8 0 0 1-2-4A4.5 4.5 0 0 1 12 3Z" />
                                                    <path d="M10 18h4" />
                                                    <path d="M10.8 21h2.4" />
                                                </svg>
                                            </div>
                                            <div class="problem-solution-title-wrap">
                                                <p class="problem-solution-label">Problem</p>
                                                <h3 class="mt-3 text-3xl font-semibold tracking-tight text-foreground md:text-[2.35rem]">Problem</h3>
                                            </div>
                                        </div>

                                        <p class="problem-solution-body mt-8 max-w-4xl text-base leading-7 text-white/88">
                                            Maraming gustong mag-build ng website or web app, pero nai-stuck agad sila kasi akala nila sobrang hirap at sobrang technical ng coding. Ang daming tutorials online, pero karamihan doon walang malinaw na step-by-step process na puwede mo talagang sundan from start to finish. May idea ka sa isip mo, pero hindi mo alam paano mo siya gagawing real at working na application.
                                        </p>

                                        <p class="problem-solution-body mt-6 max-w-4xl text-base leading-7 text-white/88">
                                            On top of that, maraming tools at closed-source platforms na okay sa simula, pero habang tumatagal, doon mo mararamdaman na limitado ang control mo. Kapag tumaas ang pricing, nabawasan ang features, o biglang nagsara ang platform, wala kang full freedom sa app mo dahil naka-lock ka sa system nila. Ibig sabihin, hindi mo hawak nang buo ang code, deployment, at future ng project mo.
                                        </p>

                                        <div class="problem-solution-track" aria-hidden="true">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>

                                    <div class="problem-solution-visual problem-visual" aria-hidden="true">
                                        <div class="visual-frame visual-frame-problem">
                                            <div class="problem-photo-wrap">
                                                <img
                                                    src="{{ asset('images/problem/problem-overwhelm.webp') }}"
                                                    alt="Overwhelmed person working on a laptop"
                                                    class="problem-photo"
                                                >
                                                <div class="problem-photo-overlay"></div>
                                                <div class="problem-photo-vignette"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <article class="problem-solution-card problem-solution-solution reveal reveal-delay-2 rounded-[1.8rem] border border-border p-8 lg:p-10">
                                <div class="problem-solution-border" aria-hidden="true"></div>
                                <div class="problem-solution-layout problem-solution-layout-solution gap-8 xl:grid-cols-[20rem_minmax(0,1.08fr)] xl:gap-12">
                                    <div class="problem-solution-visual problem-solution-visual-left solution-visual" aria-hidden="true">
                                        <div class="visual-frame visual-frame-solution">
                                            <div class="problem-photo-wrap">
                                                <img
                                                    src="{{ asset('images/problem/solution-success.png') }}"
                                                    alt="Successful builder working on a laptop"
                                                    class="problem-photo"
                                                >
                                                <div class="problem-photo-overlay"></div>
                                                <div class="problem-photo-vignette"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="problem-solution-copy">
                                        <div class="problem-solution-header problem-solution-header-solution">
                                            <div class="problem-solution-icon problem-solution-icon-solution">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                    <path d="M10 14 4 8l2-2 4 4 8-8 2 2-10 10Z" />
                                                    <path d="M12 22C6.5 22 2 17.5 2 12" />
                                                </svg>
                                            </div>
                                            <div class="problem-solution-title-wrap">
                                                <p class="problem-solution-label problem-solution-label-solution">Solution</p>
                                                <h3 class="mt-3 text-3xl font-semibold tracking-tight text-foreground md:text-[2.35rem]">Solution</h3>
                                            </div>
                                        </div>

                                        <p class="problem-solution-body mt-8 max-w-4xl text-base leading-7 text-white/88">
                                            Sa course na ito, matututunan mo ang simple way to build web applications gamit ang AI, without needing to learn traditional coding first. You will design your UI, build your features using prompts, manage your project properly, and deploy it to a live website that people can actually access.
                                        </p>

                                        <p class="problem-solution-body mt-6 max-w-4xl text-base leading-7 text-white/88">
                                            At hindi doon nagtatapos iyon. Kahit published na ang project mo, puwede ka pa ring mag-add ng new features, mag-adjust ng design, mag-fix ng issues, at mag-improve ng system anytime. Ibig sabihin, meron ka talagang total control sa app mo, at mabibuild mo ang mga gusto mong i-build without being dependent on manual coding every step of the way.
                                        </p>

                                        <div class="problem-solution-track problem-solution-track-solution" aria-hidden="true">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>

                                    <div class="problem-solution-glow problem-solution-glow-solution" aria-hidden="true"></div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

                <div class="enroll-stage enroll-stage-shell relative overflow-hidden rounded-[2rem] border border-border px-6 py-14 shadow-[var(--shadow-elegant)] sm:px-8 lg:px-10">
                    <div class="pointer-events-none absolute inset-0 enroll-stage-glow" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-0 enroll-stage-grid opacity-[0.22]" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-x-[8%] top-0 h-24 enroll-stage-rim" aria-hidden="true"></div>

                    <div class="relative">
                        <div class="mx-auto max-w-3xl text-center">
                            <p class="reveal reveal-delay-1 enroll-kicker inline-flex items-center rounded-full border border-border px-4 py-2 text-sm font-medium uppercase tracking-[0.28em] text-primary">Course Benefits</p>
                            <h2 class="display-title reveal reveal-delay-2 mt-4 text-4xl font-semibold tracking-tight text-foreground md:text-5xl">
                                Why You Should Enroll in This Course
                            </h2>
                        </div>

                        <div class="mt-12 grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <article class="reveal reveal-delay-1 enroll-card group h-full rounded-[1.4rem] border border-border p-6">
                                <div class="enroll-card-accent" aria-hidden="true"></div>
                                <div class="enroll-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M8 15c0-2.4 1.8-4.2 4-4.2s4 1.8 4 4.2" />
                                        <path d="M12 3v3" />
                                        <path d="M5.6 5.6l2.1 2.1" />
                                        <path d="M3 12h3" />
                                        <path d="M18.4 5.6l-2.1 2.1" />
                                        <path d="M21 12h-3" />
                                        <rect x="6" y="15" width="12" height="6" rx="2" />
                                    </svg>
                                </div>
                                <h3 class="mt-5 text-2xl font-semibold tracking-tight text-foreground">Build Real Web Apps Using AI</h3>
                                <p class="mt-4 text-base leading-7 text-muted-foreground">
                                    Create real, working web applications using AI, not just theory but actual results.
                                    You will go from idea to a functional system that you can use, improve, or offer to clients.
                                </p>
                            </article>

                            <article class="reveal reveal-delay-2 enroll-card group h-full rounded-[1.4rem] border border-border p-6">
                                <div class="enroll-card-accent" aria-hidden="true"></div>
                                <div class="enroll-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M7 4h10" />
                                        <path d="M9 4v6l3 2 3-2V4" />
                                        <path d="M6 15c0-1.7 1.3-3 3-3h6c1.7 0 3 1.3 3 3v3H6z" />
                                    </svg>
                                </div>
                                <h3 class="mt-5 text-2xl font-semibold tracking-tight text-foreground">No Coding Needed</h3>
                                <p class="mt-4 text-base leading-7 text-muted-foreground">
                                    You do not need to learn traditional coding. You will build everything using prompts.
                                    This helps you avoid overwhelm and lets you progress faster, even if you are starting from zero.
                                </p>
                            </article>

                            <article class="reveal reveal-delay-3 enroll-card group h-full rounded-[1.4rem] border border-border p-6">
                                <div class="enroll-card-accent" aria-hidden="true"></div>
                                <div class="enroll-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <rect x="4" y="5" width="16" height="12" rx="2" />
                                        <path d="M8 21h8" />
                                        <path d="M10 17v4" />
                                        <path d="M14 17v4" />
                                    </svg>
                                </div>
                                <h3 class="mt-5 text-2xl font-semibold tracking-tight text-foreground">From Idea to UI Design</h3>
                                <p class="mt-4 text-base leading-7 text-muted-foreground">
                                    Design your own interface using Google Stitch, from concept to real layout.
                                    You will learn how to turn ideas into clean, modern UI components that can be used in real apps.
                                </p>
                            </article>

                            <article class="reveal reveal-delay-1 enroll-card group h-full rounded-[1.4rem] border border-border p-6">
                                <div class="enroll-card-accent" aria-hidden="true"></div>
                                <div class="enroll-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M5 12h9" />
                                        <path d="m11 6 6 6-6 6" />
                                        <rect x="3" y="4" width="18" height="16" rx="3" />
                                    </svg>
                                </div>
                                <h3 class="mt-5 text-2xl font-semibold tracking-tight text-foreground">Deploy to a Live Website</h3>
                                <p class="mt-4 text-base leading-7 text-muted-foreground">
                                    Launch your project on a live domain and server, a real website that anyone can access.
                                    This is not just a demo but a working app that is ready for real use.
                                </p>
                            </article>

                            <article class="reveal reveal-delay-2 enroll-card group h-full rounded-[1.4rem] border border-border p-6">
                                <div class="enroll-card-accent" aria-hidden="true"></div>
                                <div class="enroll-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M12 3l7 4v5c0 4.4-3 7.9-7 9-4-1.1-7-4.6-7-9V7z" />
                                        <path d="M9.5 12.5l1.6 1.6 3.4-3.6" />
                                    </svg>
                                </div>
                                <h3 class="mt-5 text-2xl font-semibold tracking-tight text-foreground">Full Control Over Your App</h3>
                                <p class="mt-4 text-base leading-7 text-muted-foreground">
                                    You are not locked into any platform. You own your system, hosting, and updates.
                                    You decide where to host it, how to improve it, and how to scale it over time.
                                </p>
                            </article>

                            <article class="reveal reveal-delay-3 enroll-card group h-full rounded-[1.4rem] border border-border p-6">
                                <div class="enroll-card-accent" aria-hidden="true"></div>
                                <div class="enroll-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M12 2v4" />
                                        <path d="M12 18v4" />
                                        <path d="M4.9 4.9l2.8 2.8" />
                                        <path d="M16.3 16.3l2.8 2.8" />
                                        <path d="M2 12h4" />
                                        <path d="M18 12h4" />
                                        <path d="M4.9 19.1l2.8-2.8" />
                                        <path d="M16.3 7.7l2.8-2.8" />
                                        <circle cx="12" cy="12" r="4" />
                                    </svg>
                                </div>
                                <h3 class="mt-5 text-2xl font-semibold tracking-tight text-foreground">Update Anytime Using AI</h3>
                                <p class="mt-4 text-base leading-7 text-muted-foreground">
                                    Add features, fix issues, and improve your app anytime using Codex with no manual coding.
                                    Even after launch, you can continue upgrading your app using simple prompts.
                                </p>
                            </article>
                        </div>

                        <p class="hero-support-text reveal reveal-delay-4 enroll-highlight mx-auto mt-12 max-w-4xl rounded-[1.6rem] border border-border px-6 py-5 text-center text-lg font-medium leading-8">
                            From idea to design to a live website, you build it, launch it, and stay in full control using AI.
                        </p>
                    </div>
                </div>
            </section>

            <section class="editorial-section relative mx-auto max-w-[1200px] px-6 pb-28" id="curriculum">
                <div class="inside-course-stage relative overflow-hidden rounded-[2rem] border border-border px-6 py-14 shadow-[var(--shadow-elegant)] sm:px-8 lg:px-10">
                    <div class="pointer-events-none absolute inset-0 inside-course-glow" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-x-[8%] top-0 h-24 inside-course-rim" aria-hidden="true"></div>

                    <div class="relative">
                        <div class="mx-auto max-w-3xl text-center">
                            <p class="reveal reveal-delay-1 inside-course-kicker inline-flex items-center rounded-full border border-border px-4 py-2 text-sm font-medium uppercase tracking-[0.28em] text-primary">
                                Curriculum Overview
                            </p>
                            <h2 class="display-title reveal reveal-delay-2 mt-4 text-4xl font-semibold tracking-tight text-foreground md:text-5xl">
                                What’s inside the course?
                            </h2>
                            <p class="reveal reveal-delay-3 mx-auto mt-5 max-w-2xl text-base leading-8 text-muted-foreground md:text-lg">
                                Here you can see the content of the course. This is an interactive and beginner friendly course
                            </p>
                        </div>

                        <div class="inside-course-columns mt-12 grid gap-6 lg:grid-cols-2">
                            <article class="inside-course-column reveal reveal-delay-1 rounded-[1.6rem] border border-border p-7 lg:p-8">
                                <div class="inside-course-column-accent" aria-hidden="true"></div>
                                <div class="inside-course-column-head">
                                    <p class="inside-course-column-label">Part 01</p>
                                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-foreground">Tools, prompts, and interface design</h3>
                                </div>

                                <div class="inside-course-list mt-8 space-y-4">
                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>How to use Codex from OpenAI together with ChatGPT as your actual build workflow.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Install Codex properly and set up the project so you can start building your store step by step.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>How to write better prompts so Codex gives you cleaner output, clearer edits, and usable next steps.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Create the UI design for an e-commerce website using Google Stitch before turning it into a real build.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Build the actual e-commerce website, not just the design, including the core pages and real application flow.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="inside-course-column reveal reveal-delay-2 rounded-[1.6rem] border border-border p-7 lg:p-8">
                                <div class="inside-course-column-accent inside-course-column-accent-alt" aria-hidden="true"></div>
                                <div class="inside-course-column-head">
                                    <p class="inside-course-column-label">Part 02</p>
                                    <h3 class="mt-3 text-2xl font-semibold tracking-tight text-foreground">Features, deployment, and live updates</h3>
                                </div>

                                <div class="inside-course-list mt-8 space-y-4">
                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Add features and create an admin dashboard where products can be managed in a practical way.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Let users update, add, and delete items, place orders, and adjust pricing from the system.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Connect the project to GitHub so your code is versioned, backed up, and easier to update confidently.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Deploy the working website to your own server so your store becomes a real live website online.</p>
                                    </div>

                                    <div class="inside-course-item">
                                        <span class="inside-course-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p>Keep improving the live website through Codex even after launch, from design fixes to feature updates on your hosting server.</p>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div class="reveal reveal-delay-4 inside-course-footer mt-10 rounded-[1.5rem] border border-border px-6 py-5 text-center">
                            <p class="mx-auto max-w-4xl text-lg font-medium leading-8 text-foreground/92">
                                This course is built around one real project, so instead of jumping between disconnected lessons, you’ll see the full process from UI concept to live web app that you can keep updating with Codex.
                            </p>
                        </div>

                    </div>
                </div>
            </section>

            <section class="editorial-section relative mx-auto max-w-[1200px] px-6 pb-28" id="reviews">
                <div class="testimonial-stage testimonial-stage-shell relative overflow-hidden rounded-[2rem] border border-border px-6 py-14 shadow-[var(--shadow-elegant)] sm:px-8 lg:px-10">
                    <div class="pointer-events-none absolute inset-0 testimonial-stage-glow" aria-hidden="true"></div>

                    <div class="relative">
                        <div class="mx-auto max-w-3xl text-center">
                            <p class="reveal reveal-delay-1 testimonial-kicker inline-flex items-center rounded-full border border-border px-4 py-2 text-sm font-medium uppercase tracking-[0.28em] text-primary">
                                Feedbacks and Reviews
                            </p>
                            <h3 class="display-title reveal reveal-delay-2 mt-4 text-4xl font-semibold tracking-tight text-foreground md:text-5xl">
                                What students say after building with Codex
                            </h3>
                        </div>

                        <div class="mt-10 grid gap-6 lg:grid-cols-3">
                                <article class="testimonial-card reveal reveal-delay-1 rounded-[1.6rem] border border-border p-7">
                                    <div class="testimonial-quote">“</div>
                                    <p class="testimonial-body">
                                        I really like the teaching style of Coach. Hindi lang basta pinapakita yung output. He explained the structure and gave tips on prompting, and then connecting to GitHub and then to my own live server. Also, hindi ka maiiwan sa ere kasi very clear yung process. Hindi ako lost.
                                    </p>
                                    <p class="testimonial-name">John D.</p>
                                </article>

                                <article class="testimonial-card reveal reveal-delay-2 rounded-[1.6rem] border border-border p-7">
                                    <div class="testimonial-quote">“</div>
                                    <p class="testimonial-body">
                                        I have tried many tutorials before, but this one was one of the best because the instructor tends to simplify the process and hindi ka talaga iiwan sa guessing stage because malinaw kung paano gagawin from start to finish.
                                    </p>
                                    <p class="testimonial-name">Kristen C.</p>
                                </article>

                                <article class="testimonial-card reveal reveal-delay-3 rounded-[1.6rem] border border-border p-7">
                                    <div class="testimonial-quote">“</div>
                                    <p class="testimonial-body">
                                        Madaling sundan kasi Taglish, very practical, and very nice yung project na ginawa, which is an e-commerce website. And I like na hindi closed platform yung approach. And after ma-launch, I can update the live site through Codex. So pakiramdam mo hawak mo talaga yung application mo.
                                    </p>
                                    <p class="testimonial-name">Jomar B.</p>
                                </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative w-full px-0 pb-28" id="pricing">
                <div class="pricing-cta-stage pricing-cta-stage-full reveal reveal-delay-4 border-y border-white/8 px-6 py-14 text-center sm:px-8 lg:px-10">
                    <div class="pricing-cta-glow" aria-hidden="true"></div>
                    <div class="relative mx-auto max-w-5xl">
                        <p class="pricing-cta-kicker inline-flex items-center rounded-full border border-border px-4 py-2 text-sm font-medium uppercase tracking-[0.28em] text-primary">
                            Limited Launch Offer
                        </p>
                        <h3 class="display-title mt-5 text-4xl font-semibold tracking-tight text-foreground md:text-5xl">
                            How much is this course?
                        </h3>

                        <div class="mt-6 flex flex-col items-center gap-4">
                            <p class="pricing-cta-label text-xl font-semibold text-foreground md:text-2xl">
                                Launching price for a limited time
                            </p>
                            <p class="pricing-cta-price text-6xl font-bold leading-none md:text-8xl">
                                ₱599
                            </p>
                        </div>

                        <div class="pricing-cta-timer-wrap mt-8 flex flex-col items-center gap-3">
                            <div class="pricing-countdown" id="pricing-countdown" aria-live="polite">
                                <div class="pricing-countdown-segment">
                                    <span class="pricing-countdown-value" data-countdown="hours">18</span>
                                    <span class="pricing-countdown-label">Hours</span>
                                </div>
                                <div class="pricing-countdown-segment">
                                    <span class="pricing-countdown-value" data-countdown="minutes">00</span>
                                    <span class="pricing-countdown-label">Minutes</span>
                                </div>
                                <div class="pricing-countdown-segment">
                                    <span class="pricing-countdown-value" data-countdown="seconds">00</span>
                                    <span class="pricing-countdown-label">Seconds</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-center">
                            <a
                                href="{{ route('checkout') }}"
                                class="pricing-cta-button group relative inline-flex min-h-[4.5rem] w-full max-w-[300px] items-center justify-center overflow-hidden rounded-full px-8 text-lg font-semibold text-white transition-transform duration-300 hover:scale-[1.03]"
                            >
                                <span class="pricing-cta-button-bg" aria-hidden="true"></span>
                                <span class="pricing-cta-button-glow" aria-hidden="true"></span>
                                <span class="relative z-10">Enroll Now</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </body>
</html>

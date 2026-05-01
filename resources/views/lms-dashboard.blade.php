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
                            <span class="lms-brand-mark"></span>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Student Area</p>
                                <h1 class="mt-1 text-xl font-semibold text-foreground">Vibe Coding LMS</h1>
                            </div>
                        </div>

                        <nav class="mt-8 space-y-3">
                            <a href="#overview" class="lms-nav-link is-active">Overview</a>
                            <a href="#curriculum" class="lms-nav-link">Curriculum</a>
                            <a href="#resources" class="lms-nav-link">Resources</a>
                            <a href="{{ url('/') }}" class="lms-nav-link">Back to landing</a>
                        </nav>
                    </div>
                </aside>

                <section class="lms-main">
                    <header class="lms-topbar" id="overview">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Welcome back</p>
                            <h2 class="display-title mt-3 text-balance text-3xl font-semibold tracking-tight text-foreground md:text-4xl">
                                Your course dashboard is ready
                            </h2>
                            <p class="mt-4 max-w-3xl text-base leading-8 text-muted-foreground">
                                Start learning right away. Your videos, lesson flow, and resources are all in one place so you can move from prompts to a real web app without getting lost.
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
                    </header>

                    <section class="lms-section" id="curriculum">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Curriculum</p>
                                <h3 class="mt-2 text-3xl font-semibold tracking-tight text-foreground">Watch the course videos</h3>
                            </div>
                            <p class="max-w-2xl text-sm leading-7 text-muted-foreground">
                                Each lesson is designed to feel sequential and practical, so students always know what to watch next.
                            </p>
                        </div>

                        <div class="mt-8 grid gap-4" id="lms-curriculum-list">
                            <article
                                class="lms-lesson-row is-current"
                                data-lesson
                                data-kicker="Lesson 01"
                                data-title="Welcome to Vibe Coding"
                                data-description="Get oriented with the exact Codex → GitHub → own server workflow, and see how the course removes the usual confusion around building full stack apps."
                                data-module="Module 1"
                                data-video-title="Intro, setup, and how this LMS works"
                                data-duration="12:48"
                                data-image="{{ asset('images/problem/solution-success.png') }}"
                            >
                                <button type="button" class="lms-lesson-trigger" aria-expanded="true">
                                    <div class="lms-lesson-index">01</div>
                                    <div class="lms-lesson-content">
                                        <h4 class="text-xl font-semibold text-foreground">Welcome to the course and what you’ll build</h4>
                                        <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                            A quick orientation so students understand the full system they’re about to create and how the course flow works from start to finish.
                                        </p>
                                    </div>
                                    <div class="lms-lesson-meta">
                                        <span class="lms-lesson-tag">Playing now</span>
                                        <span class="lms-lesson-time">12:48</span>
                                        <span class="lms-lesson-chevron">⌄</span>
                                    </div>
                                </button>
                                <div class="lms-lesson-panel is-open">
                                    <p>
                                        In this lesson, students get the full overview of how the course works, what they’re going to build, and why the workflow is structured this way.
                                    </p>
                                </div>
                            </article>

                            <article
                                class="lms-lesson-row"
                                data-lesson
                                data-kicker="Lesson 02"
                                data-title="Set up Codex, GitHub, and your project structure"
                                data-description="Prepare the exact environment you need so your first build is organized from day one."
                                data-module="Module 1"
                                data-video-title="Codex, GitHub, and project setup"
                                data-duration="18:12"
                                data-image="{{ asset('images/problem/problem-overwhelm.webp') }}"
                            >
                                <button type="button" class="lms-lesson-trigger" aria-expanded="false">
                                    <div class="lms-lesson-index">02</div>
                                    <div class="lms-lesson-content">
                                        <h4 class="text-xl font-semibold text-foreground">Set up Codex, GitHub, and your project structure</h4>
                                        <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                            Prepare the exact environment you need so your first build is organized from day one.
                                        </p>
                                    </div>
                                    <div class="lms-lesson-meta">
                                        <span class="lms-lesson-tag lms-lesson-tag-muted">Up next</span>
                                        <span class="lms-lesson-time">18:12</span>
                                        <span class="lms-lesson-chevron">⌄</span>
                                    </div>
                                </button>
                                <div class="lms-lesson-panel">
                                    <p>
                                        This lesson walks through the exact setup so students can avoid messy starts and build with a cleaner foundation immediately.
                                    </p>
                                </div>
                            </article>

                            <article
                                class="lms-lesson-row"
                                data-lesson
                                data-kicker="Lesson 03"
                                data-title="Design your UI using Google Stitch"
                                data-description="Translate rough ideas into a cleaner interface before turning the design into a real working app."
                                data-module="Module 2"
                                data-video-title="UI design before development"
                                data-duration="21:30"
                                data-image="{{ asset('images/problem/solution-success.png') }}"
                            >
                                <button type="button" class="lms-lesson-trigger" aria-expanded="false">
                                    <div class="lms-lesson-index">03</div>
                                    <div class="lms-lesson-content">
                                        <h4 class="text-xl font-semibold text-foreground">Design your UI using Google Stitch</h4>
                                        <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                            Translate rough ideas into a cleaner interface before turning the design into a real working app.
                                        </p>
                                    </div>
                                    <div class="lms-lesson-meta">
                                        <span class="lms-lesson-tag lms-lesson-tag-muted">Queued</span>
                                        <span class="lms-lesson-time">21:30</span>
                                        <span class="lms-lesson-chevron">⌄</span>
                                    </div>
                                </button>
                                <div class="lms-lesson-panel">
                                    <p>
                                        Students learn how to shape their screens first, so development feels more intentional and less chaotic once the build starts.
                                    </p>
                                </div>
                            </article>

                            <article
                                class="lms-lesson-row"
                                data-lesson
                                data-kicker="Lesson 04"
                                data-title="Build the features with AI prompts"
                                data-description="Learn how to prompt for actual features and outputs instead of just generating disconnected snippets."
                                data-module="Module 2"
                                data-video-title="Prompting for real product features"
                                data-duration="26:05"
                                data-image="{{ asset('images/problem/solution-success.png') }}"
                            >
                                <button type="button" class="lms-lesson-trigger" aria-expanded="false">
                                    <div class="lms-lesson-index">04</div>
                                    <div class="lms-lesson-content">
                                        <h4 class="text-xl font-semibold text-foreground">Build the features with AI prompts</h4>
                                        <p class="mt-2 text-sm leading-7 text-muted-foreground">
                                            Learn how to prompt for actual features and outputs instead of just generating disconnected snippets.
                                        </p>
                                    </div>
                                    <div class="lms-lesson-meta">
                                        <span class="lms-lesson-tag lms-lesson-tag-muted">Queued</span>
                                        <span class="lms-lesson-time">26:05</span>
                                        <span class="lms-lesson-chevron">⌄</span>
                                    </div>
                                </button>
                                <div class="lms-lesson-panel">
                                    <p>
                                        This lesson focuses on turning prompts into complete feature work instead of random outputs that are hard to integrate.
                                    </p>
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="lms-section lms-resource-grid" id="resources">
                        <article class="lms-resource-card">
                            <p class="text-xs font-medium uppercase tracking-[0.22em] text-primary">Downloads</p>
                            <h4 class="mt-3 text-2xl font-semibold tracking-tight text-foreground">Resources and references</h4>
                            <ul class="mt-5 space-y-3 text-sm leading-7 text-muted-foreground">
                                <li>Prompt templates for feature building</li>
                                <li>Starter project structure guide</li>
                                <li>Deployment checklist for your live server</li>
                            </ul>
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

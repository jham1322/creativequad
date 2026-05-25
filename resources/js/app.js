const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const revealItems = document.querySelectorAll('.reveal');
const root = document.documentElement;
root.dataset.theme = 'dark';
window.localStorage.removeItem('theme-preference');

if (prefersReducedMotion.matches || !('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('reveal-visible'));
} else {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.18,
            rootMargin: '0px 0px -8% 0px',
        },
    );

revealItems.forEach((item) => observer.observe(item));
}

const lessonItems = document.querySelectorAll('[data-lesson]');
const activeLessonKicker = document.getElementById('lms-active-lesson-kicker');
const activeLessonTitle = document.getElementById('lms-active-lesson-title');
const activeLessonDescription = document.getElementById('lms-active-lesson-description');
const activeLessonDuration = document.getElementById('lms-active-lesson-duration');
const activeModuleLabel = document.getElementById('lms-active-module-label');
const activeVideoTitle = document.getElementById('lms-active-video-title');
const videoPoster = document.getElementById('lms-video-poster');

const stopLockedVideo = (lesson) => {
    const shell = lesson.querySelector('[data-video-shell]');
    const frame = lesson.querySelector('[data-video-frame]');

    if (!shell || !frame) {
        return;
    }

    shell.classList.remove('is-playing');
    frame.removeAttribute('src');
};

const activateLesson = (lesson, shouldOpen = true) => {
    lessonItems.forEach((item) => {
        const trigger = item.querySelector('.lms-lesson-trigger');
        const panel = item.querySelector('.lms-lesson-panel');
        const isActive = item === lesson && shouldOpen;

        item.classList.toggle('is-current', isActive);
        trigger?.setAttribute('aria-expanded', String(isActive));
        panel?.classList.toggle('is-open', isActive);

        if (!isActive) {
            stopLockedVideo(item);
        }
    });

    if (!shouldOpen) {
        stopLockedVideo(lesson);
        return;
    }

    if (!activeLessonKicker || !activeLessonTitle || !activeLessonDescription || !activeLessonDuration || !activeModuleLabel || !activeVideoTitle || !videoPoster) {
        return;
    }

    activeLessonKicker.textContent = lesson.dataset.kicker || '';
    activeLessonTitle.textContent = lesson.dataset.title || '';
    activeLessonDescription.textContent = lesson.dataset.description || '';
    activeLessonDuration.textContent = lesson.dataset.duration || '';
    activeModuleLabel.textContent = lesson.dataset.module || '';
    activeVideoTitle.textContent = lesson.dataset.videoTitle || '';

    if (lesson.dataset.image) {
        videoPoster.style.background = `
            linear-gradient(160deg, rgba(121, 73, 255, 0.28), rgba(7, 21, 34, 0.42) 36%, rgba(255, 93, 177, 0.18) 100%),
            radial-gradient(circle at 18% 24%, rgba(217, 193, 255, 0.2), transparent 22%),
            url('${lesson.dataset.image}') center/cover no-repeat
        `;
    }
};

lessonItems.forEach((lesson) => {
    lesson.querySelector('.lms-lesson-trigger')?.addEventListener('click', () => {
        const trigger = lesson.querySelector('.lms-lesson-trigger');
        const isExpanded = trigger?.getAttribute('aria-expanded') === 'true';

        activateLesson(lesson, !isExpanded);
    });

    lesson.querySelector('[data-video-play]')?.addEventListener('click', (event) => {
        const button = event.currentTarget;
        const shell = lesson.querySelector('[data-video-shell]');
        const frame = lesson.querySelector('[data-video-frame]');

        if (!(button instanceof HTMLElement) || !shell || !(frame instanceof HTMLIFrameElement)) {
            return;
        }

        const embedSrc = button.dataset.embedSrc;

        if (!embedSrc) {
            return;
        }

        const separator = embedSrc.includes('?') ? '&' : '?';
        frame.src = `${embedSrc}${separator}autoplay=1&controls=1`;
        shell.classList.add('is-playing');
    });
});

const countdownRoot = document.getElementById('pricing-countdown');
const passwordToggles = document.querySelectorAll('[data-password-toggle]');
const adminAnalyticsRoot = document.querySelector('[data-admin-analytics]');
const heroVideoStage = document.querySelector('[data-hero-video-stage]');
const heroVideoShell = document.querySelector('[data-hero-video-shell]');
const heroVideoFrame = document.querySelector('[data-hero-video-frame]');
const heroVideoPosterButton = document.querySelector('[data-hero-video-play]');
const heroVideoElement = document.querySelector('[data-hero-video-element]');

const startHeroVideo = ({ muted = false, fallbackToMuted = false } = {}) => {
    if (!(heroVideoElement instanceof HTMLVideoElement) || !(heroVideoFrame instanceof HTMLElement)) {
        return Promise.resolve(false);
    }

    heroVideoElement.muted = muted;
    heroVideoElement.defaultMuted = muted;
    heroVideoElement.volume = 1;
    heroVideoFrame.classList.add('is-playing');

    const playback = heroVideoElement.play();

    if (playback && typeof playback.catch === 'function') {
        return playback.catch(() => {
            if (fallbackToMuted && !muted) {
                heroVideoElement.muted = true;
                heroVideoElement.defaultMuted = true;

                const mutedPlayback = heroVideoElement.play();

                if (mutedPlayback && typeof mutedPlayback.catch === 'function') {
                    return mutedPlayback.catch(() => {
                        heroVideoFrame.classList.remove('is-playing');
                        return false;
                    });
                }

                return true;
            }

            heroVideoFrame.classList.remove('is-playing');
            return false;
        });
    }

    return Promise.resolve(true);
};

heroVideoPosterButton?.addEventListener('click', () => {
    startHeroVideo({ muted: false });
});

if (heroVideoStage && heroVideoShell && !prefersReducedMotion.matches) {
    let ticking = false;

    const updateHeroVideoZoom = () => {
        const rect = heroVideoStage.getBoundingClientRect();
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const stageHeight = rect.height || 1;
        const start = viewportHeight * 0.92;
        const end = -stageHeight * 0.4;
        const rawProgress = (start - rect.top) / (start - end);
        const progress = Math.min(Math.max(rawProgress, 0), 1);

        const maxScale = window.innerWidth < 768 ? 1.08 : window.innerWidth < 1280 ? 1.14 : 1.18;
        const scale = 1 + ((maxScale - 1) * progress);
        const offset = progress * -32;

        heroVideoShell.style.setProperty('--hero-video-scale', scale.toFixed(4));
        heroVideoStage.style.setProperty('--hero-video-offset', `${offset.toFixed(2)}px`);
        heroVideoStage.classList.toggle('is-zooming', progress > 0.02);
        ticking = false;
    };

    const requestHeroVideoZoomUpdate = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        window.requestAnimationFrame(updateHeroVideoZoom);
    };

    updateHeroVideoZoom();
    window.addEventListener('scroll', requestHeroVideoZoomUpdate, { passive: true });
    window.addEventListener('resize', requestHeroVideoZoomUpdate);
}

if (heroVideoStage && heroVideoPosterButton && 'IntersectionObserver' in window) {
    let hasAutoStartedHeroVideo = false;

    const autoStartHeroVideo = new IntersectionObserver(
        (entries) => {
            const entry = entries[0];

            if (!entry?.isIntersecting || hasAutoStartedHeroVideo) {
                return;
            }

            if (!(heroVideoElement instanceof HTMLVideoElement)) {
                return;
            }

            startHeroVideo({ muted: false, fallbackToMuted: true });
            hasAutoStartedHeroVideo = true;
            autoStartHeroVideo.disconnect();
        },
        {
            threshold: 0.55,
            rootMargin: '0px 0px -6% 0px',
        },
    );

    autoStartHeroVideo.observe(heroVideoStage);
}

if (countdownRoot) {
    const hoursEl = countdownRoot.querySelector('[data-countdown="hours"]');
    const minutesEl = countdownRoot.querySelector('[data-countdown="minutes"]');
    const secondsEl = countdownRoot.querySelector('[data-countdown="seconds"]');
    const storageKey = 'vibe-coding-launch-offer-expires-at';
    const countdownDuration = 18 * 60 * 60 * 1000;

    const formatUnit = (value) => String(value).padStart(2, '0');

    let expiresAt = Number.parseInt(window.localStorage.getItem(storageKey) || '', 10);

    if (!Number.isFinite(expiresAt) || expiresAt <= Date.now()) {
        expiresAt = Date.now() + countdownDuration;
        window.localStorage.setItem(storageKey, String(expiresAt));
    }

    const renderCountdown = () => {
        const remaining = Math.max(0, expiresAt - Date.now());
        const totalSeconds = Math.floor(remaining / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        if (hoursEl) hoursEl.textContent = formatUnit(hours);
        if (minutesEl) minutesEl.textContent = formatUnit(minutes);
        if (secondsEl) secondsEl.textContent = formatUnit(seconds);

        if (remaining === 0) {
            expiresAt = Date.now() + countdownDuration;
            window.localStorage.setItem(storageKey, String(expiresAt));
        }
    };

    renderCountdown();
    window.setInterval(renderCountdown, 1000);
}

passwordToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const field = toggle.closest('.checkout-password-wrap');
        const input = field?.querySelector('[data-password-input]');
        const label = toggle.querySelector('[data-password-toggle-label]');

        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        const shouldShow = input.type === 'password';
        input.type = shouldShow ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
        toggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');

        if (label) {
            label.textContent = shouldShow ? 'Hide' : 'Show';
        }
    });
});

if (adminAnalyticsRoot instanceof HTMLElement) {
    const analyticsEndpoint = adminAnalyticsRoot.dataset.analyticsEndpoint;
    const updatedAtEl = adminAnalyticsRoot.querySelector('[data-analytics-updated-at]');
    const topPagesBody = adminAnalyticsRoot.querySelector('[data-analytics-top-pages]');
    const recentActivityBody = adminAnalyticsRoot.querySelector('[data-analytics-recent-activity]');
    let analyticsRefreshTimer;

    const formatNumber = (value) => new Intl.NumberFormat().format(Number(value || 0));

    const renderMetric = (key, value) => {
        const metricEl = adminAnalyticsRoot.querySelector(`[data-analytics-metric="${key}"]`);

        if (metricEl) {
            metricEl.textContent = formatNumber(value);
        }
    };

    const renderRows = (target, rows, builder, emptyMessage, colspan) => {
        if (!(target instanceof HTMLElement)) {
            return;
        }

        if (!Array.isArray(rows) || rows.length === 0) {
            target.innerHTML = `<tr><td colspan="${colspan}" class="admin-empty-state">${emptyMessage}</td></tr>`;
            return;
        }

        target.innerHTML = rows.map(builder).join('');
    };

    const syncAnalytics = (payload) => {
        if (!payload || typeof payload !== 'object') {
            return;
        }

        renderMetric('live_visitors', payload.live_visitors);
        renderMetric('unique_visitors_today', payload.unique_visitors_today);
        renderMetric('page_views_today', payload.page_views_today);
        renderMetric('unique_visitors_7d', payload.unique_visitors_7d);

        const totalMetricMeta = adminAnalyticsRoot.querySelector('[data-analytics-metric="unique_visitors_7d"]')?.nextElementSibling;

        if (totalMetricMeta instanceof HTMLElement && typeof payload.total_unique_visitors !== 'undefined') {
            totalMetricMeta.textContent = `${formatNumber(payload.total_unique_visitors)} total unique visitors recorded`;
        }

        if (updatedAtEl && payload.updated_at_label) {
            updatedAtEl.textContent = payload.updated_at_label;
        }

        renderRows(
            topPagesBody,
            payload.top_pages,
            (page) => `
                <tr>
                    <td>${page.label}</td>
                    <td>${formatNumber(page.views)}</td>
                    <td>${formatNumber(page.unique_visitors)}</td>
                </tr>
            `,
            'No analytics data yet. Visits will appear here once people start browsing the site.',
            3,
        );

        renderRows(
            recentActivityBody,
            payload.recent_activity,
            (activity) => `
                <tr>
                    <td>${activity.label}</td>
                    <td>${activity.viewed_at_label}</td>
                </tr>
            `,
            'No recent visitor activity yet.',
            2,
        );
    };

    const fetchAnalytics = async () => {
        if (!analyticsEndpoint) {
            return;
        }

        try {
            const response = await window.fetch(analyticsEndpoint, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            syncAnalytics(payload);
        } catch (_error) {
            // Keep the last rendered analytics state if polling fails temporarily.
        }
    };

    fetchAnalytics();
    analyticsRefreshTimer = window.setInterval(fetchAnalytics, 30000);
    window.addEventListener('beforeunload', () => window.clearInterval(analyticsRefreshTimer), { once: true });
}

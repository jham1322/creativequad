<?php

namespace App\Http\Middleware;

use App\Models\AnalyticsPageView;
use App\Models\AnalyticsVisitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorAnalytics
{
    private const VISITOR_COOKIE = 'creativequad_visitor';

    private const PAGE_VIEW_DEDUPE_SECONDS = 120;

    private static ?bool $analyticsTablesReady = null;

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        if (! $this->analyticsTablesReady()) {
            return $response;
        }

        $visitorKey = (string) ($request->cookie(self::VISITOR_COOKIE) ?: Str::uuid());

        if (! $request->cookies->has(self::VISITOR_COOKIE)) {
            Cookie::queue(
                cookie(
                    name: self::VISITOR_COOKIE,
                    value: $visitorKey,
                    minutes: 60 * 24 * 365,
                    path: '/',
                    secure: $request->isSecure(),
                    httpOnly: true,
                    sameSite: 'lax',
                )
            );
        }

        $this->recordVisit($request, $visitorKey);

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return false;
        }

        if ($request->user() !== null) {
            return false;
        }

        if ($request->routeIs('admin.*') || $request->routeIs('webhooks.*')) {
            return false;
        }

        if ($request->is('up')) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        return $contentType === '' || str_contains($contentType, 'text/html');
    }

    private function analyticsTablesReady(): bool
    {
        if (self::$analyticsTablesReady !== null) {
            return self::$analyticsTablesReady;
        }

        self::$analyticsTablesReady = Schema::hasTable('analytics_visitors')
            && Schema::hasTable('analytics_page_views');

        return self::$analyticsTablesReady;
    }

    private function recordVisit(Request $request, string $visitorKey): void
    {
        $now = now();
        $path = '/' . ltrim($request->path(), '/');
        $path = $path === '//' ? '/' : $path;
        $routeName = (string) ($request->route()?->getName() ?? '');
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $user = $request->user();
        $ipHash = hash('sha256', (string) ($request->ip() ?? 'unknown'));
        $userAgent = Str::limit((string) $request->userAgent(), 1000, '');
        $referrer = Str::limit((string) $request->headers->get('referer', ''), 1000, '');

        $visitor = AnalyticsVisitor::query()->firstOrNew([
            'visitor_key' => $visitorKey,
        ]);

        if (! $visitor->exists) {
            $visitor->first_seen_at = $now;
            $visitor->landing_path = $path;
            $visitor->landing_route_name = $routeName !== '' ? $routeName : null;
            $visitor->page_views = 0;
        }

        $visitor->session_id = $sessionId;
        $visitor->user_id = $user?->id;
        $visitor->last_seen_at = $now;
        $visitor->last_path = $path;
        $visitor->last_route_name = $routeName !== '' ? $routeName : null;
        $visitor->ip_hash = $ipHash;
        $visitor->user_agent = $userAgent !== '' ? $userAgent : null;
        $visitor->referrer = $referrer !== '' ? $referrer : null;

        $pageViewKey = sprintf('analytics:view:%s:%s', $visitorKey, sha1($path));
        $shouldCountPageView = Cache::store('file')->add($pageViewKey, true, now()->addSeconds(self::PAGE_VIEW_DEDUPE_SECONDS));

        if ($shouldCountPageView) {
            $visitor->page_views = (int) $visitor->page_views + 1;
        }

        $visitor->save();

        if (! $shouldCountPageView) {
            return;
        }

        AnalyticsPageView::query()->create([
            'visitor_key' => $visitorKey,
            'session_id' => $sessionId,
            'user_id' => $user?->id,
            'path' => $path,
            'route_name' => $routeName !== '' ? $routeName : null,
            'referrer' => $referrer !== '' ? $referrer : null,
            'ip_hash' => $ipHash,
            'user_agent' => $userAgent !== '' ? $userAgent : null,
            'viewed_at' => $now,
        ]);
    }
}

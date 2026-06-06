<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.meta-head', [
            'title' => 'Admin Dashboard | Creative Quad Vibe Coding Course',
            'description' => 'Private admin dashboard for Creative Quad enrollments and payments.',
            'robots' => 'noindex,nofollow',
            'canonical' => route('admin.dashboard'),
        ])
        <style>html,body{background:#020f18;color:#f4f8ff}</style>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|inter:400,500,600,700,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-background font-sans text-foreground antialiased">
        @include('partials.meta-pixel-noscript')
        <main class="relative min-h-screen overflow-hidden bg-background">
            <div class="pointer-events-none absolute inset-0 page-aura" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 hero-grid opacity-[0.035]" aria-hidden="true"></div>

            <section class="relative mx-auto max-w-7xl px-6 py-10">
                <header class="admin-topbar">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Admin Dashboard</p>
                        <h1 class="display-title mt-3 text-balance text-3xl font-semibold tracking-tight text-foreground md:text-5xl">
                            Track course revenue and review enrollments
                        </h1>
                        <p class="mt-4 max-w-3xl text-base leading-8 text-muted-foreground">
                            Monitor paid students, approve manual payments, and clean up pending orders without digging through scattered screenshots and messages.
                        </p>
                    </div>

                    <div class="admin-topbar-actions">
                        <div class="admin-chip">
                            Logged in as {{ $adminEmail }}
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="admin-ghost-button">Log out</button>
                        </form>
                    </div>
                </header>

                @if (session('admin_status'))
                    <div class="mt-6 rounded-[1.25rem] border border-emerald-400/22 bg-emerald-500/10 px-5 py-4 text-sm leading-7 text-emerald-100">
                        {{ session('admin_status') }}
                    </div>
                @endif

                @if (session('admin_temp_password'))
                    <div class="mt-6 rounded-[1.25rem] border border-amber-400/22 bg-amber-500/10 px-5 py-4 text-sm leading-7 text-amber-100">
                        We could not send the temporary password email. Share these credentials manually with
                        <strong>{{ session('admin_temp_email') }}</strong>:
                        <span class="admin-inline-secret">{{ session('admin_temp_password') }}</span>
                    </div>
                @endif

                @if ($errors->has('admin'))
                    <div class="mt-6 rounded-[1.25rem] border border-red-400/22 bg-red-500/10 px-5 py-4 text-sm leading-7 text-red-100">
                        {{ $errors->first('admin') }}
                    </div>
                @endif

                <section class="admin-metrics-grid">
                    <article class="admin-metric-card">
                        <p class="admin-metric-label">Total sales</p>
                        <h2 class="admin-metric-value">₱{{ number_format($totalSales, 2) }}</h2>
                        <p class="admin-metric-meta">{{ $paidOrdersCount }} completed enrollments</p>
                    </article>

                    <article class="admin-metric-card">
                        <p class="admin-metric-label">Active students</p>
                        <h2 class="admin-metric-value">{{ $totalStudents }}</h2>
                        <p class="admin-metric-meta">Students with paid course access</p>
                    </article>

                    <article class="admin-metric-card">
                        <p class="admin-metric-label">Pending payments</p>
                        <h2 class="admin-metric-value">{{ $pendingOrdersCount }}</h2>
                        <p class="admin-metric-meta">Orders waiting for Xendit or manual approval</p>
                    </article>
                </section>

                <section class="admin-panel mt-6">
                    <div class="admin-panel-head">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Payment Test Switcher</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-foreground">Change the Xendit charge amount anytime</h3>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                                This only changes the actual amount sent to Xendit for new checkout sessions. It does not change the public landing page price or the checkout page display price.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.payment-price.update') }}" class="admin-enroll-form">
                        @csrf
                        <div class="admin-enroll-grid">
                            <label class="admin-field">
                                <span>Current public display price</span>
                                <input type="text" value="₱{{ number_format($displayCoursePrice, 2) }}" class="admin-input" disabled>
                            </label>

                            <label class="admin-field">
                                <span>Current Xendit payment price</span>
                                <input
                                    type="number"
                                    name="payment_price"
                                    value="{{ old('payment_price', number_format($paymentCoursePrice, 2, '.', '')) }}"
                                    min="1"
                                    step="0.01"
                                    placeholder="1.00"
                                    class="admin-input @if($errors->has('payment_price')) admin-input-error @endif"
                                    required
                                >
                                @error('payment_price')
                                    <small class="admin-field-error">{{ $message }}</small>
                                @enderror
                            </label>
                        </div>

                        <div class="admin-enroll-actions">
                            <div class="admin-action-row">
                                <button type="submit" name="payment_price" value="1" class="admin-action-button admin-action-button-secondary">
                                    Set ₱1
                                </button>
                                <button type="submit" name="payment_price" value="2" class="admin-action-button admin-action-button-secondary">
                                    Set ₱2
                                </button>
                                <button type="submit" name="payment_price" value="599" class="admin-action-button admin-action-button-secondary">
                                    Restore ₱599
                                </button>
                            </div>

                            <div class="flex flex-col items-end gap-3">
                                <p class="admin-enroll-note">
                                    Save a custom test amount here, or use the quick buttons for one-click purchase testing.
                                </p>
                                <button type="submit" class="admin-action-button admin-action-button-primary">
                                    Save payment price
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

                <section
                    class="admin-panel mt-6"
                    data-admin-analytics
                    data-analytics-endpoint="{{ route('admin.analytics.summary') }}"
                >
                    <div class="admin-panel-head">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Realtime Analytics</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-foreground">See unique visits and live traffic</h3>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                                Track active visitors, today’s unique visits, page views, and the pages people are opening most often.
                            </p>
                        </div>
                        <div class="admin-chip admin-analytics-stamp" data-analytics-updated-at>
                            {{ $analytics['updated_at_label'] }}
                        </div>
                    </div>

                    <div class="admin-analytics-grid">
                        <article class="admin-metric-card admin-analytics-card">
                            <p class="admin-metric-label">Live visitors</p>
                            <h3 class="admin-metric-value" data-analytics-metric="live_visitors">{{ number_format($analytics['live_visitors']) }}</h3>
                            <p class="admin-metric-meta">Active in the last 5 minutes</p>
                        </article>

                        <article class="admin-metric-card admin-analytics-card">
                            <p class="admin-metric-label">Unique today</p>
                            <h3 class="admin-metric-value" data-analytics-metric="unique_visitors_today">{{ number_format($analytics['unique_visitors_today']) }}</h3>
                            <p class="admin-metric-meta">Distinct visitors since midnight</p>
                        </article>

                        <article class="admin-metric-card admin-analytics-card">
                            <p class="admin-metric-label">Page views today</p>
                            <h3 class="admin-metric-value" data-analytics-metric="page_views_today">{{ number_format($analytics['page_views_today']) }}</h3>
                            <p class="admin-metric-meta">Tracked page loads today</p>
                        </article>

                        <article class="admin-metric-card admin-analytics-card">
                            <p class="admin-metric-label">Unique visitors 7d</p>
                            <h3 class="admin-metric-value" data-analytics-metric="unique_visitors_7d">{{ number_format($analytics['unique_visitors_7d']) }}</h3>
                            <p class="admin-metric-meta">
                                {{ number_format($analytics['total_unique_visitors']) }} total unique visitors recorded
                            </p>
                        </article>
                    </div>

                    <div class="admin-panel-grid mt-6">
                        <article class="admin-panel admin-analytics-panel">
                            <div class="admin-panel-head">
                                <div>
                                    <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Top Pages</p>
                                    <h4 class="mt-2 text-xl font-semibold tracking-tight text-foreground">Most visited in the last 7 days</h4>
                                </div>
                            </div>

                            <div class="admin-table-wrap">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Page</th>
                                            <th>Views</th>
                                            <th>Unique Visitors</th>
                                        </tr>
                                    </thead>
                                    <tbody data-analytics-top-pages>
                                        @forelse ($analytics['top_pages'] as $page)
                                            <tr>
                                                <td>{{ $page['label'] }}</td>
                                                <td>{{ number_format($page['views']) }}</td>
                                                <td>{{ number_format($page['unique_visitors']) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="admin-empty-state">No analytics data yet. Visits will appear here once people start browsing the site.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </article>

                        <article class="admin-panel admin-analytics-panel">
                            <div class="admin-panel-head">
                                <div>
                                    <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Recent Activity</p>
                                    <h4 class="mt-2 text-xl font-semibold tracking-tight text-foreground">Latest tracked visits</h4>
                                </div>
                            </div>

                            <div class="admin-table-wrap">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Page</th>
                                            <th>Last Seen</th>
                                        </tr>
                                    </thead>
                                    <tbody data-analytics-recent-activity>
                                        @forelse ($analytics['recent_activity'] as $activity)
                                            <tr>
                                                <td>{{ $activity['label'] }}</td>
                                                <td>{{ $activity['viewed_at_label'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="admin-empty-state">No recent visitor activity yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="admin-panel mt-6">
                    <div class="admin-panel-head">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Manual Enrollment</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-foreground">Add or enroll a student manually</h3>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-muted-foreground">
                                Create the student account, unlock course access immediately, and email a temporary password for first login.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.students.enroll') }}" class="admin-enroll-form">
                        @csrf
                        <div class="admin-enroll-grid">
                            <label class="admin-field">
                                <span>First name</span>
                                <input
                                    type="text"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    placeholder="Juan"
                                    class="admin-input @if($errors->has('first_name')) admin-input-error @endif"
                                    required
                                >
                                @error('first_name')
                                    <small class="admin-field-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="admin-field">
                                <span>Last name</span>
                                <input
                                    type="text"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    placeholder="Dela Cruz"
                                    class="admin-input @if($errors->has('last_name')) admin-input-error @endif"
                                >
                                @error('last_name')
                                    <small class="admin-field-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="admin-field">
                                <span>Email</span>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="student@example.com"
                                    class="admin-input @if($errors->has('email')) admin-input-error @endif"
                                    required
                                >
                                @error('email')
                                    <small class="admin-field-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="admin-field">
                                <span>Username</span>
                                <input
                                    type="text"
                                    name="username"
                                    value="{{ old('username') }}"
                                    placeholder="studentusername"
                                    class="admin-input @if($errors->has('username')) admin-input-error @endif"
                                    required
                                >
                                @error('username')
                                    <small class="admin-field-error">{{ $message }}</small>
                                @enderror
                            </label>
                        </div>

                        <div class="admin-enroll-actions">
                            <p class="admin-enroll-note">
                                The student will appear in the purchased accounts list right away and receive a temporary password by email.
                            </p>
                            <button type="submit" class="admin-action-button admin-action-button-primary">
                                Enroll student now
                            </button>
                        </div>
                    </form>
                </section>

                <section class="admin-panel-grid">
                    <article class="admin-panel">
                        <div class="admin-panel-head">
                            <div>
                                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Pending Orders</p>
                                <h3 class="mt-2 text-2xl font-semibold tracking-tight text-foreground">Review and approve offline payments</h3>
                            </div>
                        </div>

                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pendingOrders as $order)
                                        <tr>
                                            <td>{{ $order->display_name }}</td>
                                            <td>{{ $order->email }}</td>
                                            <td>{{ $order->payment_method ?: 'N/A' }}</td>
                                            <td>₱{{ number_format((float) $order->amount, 2) }}</td>
                                            <td><span class="admin-status-pill">Pending</span></td>
                                            <td>
                                                <div class="admin-action-row">
                                                    <form method="POST" action="{{ route('admin.orders.approve', $order) }}">
                                                        @csrf
                                                        <button type="submit" class="admin-action-button admin-action-button-primary">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="admin-action-button admin-action-button-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="admin-empty-state">No pending orders right now.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <article class="admin-panel">
                        <div class="admin-panel-head">
                            <div>
                                <p class="text-sm font-medium uppercase tracking-[0.24em] text-primary">Enrolled Students</p>
                                <h3 class="mt-2 text-2xl font-semibold tracking-tight text-foreground">Purchased course accounts</h3>
                            </div>
                        </div>

                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Purchased</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $student)
                                        <tr>
                                            <td>{{ $student->first_name ?: $student->name }} {{ $student->last_name }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->username ?: '—' }}</td>
                                            <td>{{ optional($student->purchased_at)->format('M d, Y h:i A') ?: '—' }}</td>
                                            <td>{{ $student->xendit_reference ?: 'Manual access' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="admin-empty-state">No paid students yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>
            </section>
        </main>
    </body>
</html>

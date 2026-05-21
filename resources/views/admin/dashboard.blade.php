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

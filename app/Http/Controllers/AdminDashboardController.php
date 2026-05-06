<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $paidStatuses = ['paid', 'approved'];
        $paidOrders = Order::query()
            ->whereIn('status', $paidStatuses)
            ->latest('paid_at')
            ->latest()
            ->get();

        $pendingOrders = Order::query()
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.dashboard', [
            'adminEmail' => (string) $request->session()->get('admin_email'),
            'totalSales' => $paidOrders->sum(fn (Order $order) => (float) $order->amount),
            'totalStudents' => User::query()->whereNotNull('purchased_at')->count(),
            'paidOrdersCount' => $paidOrders->count(),
            'pendingOrdersCount' => $pendingOrders->count(),
            'students' => User::query()->whereNotNull('purchased_at')->latest('purchased_at')->get(),
            'pendingOrders' => $pendingOrders,
        ]);
    }

    public function approve(Request $request, Order $order): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        if ($order->status !== 'pending') {
            return back()->withErrors([
                'admin' => 'Only pending orders can be approved.',
            ]);
        }

        $user = $order->user;

        if ($user) {
            $user->forceFill([
                'course_slug' => $order->course_slug,
                'purchased_at' => $user->purchased_at ?? now(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        $order->forceFill([
            'status' => 'approved',
            'approved_at' => now(),
            'paid_at' => $order->paid_at ?? now(),
            'source' => 'manual',
        ])->save();

        return back()->with('admin_status', 'Pending order approved successfully.');
    }

    public function destroy(Request $request, Order $order): RedirectResponse
    {
        if ($redirect = $this->ensureAdmin($request)) {
            return $redirect;
        }

        $order->forceFill([
            'status' => 'deleted',
            'deleted_at' => now(),
        ])->save();

        if ($order->user && $order->user->purchased_at === null) {
            $hasActiveOrders = $order->user->orders()
                ->where('id', '!=', $order->id)
                ->whereNotIn('status', ['deleted'])
                ->exists();

            if (! $hasActiveOrders) {
                $order->user->delete();
            }
        }

        return back()->with('admin_status', 'Order removed from the pending list.');
    }

    private function ensureAdmin(Request $request): ?RedirectResponse
    {
        if (! (bool) $request->session()->get('admin_authenticated', false)) {
            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'login' => 'Please log in with your admin account first.',
                ]);
        }

        return null;
    }
}

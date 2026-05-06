<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if ($this->isAuthenticated(request())) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $allowedEmails = collect(config('admin.emails', []))
            ->map(fn (string $email) => strtolower(trim($email)))
            ->filter()
            ->values();

        $configuredHash = (string) config('admin.password_hash', '');
        $configuredPlain = (string) config('admin.password', '');

        if ($allowedEmails->isEmpty() || ($configuredHash === '' && $configuredPlain === '')) {
            return back()->withErrors([
                'login' => 'Admin access is not configured yet. Add your whitelisted admin email and password first.',
            ]);
        }

        $emailMatches = $allowedEmails->contains(strtolower($validated['email']));
        $passwordMatches = $configuredHash !== ''
            ? Hash::check($validated['password'], $configuredHash)
            : hash_equals($configuredPlain, $validated['password']);

        if (! $emailMatches || ! $passwordMatches) {
            return back()->withErrors([
                'login' => 'We could not log you in with those admin details.',
            ]);
        }

        $request->session()->put('admin_authenticated', true);
        $request->session()->put('admin_email', strtolower($validated['email']));
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'admin_authenticated',
            'admin_email',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function isAuthenticated(Request $request): bool
    {
        return (bool) $request->session()->get('admin_authenticated', false);
    }
}

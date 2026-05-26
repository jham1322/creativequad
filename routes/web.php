<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\XenditWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/analytics/summary', [AdminDashboardController::class, 'analyticsSummary'])->name('admin.analytics.summary');
Route::post('/admin/students/enroll', [AdminDashboardController::class, 'enroll'])->name('admin.students.enroll');
Route::post('/admin/orders/{order}/approve', [AdminDashboardController::class, 'approve'])->name('admin.orders.approve');
Route::delete('/admin/orders/{order}', [AdminDashboardController::class, 'destroy'])->name('admin.orders.destroy');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/lms/dashboard', [CheckoutController::class, 'dashboard'])->name('lms.dashboard');
Route::get('/lms/resources/laravel-shared-hosting-setup-prompt', [CheckoutController::class, 'resourcePrompt'])->name('lms.resources.laravel-setup-prompt');
Route::post('/lms/pending-payment/retry', [CheckoutController::class, 'retryPendingPayment'])->name('lms.pending-payment.retry');
Route::post('/webhooks/xendit/invoice', [XenditWebhookController::class, 'invoice'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class])
    ->name('webhooks.xendit.invoice');

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(): View
    {
        return view('checkout', [
            'coursePrice' => number_format((float) config('services.xendit.course_price', 599), 2),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'payment_method' => ['required', 'string', 'in:gcash,maya,grabpay,qrph'],
        ]);

        $secretKey = (string) config('services.xendit.secret_key');

        if ($secretKey === '') {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'checkout' => 'Xendit is not configured yet. Add your secret key first.',
                ]);
        }

        $price = (float) config('services.xendit.course_price', 599);
        $externalId = 'vibe-course-' . Str::uuid();
        $paymentMethodMap = [
            'gcash' => ['GCASH'],
            'maya' => ['PAYMAYA'],
            'grabpay' => ['GRABPAY'],
            'qrph' => ['QRPH'],
        ];

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->post(rtrim((string) config('services.xendit.base_url', 'https://api.xendit.co'), '/') . '/v2/invoices', [
                'external_id' => $externalId,
                'amount' => $price,
                'currency' => 'PHP',
                'description' => 'Build Real Full Stack Web Apps using AI and Codex',
                'invoice_duration' => 86400,
                'success_redirect_url' => route('lms.dashboard', ['reference' => $externalId, 'enrolled' => 1]),
                'failure_redirect_url' => route('checkout.failed', ['reference' => $externalId]),
                'customer' => [
                    'given_names' => $validated['first_name'],
                    'surname' => $validated['last_name'],
                    'email' => $validated['email'],
                ],
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_paid' => ['email'],
                ],
                'items' => [
                    [
                        'name' => 'Build Real Full Stack Web Apps using AI and Codex',
                        'quantity' => 1,
                        'price' => $price,
                        'category' => 'Course',
                        'url' => url('/'),
                    ],
                ],
                'payment_methods' => $paymentMethodMap[$validated['payment_method']],
                'metadata' => [
                    'username' => $validated['username'],
                    'payment_method' => $validated['payment_method'],
                ],
            ]);

        if ($response->failed() || ! $response->json('invoice_url')) {
            report('Xendit checkout creation failed: ' . $response->body());

            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'checkout' => 'We could not create your checkout session right now. Please try again in a moment.',
                ]);
        }

        return redirect()->away($response->json('invoice_url'));
    }

    public function success(Request $request): View
    {
        return redirect()->route('lms.dashboard', [
            'reference' => $request->string('reference')->value(),
            'enrolled' => 1,
        ]);
    }

    public function failed(Request $request): RedirectResponse
    {
        return redirect()
            ->route('checkout')
            ->withInput()
            ->withErrors([
                'checkout' => 'Checkout was not completed. You can review your details and try again.',
            ]);
    }
}

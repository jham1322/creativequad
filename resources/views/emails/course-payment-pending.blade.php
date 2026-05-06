<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Complete your payment</title>
    </head>
    <body style="margin:0;padding:0;background:#07131d;font-family:Inter,Arial,sans-serif;color:#f5f7ff;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#07131d;padding:32px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:linear-gradient(180deg,#0b1724 0%,#0a121c 100%);border:1px solid rgba(255,255,255,0.08);border-radius:28px;overflow:hidden;">
                        <tr>
                            <td style="padding:40px 40px 24px;background:radial-gradient(circle at top left, rgba(170,96,255,0.28), transparent 42%),radial-gradient(circle at top right, rgba(255,87,171,0.22), transparent 34%),linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0));">
                                <div style="display:inline-block;padding:8px 14px;border-radius:999px;border:1px solid rgba(255,255,255,0.12);background:rgba(10,18,28,0.55);font-size:12px;letter-spacing:0.24em;text-transform:uppercase;color:#c976ff;">
                                    Checkout created
                                </div>
                                <h1 style="margin:20px 0 0;font-size:42px;line-height:1.05;font-weight:800;color:#ffffff;">
                                    Complete your payment
                                </h1>
                                <p style="margin:18px 0 0;font-size:18px;line-height:1.7;color:rgba(245,247,255,0.82);">
                                    Hi {{ $mailData['name'] }}, your checkout for <strong style="color:#ffffff;">{{ $mailData['course_name'] }}</strong> is ready.
                                    Click the button below to finish your payment and unlock the course.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 40px 12px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 14px;">
                                    <tr>
                                        <td style="padding:18px 20px;border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                            <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Amount due</div>
                                            <div style="margin-top:10px;font-size:34px;line-height:1.1;font-weight:800;color:#ffffff;">PHP {{ $mailData['amount'] }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:18px 20px;border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                            <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Payment method</div>
                                            <div style="margin-top:10px;font-size:15px;line-height:1.7;color:rgba(245,247,255,0.82);">{{ $mailData['payment_method'] }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:18px 20px;border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                            <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Reference</div>
                                            <div style="margin-top:10px;font-size:15px;line-height:1.7;color:rgba(245,247,255,0.82);">{{ $mailData['reference'] }}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:8px 40px 24px;">
                                <a href="{{ $mailData['payment_url'] }}" style="display:inline-block;min-width:240px;padding:18px 28px;border-radius:999px;background:linear-gradient(90deg,#8a5cff 0%,#ff59b6 100%);box-shadow:0 16px 40px rgba(173,97,255,0.32);font-size:16px;font-weight:700;line-height:1;text-align:center;text-decoration:none;color:#ffffff;">
                                    Pay now
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 40px 40px;">
                                <p style="margin:0;font-size:14px;line-height:1.8;color:rgba(245,247,255,0.62);">
                                    Once payment is confirmed, we’ll send your paid access email automatically and direct you to the LMS dashboard.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>

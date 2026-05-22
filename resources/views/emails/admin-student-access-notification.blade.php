<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>New student access confirmed</title>
    </head>
    <body style="margin:0;padding:0;background:#07131d;font-family:Inter,Arial,sans-serif;color:#f5f7ff;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#07131d;padding:32px 16px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#08121c;border:1px solid rgba(255,255,255,0.08);border-radius:30px;overflow:hidden;box-shadow:0 24px 80px rgba(0,0,0,0.32);">
                        <tr>
                            <td style="padding:40px 40px 24px;background:
                                radial-gradient(circle at top left, rgba(146,86,255,0.34), transparent 38%),
                                radial-gradient(circle at 88% 16%, rgba(255,86,176,0.22), transparent 28%),
                                linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0));">
                                <img src="{{ asset('images/branding/logo.webp') }}" alt="Creative Quad" width="166" style="display:block;width:166px;max-width:100%;height:auto;border:0;">

                                <div style="margin-top:24px;display:inline-block;padding:9px 16px;border-radius:999px;border:1px solid rgba(255,255,255,0.12);background:rgba(11,20,34,0.72);font-size:12px;letter-spacing:0.24em;text-transform:uppercase;color:#c976ff;">
                                    Admin notification
                                </div>

                                <h1 style="margin:20px 0 0;font-size:38px;line-height:1.08;font-weight:800;letter-spacing:-0.03em;color:#ffffff;">
                                    A student now has course access
                                </h1>

                                <p style="margin:18px 0 0;font-size:17px;line-height:1.75;color:rgba(245,247,255,0.84);">
                                    A student has completed payment or was granted access and is now active inside the Creative Quad course.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:0 40px 18px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 14px;">
                                    <tr>
                                        <td style="padding:22px;border-radius:22px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.02);">
                                            <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Student</div>
                                            <div style="margin-top:10px;font-size:24px;font-weight:800;color:#ffffff;">{{ $mailData['name'] }}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:0 40px 18px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="50%" valign="top" style="padding-right:8px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                                <tr>
                                                    <td style="padding:18px 20px;">
                                                        <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Email</div>
                                                        <div style="margin-top:10px;font-size:15px;line-height:1.7;color:#ffffff;word-break:break-word;">{{ $mailData['email'] }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" valign="top" style="padding-left:8px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                                <tr>
                                                    <td style="padding:18px 20px;">
                                                        <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Payment method</div>
                                                        <div style="margin-top:10px;font-size:16px;font-weight:700;color:#ffffff;">{{ $mailData['payment_method'] }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:0 40px 18px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="50%" valign="top" style="padding-right:8px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                                <tr>
                                                    <td style="padding:18px 20px;">
                                                        <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Amount</div>
                                                        <div style="margin-top:10px;font-size:16px;font-weight:700;color:#ffffff;">PHP {{ $mailData['amount'] }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="50%" valign="top" style="padding-left:8px;">
                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid rgba(255,255,255,0.08);border-radius:20px;background:rgba(255,255,255,0.02);">
                                                <tr>
                                                    <td style="padding:18px 20px;">
                                                        <div style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#b56cff;">Reference</div>
                                                        <div style="margin-top:10px;font-size:14px;line-height:1.7;color:#ffffff;word-break:break-word;">{{ $mailData['reference'] }}</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:0 40px 40px;">
                                <a href="{{ $mailData['admin_url'] }}" style="display:inline-block;min-width:240px;padding:18px 30px;border-radius:999px;background:linear-gradient(90deg,#8a5cff 0%,#ff59b6 100%);box-shadow:0 16px 40px rgba(173,97,255,0.28);font-size:16px;font-weight:700;line-height:1;text-align:center;text-decoration:none;color:#ffffff;">
                                    Open admin dashboard
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>

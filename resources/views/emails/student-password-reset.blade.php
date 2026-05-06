<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reset your Vibe Coding password</title>
    </head>
    <body style="margin:0; background:#050b16; color:#e8eefb; font-family:Inter,Arial,sans-serif;">
        <div style="max-width:640px; margin:0 auto; padding:32px 20px;">
            <div style="border:1px solid rgba(186,109,255,0.28); border-radius:28px; overflow:hidden; background:linear-gradient(180deg,#121b29 0%,#09131d 100%); box-shadow:0 24px 80px rgba(0,0,0,0.35);">
                <div style="padding:28px 28px 12px; background:radial-gradient(circle at top left, rgba(184,120,255,0.24), transparent 48%), radial-gradient(circle at top right, rgba(255,85,170,0.18), transparent 42%);">
                    <div style="display:inline-flex; align-items:center; gap:10px; color:#c875ff; font-size:12px; letter-spacing:0.3em; text-transform:uppercase; font-weight:700;">
                        Password Reset
                    </div>
                    <h1 style="margin:18px 0 10px; font-size:34px; line-height:1.08; color:#f7f9fe;">
                        Set a new password for your student account
                    </h1>
                    <p style="margin:0; font-size:16px; line-height:1.7; color:rgba(235,242,255,0.82);">
                        Hi {{ $studentName }}, click the button below to create a new password and continue learning inside your purchased course dashboard.
                    </p>
                </div>

                <div style="padding:28px;">
                    <a href="{{ $resetUrl }}" style="display:inline-block; padding:16px 28px; border-radius:999px; text-decoration:none; color:#fff; font-weight:700; background:linear-gradient(135deg,#9e53ff 0%,#ff4fa8 100%); box-shadow:0 18px 40px rgba(191,94,255,0.32);">
                        Reset my password
                    </a>

                    <p style="margin:24px 0 0; font-size:15px; line-height:1.75; color:rgba(235,242,255,0.74);">
                        If the button does not work, copy and paste this link into your browser:
                    </p>
                    <p style="margin:10px 0 0; word-break:break-word;">
                        <a href="{{ $resetUrl }}" style="color:#ff8fce; text-decoration:none;">{{ $resetUrl }}</a>
                    </p>

                    <p style="margin:24px 0 0; font-size:14px; line-height:1.75; color:rgba(235,242,255,0.6);">
                        If you did not request this, you can safely ignore this email.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>

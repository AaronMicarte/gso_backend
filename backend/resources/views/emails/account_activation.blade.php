<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family: Arial, sans-serif; background:#f5f7fb; margin:0; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.06);">
        <tr>
            <td style="padding:20px 24px; background:#0f172a; color:#fff;">
                <div style="font-size:18px; font-weight:700;">GSO IAMS Account Activation</div>
                <div style="font-size:12px; color:#cbd5e1; margin-top:4px;">City Government of El Salvador</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px; color:#0f172a;">
                <p style="margin:0 0 12px; font-size:14px;">Hello {{ $fullName }},</p>
                <p style="margin:0 0 12px; font-size:14px; line-height:1.5;">
                    Your account has been created. Please activate your account using the button below.
                </p>

                <p style="margin:0 0 18px; font-size:14px;"><strong>Username:</strong> {{ $username }}</p>

                <p style="margin:0 0 18px;">
                    <a href="{{ $activationLink }}"
                       style="display:inline-block; padding:12px 16px; background:#2563eb; color:#ffffff; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px;">
                        Activate Account
                    </a>
                </p>

                <p style="margin:0 0 18px; font-size:12px; color:#475569; line-height:1.5;">
                    If the button doesn't work, copy and paste this link into your browser:<br>
                    <span style="word-break:break-all; color:#0f172a;">{{ $activationLink }}</span>
                </p>

                <p style="margin:0 0 8px; font-size:12px; color:#475569;">
                    This activation link expires at {{ optional($expiresAt)->format('Y-m-d H:i') }}.
                </p>

                <p style="margin:0; font-size:12px; color:#64748b; line-height:1.5;">
                    If you did not request this, please ignore this email.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 24px; background:#f8fafc; color:#64748b; font-size:11px;">
                This is an automated message. Please do not reply.
            </td>
        </tr>
    </table>
</body>
</html>

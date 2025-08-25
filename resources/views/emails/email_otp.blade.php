<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otp for Registeration</title>
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">
</head>
<body style="background-color: #1f2937; color: #e5e7eb; font-family: Arial, sans-serif; padding: 20px;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #111827; padding: 20px; border-radius: 8px;">
        <tr>
            <td align="center" style="padding-bottom: 20px;">
                <h2 style="color: #10b981; font-size: 24px; margin: 0;">Otp for Registeration</h2>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; color: #d1d5db;">
                <p>Hello,</p>
                <p>Your One-Time Registeration (OTP) is :</p>

                <p style="text-align: center; font-size: 32px; font-weight: bold; color: #3b82f6; margin: 20px 0;">
                    {{ $otp }}
                </p>

                <p>Please use this code to complete your registeration. This OTP will expire in 1 minute..😊😊😊..</p>
                <p>Thank you,<br>Your Application Team</p>
            </td>
        </tr>
    </table>

</body>
</html>

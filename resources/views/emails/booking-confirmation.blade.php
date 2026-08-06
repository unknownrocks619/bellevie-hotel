<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Confirmation — {{ $booking->booking_reference }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Georgia,'Times New Roman',serif;color:#333;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:32px 0;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;">

    <tr>
        <td style="background:#0D1B2A;padding:28px 32px;text-align:center;">
            <div style="color:#C9A227;font-size:.7rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;margin-bottom:4px;">
                Bellevie Hotel
            </div>
            <div style="color:#fff;font-size:1.3rem;">Booking Confirmed</div>
        </td>
    </tr>

    <tr>
        <td style="padding:32px;">
            <div style="font-size:1rem;line-height:1.7;white-space:pre-line;">{{ $bodyText }}</div>
        </td>
    </tr>

    <tr>
        <td style="background:#f8f9fa;padding:16px 32px;text-align:center;font-size:.75rem;color:#999;">
            &copy; {{ date('Y') }} Bellevie Hotel. All rights reserved.
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>

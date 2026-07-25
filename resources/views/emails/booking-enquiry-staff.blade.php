<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Booking Enquiry — {{ $booking->booking_reference }}</title>
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
            <div style="color:#fff;font-size:1.3rem;">Booking Enquiry Received</div>
        </td>
    </tr>

    <tr>
        <td style="padding:32px;">
            <p style="margin:0 0 16px;font-size:1rem;line-height:1.6;">
                Hello,
            </p>
            <p style="margin:0 0 24px;font-size:1rem;line-height:1.6;">
                A new booking enquiry has been submitted on the website. Details below:
            </p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="background:#f5f0e8;border-radius:8px;margin-bottom:24px;">
                <tr>
                    <td style="padding:20px 24px;">
                        <div style="font-size:.75rem;color:#666;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">
                            Enquiry Reference
                        </div>
                        <div style="font-size:1.4rem;font-weight:bold;color:#C9A227;letter-spacing:1px;font-family:'Courier New',monospace;">
                            {{ $booking->booking_reference }}
                        </div>
                    </td>
                </tr>
            </table>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;width:40%;">Guest</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">{{ $booking->guest_first_name }} {{ $booking->guest_last_name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Email</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;"><a href="mailto:{{ $booking->guest_email }}" style="color:#0D1B2A;">{{ $booking->guest_email }}</a></td>
                </tr>
                @if($booking->guest_phone)
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Phone</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">{{ $booking->guest_phone }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;border-top:1px solid #eee;">Room</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;border-top:1px solid #eee;">{{ $booking->room->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Check-in</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">{{ \Carbon\Carbon::parse($booking->check_in)->format('l, F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Check-out</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">{{ \Carbon\Carbon::parse($booking->check_out)->format('l, F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Nights</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">{{ $booking->nights }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Guests</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">
                        {{ $booking->adults }} Adult{{ $booking->adults > 1 ? 's' : '' }}
                        @if($booking->children > 0), {{ $booking->children }} Child{{ $booking->children > 1 ? 'ren' : '' }}@endif
                    </td>
                </tr>
                @if($booking->special_requests)
                <tr>
                    <td style="padding:6px 0;font-size:.9rem;color:#666;">Special Requests</td>
                    <td style="padding:6px 0;font-size:.9rem;font-weight:600;">{{ $booking->special_requests }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding:10px 0 6px;font-size:.95rem;color:#0D1B2A;border-top:1px solid #eee;font-weight:700;">Total</td>
                    <td style="padding:10px 0 6px;font-size:1.1rem;color:#C9A227;border-top:1px solid #eee;font-weight:700;">
                        ${{ number_format((float) $booking->total_amount, 2) }}
                    </td>
                </tr>
            </table>

            <div style="text-align:center;margin-bottom:24px;">
                <a href="{{ $confirmationUrl }}"
                   style="display:inline-block;background:#C9A227;color:#fff;text-decoration:none;
                          padding:12px 32px;border-radius:6px;font-size:.95rem;font-weight:600;">
                    View Enquiry Details
                </a>
            </div>

            <p style="margin:0 0 8px;font-size:.8rem;color:#999;text-align:center;">
                This link expires in 1 hour. Afterwards, look up this enquiry by its reference number
                in Admin → Bookings.
            </p>

            <p style="margin:24px 0 0;font-size:.9rem;line-height:1.6;">
                Please follow up with the guest to confirm availability and finalise the booking.
            </p>
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

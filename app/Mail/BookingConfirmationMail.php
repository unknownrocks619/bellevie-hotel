<?php
namespace App\Mail;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $emailSubject,
        public string $emailBody,
    ) {}

    public function envelope(): Envelope
    {
        // Send from the booking enquiry inbox, falling back to the general
        // hotel email, so guest replies land where staff actually check.
        $fromAddress = Setting::get('booking_enquiry_email') ?: Setting::get('hotel_email');
        $fromName    = Setting::get('mail_from_name') ?: Setting::get('hotel_name', 'Bellevie Hotel');

        return new Envelope(
            from: $fromAddress ? new Address($fromAddress, $fromName) : null,
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'bodyText' => $this->emailBody,
                'booking'  => $this->booking,
            ],
        );
    }
}

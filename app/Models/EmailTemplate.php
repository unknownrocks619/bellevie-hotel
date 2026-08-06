<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['key', 'name', 'subject', 'body'];

    /**
     * Shortcodes available for use in a template's subject/body, and a
     * human-readable description shown to admins in the editor.
     */
    public static array $shortcodes = [
        'booking_reference' => 'Booking reference number',
        'first_name'        => "Guest's first name",
        'last_name'         => "Guest's last name",
        'booking_date'      => 'Booking (check-in) date',
        'total_guest'       => 'Total number of guests',
        'room_name'         => 'Room name',
        'user'              => 'Currently logged in admin user\'s full name',
    ];

    /**
     * Build the shortcode replacement values for a given booking.
     */
    public static function dataForBooking(Booking $booking): array
    {
        return [
            'booking_reference' => $booking->booking_reference,
            'first_name'        => $booking->guest_first_name,
            'last_name'         => $booking->guest_last_name,
            'booking_date'      => optional($booking->check_in)->format('F d, Y'),
            'total_guest'       => $booking->adults + $booking->children,
            'room_name'         => $booking->room->name ?? '',
            'user'              => auth()->user()->name ?? 'Bellevie Hotel Team',
        ];
    }

    /**
     * Replace all [shortcode] occurrences in the given text with their values.
     */
    public static function replaceShortcodes(string $text, array $data): string
    {
        $search = [];
        $replace = [];
        foreach ($data as $key => $value) {
            $search[] = "[{$key}]";
            $replace[] = (string) $value;
        }
        return str_replace($search, $replace, $text);
    }

    /**
     * Render this template's subject/body against a booking, returning
     * ['subject' => ..., 'body' => ...] with shortcodes replaced.
     */
    public function renderForBooking(Booking $booking): array
    {
        $data = self::dataForBooking($booking);

        return [
            'subject' => self::replaceShortcodes($this->subject, $data),
            'body'    => self::replaceShortcodes($this->body, $data),
        ];
    }
}

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('subject');
            $table->text('body');
            $table->timestamps();
        });

        // Seed the default booking confirmation template so it's ready to use out of the box.
        DB::table('email_templates')->insert([
            'key'        => 'booking_confirmation',
            'name'       => 'Booking Confirmation',
            'subject'    => 'Your Booking is Confirmed — [booking_reference]',
            'body'       => "Dear [first_name] [last_name],\n\n"
                . "We are delighted to confirm your booking at Bellevie Hotel. Here are your booking details:\n\n"
                . "Booking Reference: [booking_reference]\n"
                . "Room: [room_name]\n"
                . "Date: [booking_date]\n"
                . "Total Guests: [total_guest]\n\n"
                . "We look forward to welcoming you and ensuring you have a comfortable and memorable stay.\n\n"
                . "If you have any questions before your arrival, please do not hesitate to contact us.\n\n"
                . "Warm regards,\n[user]\nBellevie Hotel",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

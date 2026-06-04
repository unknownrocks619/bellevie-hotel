<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\Testimonial;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ────────────────────────────────────────────
        User::create([
            'name'     => 'Admin',
            'email'    => env('ADMIN_EMAIL', 'admin@belleviehotel.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            'role'     => 'super_admin',
        ]);

        // ── Settings ──────────────────────────────────────────────
        Setting::set('hotel_name',        'Bellevie Hotel',          'general');
        Setting::set('hotel_tagline',     'Where Luxury Meets Serenity', 'general');
        Setting::set('hotel_description', 'Experience luxury at its finest at Bellevie Hotel. Located in the heart of Beverly Hills, we offer world-class amenities and exceptional service.', 'general');
        Setting::set('hotel_email',       'info@belleviehotel.com',  'general');
        Setting::set('hotel_phone',       '+1 (310) 555-0100',       'general');
        Setting::set('hotel_address',     '100 Grand Boulevard',     'general');
        Setting::set('hotel_city',        'Beverly Hills',           'general');
        Setting::set('hotel_country',     'USA',                     'general');
        Setting::set('tax_rate',          '10',                      'general');
        Setting::set('currency',          'USD',                     'general');
        Setting::set('currency_symbol',   '$',                       'general');
        Setting::set('check_in_time',     '15:00',                   'general');
        Setting::set('check_out_time',    '11:00',                   'general');
        Setting::set('facebook_url',      'https://facebook.com/belleviehotel',  'social');
        Setting::set('instagram_url',     'https://instagram.com/belleviehotel', 'social');
        Setting::set('twitter_url',       'https://twitter.com/belleviehotel',   'social');

        // ── Room Types ────────────────────────────────────────────
        $roomTypes = [
            ['name' => 'Standard',          'description' => 'Cozy and comfortable room perfect for a relaxing stay'],
            ['name' => 'Deluxe',            'description' => 'Spacious room with premium amenities and city views'],
            ['name' => 'Junior Suite',      'description' => 'Suite with separate living area and balcony'],
            ['name' => 'Executive Suite',   'description' => 'Luxurious suite with full amenities and services'],
            ['name' => 'Presidential Suite','description' => 'Our most exclusive accommodation with panoramic views'],
        ];

        foreach ($roomTypes as $type) {
            RoomType::create([
                'name'         => $type['name'],
                'slug'         => Str::slug($type['name']),
                'description'  => $type['description'],
                'max_adults'   => 2,
                'max_children' => 1,
                'is_active'    => true,
            ]);
        }

        // ── Amenities ─────────────────────────────────────────────
        $amenityData = [
            ['name' => 'Free WiFi',          'icon' => 'bi-wifi'],
            ['name' => 'Air Conditioning',   'icon' => 'bi-thermometer-snow'],
            ['name' => 'Flat Screen TV',     'icon' => 'bi-tv'],
            ['name' => 'Mini Bar',           'icon' => 'bi-cup-hot'],
            ['name' => 'Room Service',       'icon' => 'bi-bell'],
            ['name' => 'King Bed',           'icon' => 'bi-moon'],
            ['name' => 'City View',          'icon' => 'bi-buildings'],
            ['name' => 'Ocean View',         'icon' => 'bi-water'],
            ['name' => 'Private Balcony',    'icon' => 'bi-door-open'],
            ['name' => 'Jacuzzi',            'icon' => 'bi-droplet'],
            ['name' => 'Safe Box',           'icon' => 'bi-shield-lock'],
            ['name' => 'Coffee Maker',       'icon' => 'bi-cup'],
            ['name' => 'Bathrobe & Slippers','icon' => 'bi-person-standing'],
            ['name' => 'Butler Service',     'icon' => 'bi-person-badge'],
        ];

        foreach ($amenityData as $amenity) {
            Amenity::create($amenity);
        }

        // ── Rooms ─────────────────────────────────────────────────
        $roomsData = [
            [
                'room_type_name'  => 'Standard',
                'name'            => 'Classic Standard Room',
                'room_number'     => '101',
                'description'     => 'A cozy and comfortable standard room ideal for solo travelers and couples',
                'price_per_night' => 150,
                'size_sqft'       => 320,
                'bed_type'        => 'Queen',
                'max_adults'      => 2,
                'is_featured'     => false,
                'amenities'       => ['Free WiFi', 'Air Conditioning', 'Flat Screen TV', 'King Bed'],
            ],
            [
                'room_type_name'  => 'Deluxe',
                'name'            => 'Deluxe Garden View Room',
                'room_number'     => '201',
                'description'     => 'Spacious deluxe room with beautiful garden views and premium amenities',
                'price_per_night' => 230,
                'size_sqft'       => 420,
                'bed_type'        => 'King',
                'max_adults'      => 2,
                'is_featured'     => true,
                'amenities'       => ['Free WiFi', 'Air Conditioning', 'Flat Screen TV', 'Mini Bar', 'King Bed'],
            ],
            [
                'room_type_name'  => 'Deluxe',
                'name'            => 'Deluxe City View Room',
                'room_number'     => '202',
                'description'     => 'Modern deluxe room with stunning city views and upscale furnishings',
                'price_per_night' => 280,
                'size_sqft'       => 420,
                'bed_type'        => 'King',
                'max_adults'      => 2,
                'is_featured'     => true,
                'amenities'       => ['Free WiFi', 'Air Conditioning', 'Flat Screen TV', 'Mini Bar', 'City View', 'King Bed'],
            ],
            [
                'room_type_name'  => 'Junior Suite',
                'name'            => 'Junior Suite with Balcony',
                'room_number'     => '301',
                'description'     => 'Luxurious junior suite with separate living area and private balcony',
                'price_per_night' => 380,
                'size_sqft'       => 600,
                'bed_type'        => 'King',
                'max_adults'      => 3,
                'is_featured'     => true,
                'amenities'       => ['Free WiFi', 'Air Conditioning', 'Flat Screen TV', 'Mini Bar', 'Private Balcony', 'King Bed', 'Room Service'],
            ],
            [
                'room_type_name'  => 'Executive Suite',
                'name'            => 'Executive Suite',
                'room_number'     => '401',
                'description'     => 'Ultimate luxury suite with premium amenities, butler service, and ocean views',
                'price_per_night' => 580,
                'size_sqft'       => 900,
                'bed_type'        => 'King',
                'max_adults'      => 4,
                'view_type'       => 'Ocean View',
                'is_featured'     => true,
                'amenities'       => ['Free WiFi', 'Air Conditioning', 'Flat Screen TV', 'Mini Bar', 'Ocean View', 'Private Balcony', 'Jacuzzi', 'Butler Service'],
            ],
            [
                'room_type_name'  => 'Presidential Suite',
                'name'            => 'Presidential Suite',
                'room_number'     => '501',
                'description'     => 'Our most exclusive suite with panoramic views, private spa, and personal concierge',
                'price_per_night' => 1200,
                'size_sqft'       => 2000,
                'bed_type'        => 'King',
                'max_adults'      => 4,
                'view_type'       => 'Panoramic',
                'is_featured'     => true,
                'amenities'       => ['Free WiFi', 'Air Conditioning', 'Flat Screen TV', 'Mini Bar', 'Ocean View', 'Private Balcony', 'Jacuzzi', 'Safe Box', 'Butler Service', 'Bathrobe & Slippers'],
            ],
        ];

        foreach ($roomsData as $roomData) {
            // Extract non-DB fields before creating
            $roomTypeName = $roomData['room_type_name'];
            $amenityNames = $roomData['amenities'];
            unset($roomData['room_type_name'], $roomData['amenities']);

            $roomType = RoomType::where('name', $roomTypeName)->first();

            $room = Room::create(array_merge($roomData, [
                'room_type_id' => $roomType->id,
                'slug'         => Str::slug($roomData['name']),
                'is_active'    => true,
            ]));

            $amenityIds = Amenity::whereIn('name', $amenityNames)->pluck('id')->toArray();
            $room->amenities()->sync($amenityIds);
        }

        // ── Testimonials ──────────────────────────────────────────
        $testimonials = [
            [
                'guest_name'    => 'James Harrison',
                'guest_title'   => 'Business Executive',
                'guest_country' => 'United Kingdom',
                'content'       => 'Exceptional service and world-class amenities. Bellevie Hotel exceeded all my expectations during my business trip.',
                'rating'        => 5,
                'is_featured'   => true,
            ],
            [
                'guest_name'    => 'Sofia Andreou',
                'guest_title'   => 'Travel Blogger',
                'guest_country' => 'Greece',
                'content'       => 'The luxury and attention to detail at Bellevie Hotel is unmatched. Highly recommend for anyone seeking perfection.',
                'rating'        => 5,
                'is_featured'   => true,
            ],
            [
                'guest_name'    => 'Chen Wei',
                'guest_title'   => 'Corporate Director',
                'guest_country' => 'Singapore',
                'content'       => 'Outstanding experience from check-in to check-out. The staff was incredibly professional and accommodating.',
                'rating'        => 5,
                'is_featured'   => true,
            ],
            [
                'guest_name'    => 'Marie Dupont',
                'guest_title'   => 'Entrepreneur',
                'guest_country' => 'France',
                'content'       => 'A truly luxurious escape. Bellevie Hotel offers the perfect blend of elegance and comfort.',
                'rating'        => 5,
                'is_featured'   => true,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }

        // ── Blog Categories & Posts ───────────────────────────────
        $categories = [
            ['name' => 'Hotel News',        'slug' => 'hotel-news'],
            ['name' => 'Travel Tips',       'slug' => 'travel-tips'],
            ['name' => 'Dining & Lifestyle','slug' => 'dining-lifestyle'],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }

        $admin = User::first();

        $posts = [
            [
                'title'    => 'Introducing Our New Presidential Suite',
                'slug'     => 'introducing-new-presidential-suite',
                'content'  => 'We are thrilled to announce the opening of our brand new Presidential Suite. With panoramic views and unparalleled luxury, this is truly the pinnacle of our offerings.',
                'category' => 'Hotel News',
            ],
            [
                'title'    => 'Hidden Gems of Beverly Hills: A Local Guide',
                'slug'     => 'hidden-gems-beverly-hills',
                'content'  => 'Discover the best kept secrets of Beverly Hills. From exclusive dining experiences to luxury shopping, we have everything you need for an unforgettable stay.',
                'category' => 'Travel Tips',
            ],
            [
                'title'    => 'Relax and Rejuvenate: Our Spa Experience',
                'slug'     => 'spa-experience',
                'content'  => 'Indulge in our world-class spa services. From traditional massages to modern wellness treatments, our experienced therapists will ensure you leave feeling refreshed.',
                'category' => 'Dining & Lifestyle',
            ],
        ];

        foreach ($posts as $postData) {
            $category = BlogCategory::where('name', $postData['category'])->first();
            BlogPost::create([
                'user_id'          => $admin->id,
                'blog_category_id' => $category->id,
                'title'            => $postData['title'],
                'slug'             => $postData['slug'],
                'content'          => $postData['content'],
                'status'           => 'published',
                'published_at'     => now(),
                'is_featured'      => true,
            ]);
        }

        // ── Navigation Menus ──────────────────────────────────────
        $headerMenu  = Menu::create(['name' => 'Header Menu', 'location' => 'header']);
        $headerItems = [
            ['title' => 'Home',           'route_name' => 'home'],
            ['title' => 'Rooms & Suites', 'route_name' => 'rooms.index'],
            ['title' => 'Reservations',   'route_name' => 'booking.create'],
            ['title' => 'About',          'route_name' => 'about'],
            ['title' => 'Blog',           'route_name' => 'blog.index'],
            ['title' => 'Contact',        'route_name' => 'contact'],
        ];

        foreach ($headerItems as $item) {
            MenuItem::create(array_merge(['menu_id' => $headerMenu->id], $item));
        }

        $footerMenu  = Menu::create(['name' => 'Footer Menu', 'location' => 'footer']);
        $footerItems = [
            ['title' => 'Privacy Policy',     'url' => '#'],
            ['title' => 'Terms & Conditions', 'url' => '#'],
            ['title' => 'Cancellation Policy','url' => '#'],
        ];

        foreach ($footerItems as $item) {
            MenuItem::create(array_merge(['menu_id' => $footerMenu->id], $item));
        }

        // ── Default Pages ─────────────────────────────────────────
        $pagesData = [
            [
                'title'       => 'Privacy Policy',
                'slug'        => 'privacy-policy',
                'sort_order'  => 1,
                'content'     => '<h2>Privacy Policy</h2><p>At Bellevie Hotel, we are committed to protecting your personal information. This Privacy Policy explains how we collect, use, and safeguard your data when you visit our website or make a reservation.</p><h3>Information We Collect</h3><p>We collect information you provide directly to us when making a reservation, contacting us, or signing up for our newsletter. This includes your name, email address, phone number, and payment details.</p><h3>How We Use Your Information</h3><p>We use the information we collect to process your reservations, communicate with you about your stay, improve our services, and comply with legal obligations.</p><h3>Data Security</h3><p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, or disclosure.</p><h3>Contact Us</h3><p>If you have any questions about this Privacy Policy, please contact us at privacy@belleviehotel.com.</p>',
            ],
            [
                'title'       => 'Terms & Conditions',
                'slug'        => 'terms-and-conditions',
                'sort_order'  => 2,
                'content'     => '<h2>Terms &amp; Conditions</h2><p>By accessing and using the Bellevie Hotel website and services, you agree to be bound by these Terms and Conditions.</p><h3>Reservations</h3><p>All reservations are subject to availability. Rates are quoted per room per night and include applicable taxes unless otherwise stated.</p><h3>Check-in &amp; Check-out</h3><p>Standard check-in time is 3:00 PM and check-out is 11:00 AM. Early check-in and late check-out may be available upon request, subject to availability and additional charges.</p><h3>Cancellations</h3><p>Cancellation policies vary by rate and room type. Please refer to your booking confirmation for specific cancellation terms.</p><h3>Liability</h3><p>Bellevie Hotel shall not be liable for any indirect, incidental, or consequential damages arising from your use of our services.</p>',
            ],
            [
                'title'       => 'Cancellation Policy',
                'slug'        => 'cancellation-policy',
                'sort_order'  => 3,
                'content'     => '<h2>Cancellation Policy</h2><p>We understand that plans can change. Our cancellation policy is designed to be fair to both our guests and our hotel.</p><h3>Standard Cancellation</h3><p>Reservations may be cancelled free of charge up to 48 hours before the scheduled check-in date. Cancellations made within 48 hours of check-in will be charged one night\'s stay.</p><h3>Non-Refundable Rates</h3><p>Some promotional rates are non-refundable. These are clearly marked at the time of booking. By booking a non-refundable rate, you agree that no refund will be issued for cancellations.</p><h3>No-Show Policy</h3><p>Guests who do not check in on the scheduled arrival date without prior cancellation will be charged the full amount of their reservation.</p><h3>How to Cancel</h3><p>To cancel your reservation, please contact our reservations team at reservations@belleviehotel.com or call us at +1 (310) 555-0100 with your booking reference number.</p>',
            ],
        ];

        foreach ($pagesData as $pageData) {
            Page::create(array_merge($pageData, [
                'is_active'    => true,
                'show_in_nav'  => false,
                'meta_title'   => $pageData['title'] . ' | Bellevie Hotel',
                'meta_description' => 'Bellevie Hotel ' . $pageData['title'],
            ]));
        }
    }
}

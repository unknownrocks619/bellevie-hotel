<?php
namespace Database\Seeders;

use App\Models\ConferencePage;
use App\Models\Image;
use App\Models\RestaurantMenuCategory;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantPage;
use App\Services\ImageService;
use Illuminate\Database\Seeder;

class RestaurantConferenceSeeder extends Seeder
{
    private ImageService $images;

    public function __construct()
    {
        $this->images = new ImageService();
    }

    public function run(): void
    {
        $this->seedRestaurant();
        $this->seedConference();
    }

    /** Create (or reuse) an Image row pointing at a stable Unsplash CDN URL. */
    private function stockImage(string $slug, string $unsplashId, string $filename): Image
    {
        return Image::firstOrCreate(
            ['public_id' => "seed/{$slug}"],
            [
                'url'               => "https://images.unsplash.com/photo-{$unsplashId}?auto=format&fit=crop&w=1200&q=80",
                'url_thumb'         => "https://images.unsplash.com/photo-{$unsplashId}?auto=format&fit=crop&w=300&h=300&q=80",
                'original_filename' => $filename,
                'format'            => 'jpg',
                'resource_type'     => 'image',
                'source'            => 'seed',
            ]
        );
    }

    private function seedRestaurant(): void
    {
        $page = RestaurantPage::singleton();
        $page->update([
            'hero_title'    => 'The Bellevie Restaurant',
            'hero_subtitle' => 'Contemporary fine dining in the heart of Beverly Hills',
            'intro_title'   => 'A Culinary Journey',
            'description'   => '<p>Our award-winning kitchen blends locally sourced ingredients with classic technique to create dishes that celebrate both comfort and craft. Led by an internationally trained culinary team, the Bellevie Restaurant offers an intimate, elegant setting for breakfast, lunch, dinner and private celebrations.</p><p>From our signature tasting menu to seasonal à la carte favourites, every plate is designed to be an experience — paired with an extensive wine list curated by our in-house sommelier.</p>',
            'opening_hours' => "Breakfast: 6:30am – 10:30am\nLunch: 12:00pm – 3:00pm\nDinner: 6:00pm – 11:00pm\nOpen daily",
            'is_active'     => true,
            'meta_title'    => 'Restaurant | Bellevie Hotel',
            'meta_description' => 'Fine dining at Bellevie Hotel — explore our menu, featured dishes and opening hours.',
        ]);

        $hero = $this->stockImage('restaurant-hero', '1517248135467-4c7edcad34c4', 'restaurant-hero.jpg');
        $this->images->attach($page, $hero->id, 'featured');

        $categoriesData = [
            [
                'name'        => 'Starters',
                'description' => 'Light, vibrant plates to begin your meal.',
                'items' => [
                    ['name' => 'Heirloom Tomato Burrata', 'description' => 'Fresh burrata, heirloom tomatoes, basil oil, aged balsamic', 'price' => 16, 'tags' => 'Vegetarian', 'featured' => true, 'photo' => ['tomato-burrata', '1608897013039-887f21d8c804']],
                    ['name' => 'Seared Scallops', 'description' => 'Pan-seared scallops, cauliflower purée, brown butter', 'price' => 22, 'tags' => 'Gluten-Free', 'featured' => false, 'photo' => ['seared-scallops', '1559847844-5315695dadae']],
                    ['name' => 'Wild Mushroom Soup', 'description' => 'Roasted wild mushrooms, truffle cream, chive oil', 'price' => 14, 'tags' => 'Vegetarian', 'featured' => false, 'photo' => ['mushroom-soup', '1547592166-23ac45744acd']],
                    ['name' => 'Tuna Tartare', 'description' => 'Yellowfin tuna, avocado, sesame, citrus soy', 'price' => 19, 'tags' => 'Gluten-Free', 'featured' => true, 'photo' => ['tuna-tartare', '1553621042-f6e147245754']],
                ],
            ],
            [
                'name'        => 'Main Course',
                'description' => 'Signature dishes crafted with seasonal, locally sourced ingredients.',
                'items' => [
                    ['name' => 'Herb-Crusted Rack of Lamb', 'description' => 'New Zealand lamb, rosemary jus, roasted root vegetables', 'price' => 42, 'tags' => 'Gluten-Free', 'featured' => true, 'photo' => ['rack-of-lamb', '1544025162-d76694265947']],
                    ['name' => 'Pan-Seared Chilean Sea Bass', 'description' => 'Miso glaze, bok choy, jasmine rice', 'price' => 38, 'tags' => 'Gluten-Free', 'featured' => true, 'photo' => ['sea-bass', '1467003909585-2f8a72700288']],
                    ['name' => 'Wild Mushroom Risotto', 'description' => 'Arborio rice, wild mushrooms, parmesan, truffle oil', 'price' => 28, 'tags' => 'Vegetarian, Gluten-Free', 'featured' => false, 'photo' => ['mushroom-risotto', '1476124369491-e7addf5db371']],
                    ['name' => 'Prime Filet Mignon', 'description' => '8oz filet, potato gratin, red wine reduction', 'price' => 48, 'tags' => 'Gluten-Free', 'featured' => true, 'photo' => ['filet-mignon', '1600891964599-f61ba0e24092']],
                    ['name' => 'Roasted Half Chicken', 'description' => 'Free-range chicken, herb jus, seasonal vegetables', 'price' => 32, 'tags' => '', 'featured' => false, 'photo' => ['roasted-chicken', '1598103442097-8b74394b95c6']],
                ],
            ],
            [
                'name'        => 'Desserts',
                'description' => 'Sweet finishes crafted in-house by our pastry team.',
                'items' => [
                    ['name' => 'Dark Chocolate Fondant', 'description' => 'Molten chocolate cake, vanilla bean ice cream', 'price' => 14, 'tags' => 'Vegetarian', 'featured' => true, 'photo' => ['chocolate-fondant', '1606313564200-e75d5e30476c']],
                    ['name' => 'Classic Crème Brûlée', 'description' => 'Madagascar vanilla custard, caramelised sugar', 'price' => 12, 'tags' => 'Vegetarian, Gluten-Free', 'featured' => false, 'photo' => ['creme-brulee', '1470124182917-cc6e71b22ecc']],
                    ['name' => 'Seasonal Fruit Tart', 'description' => 'Almond frangipane, seasonal fruit, apricot glaze', 'price' => 12, 'tags' => 'Vegetarian', 'featured' => false, 'photo' => ['fruit-tart', '1488477181946-6428a0291777']],
                ],
            ],
            [
                'name'        => 'Beverages',
                'description' => 'Curated wines, craft cocktails and refreshments.',
                'items' => [
                    ['name' => 'Signature Old Fashioned', 'description' => 'Bourbon, bitters, orange zest', 'price' => 16, 'tags' => '', 'featured' => false, 'photo' => ['old-fashioned', '1470337458703-46ad1756a187']],
                    ['name' => 'House Red Wine', 'description' => 'Glass of our sommelier-selected red', 'price' => 14, 'tags' => 'Vegan, Gluten-Free', 'featured' => false, 'photo' => ['red-wine', '1510812431401-41d2bd2722f3']],
                    ['name' => 'Fresh Citrus Mocktail', 'description' => 'Orange, grapefruit, mint, soda', 'price' => 9, 'tags' => 'Vegan, Gluten-Free', 'featured' => false, 'photo' => ['citrus-mocktail', '1544145945-f90425340c7e']],
                ],
            ],
        ];

        $sort = 0;
        foreach ($categoriesData as $categoryData) {
            $category = RestaurantMenuCategory::create([
                'name'        => $categoryData['name'],
                'description' => $categoryData['description'],
                'sort_order'  => $sort++,
                'is_active'   => true,
            ]);

            $itemSort = 0;
            foreach ($categoryData['items'] as $itemData) {
                [$slug, $unsplashId] = $itemData['photo'];
                $img = $this->stockImage($slug, $unsplashId, $slug . '.jpg');

                $item = RestaurantMenuItem::create([
                    'category_id'  => $category->id,
                    'name'         => $itemData['name'],
                    'description'  => $itemData['description'],
                    'price'        => $itemData['price'],
                    'image_url'    => $img->url,
                    'dietary_tags' => $itemData['tags'] ?: null,
                    'is_featured'  => $itemData['featured'],
                    'is_active'    => true,
                    'sort_order'   => $itemSort++,
                ]);

                $this->images->attach($item, $img->id, 'featured');
            }
        }
    }

    private function seedConference(): void
    {
        $page = ConferencePage::singleton();
        $page->update([
            'hero_title'    => 'Conference Hall',
            'hero_subtitle' => 'Host meetings, conferences and celebrations in style',
            'description'   => '<p>The Bellevie Conference Hall offers a versatile, elegant space for corporate meetings, conferences, product launches and private celebrations. Featuring state-of-the-art audiovisual equipment, natural daylight, and a dedicated events team, we ensure every detail is handled with precision.</p><p>Our in-house catering team can design a menu tailored to your event, from working breakfasts to formal banquet dinners, and our flexible floor plan adapts to gatherings of any size.</p>',
            'capacity_text' => 'Up to 250 guests (theatre style) / 120 guests (banquet style)',
            'layout_text'   => 'Theatre, Boardroom, U-Shape, Classroom, Banquet',
            'is_active'     => true,
            'meta_title'    => 'Conference Hall | Bellevie Hotel',
            'meta_description' => 'Host your next conference or event at Bellevie Hotel — request a booking today.',
        ]);

        $hero = $this->stockImage('conference-hero', '1540575467063-178a50c2df87', 'conference-hero.jpg');
        $this->images->attach($page, $hero->id, 'featured');

        $galleryPhotos = [
            ['conference-gallery-1', '1505373877841-8d25f7d46678'],
            ['conference-gallery-2', '1511578314322-379afb476865'],
            ['conference-gallery-3', '1497366216548-37526070297c'],
            ['conference-gallery-4', '1552664730-d307ca884978'],
            ['conference-gallery-5', '1519167758481-83f550bb49b3'],
            ['conference-gallery-6', '1587825140708-dfaf72ae4b04'],
        ];

        $ids = [];
        foreach ($galleryPhotos as [$slug, $unsplashId]) {
            $img    = $this->stockImage($slug, $unsplashId, $slug . '.jpg');
            $ids[]  = $img->id;
        }
        $this->images->attachMany($page, $ids, 'gallery');
    }
}

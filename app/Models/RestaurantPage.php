<?php
namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Model;

class RestaurantPage extends Model
{
    use HasImages;

    protected $fillable = [
        'hero_title', 'hero_subtitle', 'intro_title', 'description', 'opening_hours',
        'is_active', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** There is only ever one restaurant page — always id 1, never deleted. */
    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'hero_title'    => 'The Restaurant',
            'hero_subtitle' => 'Fine dining at Bellevie Hotel',
            'is_active'     => true,
        ]);
    }
}

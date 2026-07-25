<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RestaurantMenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'show_price', 'image_url',
        'dietary_tags', 'is_featured', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'       => 'decimal:2',
            'show_price'  => 'boolean',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RestaurantMenuItem $item) {
            if (empty($item->slug)) {
                $item->slug = static::uniqueSlug($item->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuCategory::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Dietary tags as a trimmed array, e.g. ['Vegan', 'Gluten-Free']. */
    public function getDietaryTagsListAttribute(): array
    {
        if (empty($this->dietary_tags)) return [];
        return array_filter(array_map('trim', explode(',', $this->dietary_tags)));
    }
}

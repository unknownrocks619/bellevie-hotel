<?php
namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Room extends Model
{
    use HasImages;
    protected $fillable = [
        'room_type_id', 'name', 'slug', 'room_number', 'description', 'content',
        'price_per_night', 'weekend_price', 'size_sqft', 'max_adults', 'max_children',
        'bed_type', 'floor', 'view_type', 'featured_image', 'gallery_images',
        'is_featured', 'is_active', 'show_price', 'sort_order'
    ];

    protected function casts(): array
    {
        return [
            'gallery_images'  => 'array',
            'is_featured'     => 'boolean',
            'is_active'       => 'boolean',
            'show_price'      => 'boolean',
            'price_per_night' => 'decimal:2',
            'weekend_price'   => 'decimal:2',
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_room');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isAvailable($checkIn, $checkOut): bool
    {
        return !$this->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

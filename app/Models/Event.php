<?php

namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasImages;

    protected $fillable = [
        'title', 'slug', 'type', 'excerpt', 'description',
        'starts_at', 'ends_at', 'venue', 'organizer', 'capacity', 'price',
        'cta_text', 'cta_url', 'image_url',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'   => 'datetime',
            'ends_at'     => 'datetime',
            'price'       => 'decimal:2',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    public const TYPES = [
        'event'      => 'Event',
        'conference' => 'Conference',
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = static::uniqueSlug($event->title);
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('starts_at')->orWhere('starts_at', '>=', now()->startOfDay());
        });
    }

    /** Human-readable type label. */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /** Formatted date range, e.g. "12 Aug 2026" or "12 – 14 Aug 2026". */
    public function getDateRangeAttribute(): string
    {
        if (!$this->starts_at) {
            return '';
        }
        if ($this->ends_at && !$this->ends_at->isSameDay($this->starts_at)) {
            return $this->starts_at->format('d M Y') . ' – ' . $this->ends_at->format('d M Y');
        }
        return $this->starts_at->format('d M Y');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

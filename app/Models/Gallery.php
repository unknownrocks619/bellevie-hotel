<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gallery extends Model
{
    protected $table = 'galleries';

    protected $fillable = [
        'title', 'description', 'category', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    /**
     * The single image linked to this gallery item via the image_relations pivot.
     * Eager-load with: Gallery::with('imageRelation.image')
     */
    public function imageRelation(): HasOne
    {
        return $this->hasOne(ImageRelation::class, 'relation_id')
                    ->where('relation', 'galleries')
                    ->where('type', 'primary')
                    ->with('image')
                    ->oldest('sort_order');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Full-size image URL via the pivot, or empty string. */
    public function getImageUrlAttribute(): string
    {
        return $this->imageRelation?->image?->url ?? '';
    }

    /** Thumbnail URL (200×200 auto-crop) or fall back to full URL. */
    public function getImageThumbAttribute(): string
    {
        return $this->imageRelation?->image?->url_thumb
            ?: $this->imageRelation?->image?->url
            ?: '';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}

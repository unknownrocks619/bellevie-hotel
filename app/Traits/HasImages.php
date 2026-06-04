<?php
namespace App\Traits;

use App\Models\Image;
use App\Models\ImageRelation;

trait HasImages
{
    public function images(string $type = null)
    {
        return ImageRelation::forModel($this, $type)->pluck('image')->filter()->values();
    }

    public function featuredImage(): ?Image
    {
        return ImageRelation::firstForModel($this, 'featured');
    }

    public function galleryImages(): \Illuminate\Support\Collection
    {
        return ImageRelation::forModel($this, 'gallery')->pluck('image')->filter()->values();
    }

    /**
     * Return the featured image URL, falling back to $this->featured_image column, then $default.
     */
    public function featuredImageUrl(string $default = ''): string
    {
        $img = $this->featuredImage();
        if ($img) return $img->url;
        return $this->featured_image ?? $default;
    }
}

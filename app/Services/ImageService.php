<?php
namespace App\Services;

use App\Models\Image;
use App\Models\ImageRelation;
use Illuminate\Database\Eloquent\Model;

class ImageService
{
    /**
     * Resolve an image ID submitted from the picker into an Image model.
     */
    public function find(?int $id): ?Image
    {
        return $id ? Image::find($id) : null;
    }

    /**
     * Attach a single image (by ID) to a model as a given type.
     * Replaces any existing relation of that type.
     */
    public function attach(Model $model, ?int $imageId, string $type = 'featured'): ?Image
    {
        if (!$imageId) return null;
        $image = Image::find($imageId);
        if (!$image) return null;
        ImageRelation::attachTo($model, $image, $type, true);
        return $image;
    }

    /**
     * Attach multiple images (by IDs array) to a model as a given type.
     * Replaces all existing relations of that type.
     */
    public function attachMany(Model $model, array $imageIds, string $type = 'gallery'): void
    {
        if (empty($imageIds)) return;
        ImageRelation::detachFrom($model, $type);
        foreach ($imageIds as $order => $id) {
            $image = Image::find($id);
            if ($image) {
                ImageRelation::attachTo($model, $image, $type, false, ['sort_order' => $order]);
            }
        }
    }

    /**
     * Get the first image of a given type for a model.
     */
    public function first(Model $model, string $type = 'featured'): ?Image
    {
        return ImageRelation::firstForModel($model, $type);
    }

    /**
     * Get all images of a given type for a model.
     */
    public function all(Model $model, string $type): \Illuminate\Support\Collection
    {
        return ImageRelation::forModel($model, $type)->pluck('image')->filter()->values();
    }

    /**
     * Get the URL of the first image of a given type, with an optional fallback.
     */
    public function url(Model $model, string $type = 'featured', string $fallback = ''): string
    {
        $image = $this->first($model, $type);
        return $image?->url ?? $fallback;
    }

    /**
     * Detach all images of a given type from a model.
     */
    public function detach(Model $model, ?string $type = null): void
    {
        ImageRelation::detachFrom($model, $type);
    }
}

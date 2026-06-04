<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageRelation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image_id',
        'relation',
        'relation_id',
        'type',
        'title',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /**
     * Get all images for a given model + type.
     * Usage: ImageRelation::forModel($room, 'featured')
     */
    public static function forModel(Model $model, ?string $type = null)
    {
        $q = static::with('image')
            ->where('relation', $model->getTable())
            ->where('relation_id', $model->getKey());

        if ($type) {
            $q->where('type', $type);
        }

        return $q->orderBy('sort_order')->get();
    }

    /**
     * Get the first image for a given model + type.
     */
    public static function firstForModel(Model $model, string $type = 'featured'): ?Image
    {
        $rel = static::with('image')
            ->where('relation', $model->getTable())
            ->where('relation_id', $model->getKey())
            ->where('type', $type)
            ->orderBy('sort_order')
            ->first();

        return $rel?->image;
    }

    /**
     * Attach an image to a model, replacing any existing record of the same type
     * if $replaceExisting is true.
     */
    public static function attachTo(
        Model $model,
        Image $image,
        string $type = 'featured',
        bool $replaceExisting = true,
        array $extra = []
    ): self {
        if ($replaceExisting) {
            static::where('relation', $model->getTable())
                ->where('relation_id', $model->getKey())
                ->where('type', $type)
                ->delete();
        }

        return static::create(array_merge([
            'image_id'   => $image->id,
            'relation'   => $model->getTable(),
            'relation_id'=> $model->getKey(),
            'type'       => $type,
            'sort_order' => 0,
        ], $extra));
    }

    /**
     * Detach all images of a given type from a model.
     */
    public static function detachFrom(Model $model, ?string $type = null): void
    {
        $q = static::where('relation', $model->getTable())
            ->where('relation_id', $model->getKey());

        if ($type) {
            $q->where('type', $type);
        }

        $q->delete();
    }
}

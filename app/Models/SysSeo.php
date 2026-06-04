<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SysSeo extends Model
{
    protected $table      = 'sys_seo';
    protected $primaryKey = 'id_seo';

    protected $fillable = [
        'relation_table',
        'relation_class',
        'relation_id',
        'title_seo',
        'description_seo',
        'tags_seo',
        'feature_image_seo',
    ];

    /**
     * Find the SEO record for a given model instance.
     */
    public static function forModel(Model $model): ?self
    {
        return static::where('relation_table', $model->getTable())
            ->where('relation_id', $model->getKey())
            ->first();
    }

    /**
     * Update-or-create SEO for a given model.
     */
    public static function saveFor(Model $model, array $data): self
    {
        return static::updateOrCreate(
            [
                'relation_table' => $model->getTable(),
                'relation_id'    => $model->getKey(),
            ],
            array_merge($data, [
                'relation_class' => get_class($model),
            ])
        );
    }

    /**
     * Delete/clear the feature image reference.
     * If it is a numeric ID we leave the Image record itself intact (shared library).
     * If it is a legacy local path URL we delete the file from storage.
     */
    public function deleteImage(): void
    {
        if (!$this->feature_image_seo) return;

        // Legacy local URL — delete the physical file
        if (!is_numeric($this->feature_image_seo) && str_contains($this->feature_image_seo, 'storage/')) {
            $path = str_replace(asset('storage/') . '/', '', $this->feature_image_seo);
            Storage::disk('public')->delete($path);
        }
        // If numeric (image library ID) we simply clear the reference; the Image stays in the library
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_id',
        'version',
        'url',
        'url_thumb',
        'original_filename',
        'format',
        'resource_type',
        'width',
        'height',
        'bytes',
        'folder',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'width'    => 'integer',
        'height'   => 'integer',
        'bytes'    => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function relations()
    {
        return $this->hasMany(ImageRelation::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /** Human-readable file size, e.g. "245 KB" */
    public function getFileSizeAttribute(): string
    {
        if (!$this->bytes) return '—';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($this->bytes, 1024));
        return round($this->bytes / pow(1024, $i), 1) . ' ' . $units[$i];
    }

    /** Thumbnail URL — falls back to full URL if thumb not generated */
    public function getThumbAttribute(): string
    {
        return $this->url_thumb ?: $this->url;
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    /**
     * Build a cloudinary transformation URL for a given size.
     * e.g. Image::transformUrl($image->public_id, 400, 300)
     */
    public static function transformUrl(string $publicId, int $w, int $h): string
    {
        $cloud = config('cloudinary.cloud_name') ?: str_after(config('cloudinary.url', ''), '@');
        if (!$cloud) return '';
        return "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,w_{$w},h_{$h}/{$publicId}";
    }

    /**
     * Create or retrieve an Image record from a Cloudinary widget callback payload.
     * The $info array is the `result.info` object from the JS callback.
     */
    public static function fromCloudinaryInfo(array $info): self
    {
        return static::firstOrCreate(
            ['public_id' => $info['public_id']],
            [
                'version'           => $info['version'] ?? null,
                'url'               => $info['secure_url'],
                'url_thumb'         => static::buildThumb($info),
                'original_filename' => $info['original_filename'] ?? basename($info['secure_url']),
                'format'            => $info['format'] ?? null,
                'resource_type'     => $info['resource_type'] ?? 'image',
                'width'             => $info['width'] ?? null,
                'height'            => $info['height'] ?? null,
                'bytes'             => $info['bytes'] ?? null,
                'folder'            => $info['folder'] ?? null,
                'source'            => 'cloudinary',
                'metadata'          => $info,
            ]
        );
    }

    private static function buildThumb(array $info): ?string
    {
        if (empty($info['public_id'])) return null;
        $cloud = config('cloudinary.cloud_name') ?: str_after(config('cloudinary.url', ''), '@');
        if (!$cloud) return null;
        return "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,w_200,h_200/{$info['public_id']}";
    }
}

<?php
namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Model;

class ConferencePage extends Model
{
    use HasImages;

    protected $fillable = [
        'hero_title', 'hero_subtitle', 'description', 'capacity_text', 'layout_text',
        'is_active', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** There is only ever one conference page — always id 1, never deleted. */
    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'hero_title'    => 'Conference Hall',
            'hero_subtitle' => 'Host your next event at Bellevie Hotel',
            'is_active'     => true,
        ]);
    }
}

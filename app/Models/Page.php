<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'featured_image', 'meta_title',
        'meta_description', 'is_active', 'show_in_nav', 'sort_order',
        'builder_data', 'use_builder',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'show_in_nav'  => 'boolean',
            'use_builder'  => 'boolean',
            'builder_data' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

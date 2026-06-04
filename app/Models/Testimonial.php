<?php
namespace App\Models;

use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasImages;
    protected $fillable = [
        'guest_name', 'guest_title', 'guest_country', 'guest_avatar',
        'content', 'rating', 'is_active', 'is_featured', 'sort_order'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}

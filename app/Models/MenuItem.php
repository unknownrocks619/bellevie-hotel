<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'parent_id', 'title', 'url', 'route_name', 'icon', 'target', 'sort_order', 'is_active', 'link_type', 'link_type_ref_id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function getUrlAttribute(?string $url = null): string
    {
        if ($this->route_name && Route::has($this->route_name)) {    
            return route($this->route_name);
        }
        
        return $this->url ?? ($url ?? '');
    }

    public function getRouteNameAttribute(?string $routeName = null) {
        if($routeName && Route::has($routeName) ) {
            return route($routeName);
        }

        return $routeName;
    }

    public function menuLink() {
        if($this->link_type) {
            return match($this->link_type_ref_id) {
                'page'  => '/page/'.(Page::where('is_active','=',1)->where('id','=', $this->link_type_ref_id)->first()?->slug ?? ''),
                'blog'  => '/blog/'.(BlogPost::find($this->link_ref_id)?->slug ?? ''),
                'blog-category' => '/blog/category/'.(BlogCategory::find($request->link_ref_id)?->slug ?? ''),
                'rooms' => '/rooms',
                'single-room'   => '/rooms/'. (Room::find($request->link_ref_id)?->slug ?? ''),
                default => $this->url
            };

        } else if ($this->route_name && Route::has($this->route_name) ) {
            return route($this->route_name);
        }


        return $this->route_name;
        
    }
}

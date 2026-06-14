<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Page;
use App\Models\SysSeo;

class PageController extends Controller
{
    public function show(string $page)
    {
        $page = Page::where('is_active','=',1)->where('slug','=',$page)->firstOrFail();
        if (!$page->is_active) {
            abort(404);
        }

        // SEO data
        $seo            = SysSeo::forModel($page);
        $seoTitle       = $seo?->title_seo       ?: $page->title;
        $seoDescription = $seo?->description_seo  ?: '';
        $seoImage       = '';
        if ($seo?->feature_image_seo) {
            if (is_numeric($seo->feature_image_seo)) {
                $img = Image::find($seo->feature_image_seo);
                $seoImage = $img?->url ?? '';
            } else {
                $seoImage = $seo->feature_image_seo;
            }
        }

        return view('frontend.page', compact('page', 'seoTitle', 'seoDescription', 'seoImage'));
    }
}

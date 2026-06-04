<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SysSeo;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * Remove the feature image from an SEO record.
     */
    public function removeImage(SysSeo $seo)
    {
        $seo->deleteImage();
        $seo->update(['feature_image_seo' => null]);

        return back()->with('success', 'SEO image removed.');
    }
}

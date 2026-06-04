<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\ImageRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * GET /admin/images
     * Returns paginated image library as JSON (used by the picker modal).
     */
    public function index(Request $request)
    {
        $query = Image::orderByDesc('created_at');

        if ($search = $request->search) {
            $query->where('original_filename', 'like', "%{$search}%");
        }

        if ($type = $request->resource_type) {
            $query->where('resource_type', $type);
        }

        $images = $query->paginate(24);

        return response()->json($images);
    }

    /**
     * POST /admin/images/save
     * Called by the Cloudinary widget JS callback after a successful upload.
     * Saves the Cloudinary result info to the images table.
     */
    public function save(Request $request)
    {
        $request->validate([
            'public_id'         => 'required|string',
            'secure_url'        => 'required|url',
            'original_filename' => 'nullable|string',
        ]);

        // Pass only Cloudinary fields — strip Laravel internal keys (_token, etc.)
        $image = Image::fromCloudinaryInfo($request->except(['_token', '_method']));

        return response()->json([
            'id'        => $image->id,
            'url'       => $image->url,
            'thumb'     => $image->thumb,
            'filename'  => $image->original_filename,
            'public_id' => $image->public_id,
            'width'     => $image->width,
            'height'    => $image->height,
        ]);
    }

    /**
     * DELETE /admin/images/{image}
     * Soft-deletes the image and optionally destroys it from Cloudinary.
     */
    public function destroy(Image $image)
    {
        // Attempt to delete from Cloudinary
        if ($image->public_id && $image->source === 'cloudinary') {
            try {
                $cloudinary = app(\Cloudinary\Cloudinary::class);
                $cloudinary->uploadApi()->destroy($image->public_id);
            } catch (\Exception $e) {
                // Log but don't block deletion
                \Log::warning("Cloudinary delete failed for {$image->public_id}: " . $e->getMessage());
            }
        }

        // Also remove from local storage if applicable
        if ($image->source === 'local' && $image->public_id) {
            Storage::disk('public')->delete($image->public_id);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }
}

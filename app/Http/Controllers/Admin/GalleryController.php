<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Image;
use App\Models\ImageRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with('imageRelation.image')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $galleries  = $query->paginate(24)->appends($request->only('category', 'search'));
        $categories = Gallery::distinct()->orderBy('category')->whereNotNull('category')->pluck('category');

        return view('admin.gallery.index', compact('galleries', 'categories'));
    }

    public function create()
    {
        $categories = Gallery::distinct()->whereNotNull('category')->pluck('category');
        return view('admin.gallery.create', compact('categories'));
    }

    /**
     * Called after Cloudinary upload widget closes.
     * Receives image_ids[] (already saved in images table) + category/title metadata.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image_ids'   => 'required|array|min:1',
            'image_ids.*' => 'exists:images,id',
            'category'    => 'required|string|max:100',
            'title'       => 'nullable|string|max:255',
        ]);

        $nextOrder = (Gallery::max('sort_order') ?? 0) + 1;
        $count     = 0;

        foreach ($request->image_ids as $imageId) {
            $img = Image::find($imageId);
            if (!$img) continue;

            // Skip if this image is already linked to a gallery entry
            $alreadyLinked = ImageRelation::where('image_id', $imageId)
                ->where('relation', 'galleries')
                ->where('type', 'primary')
                ->exists();
            if ($alreadyLinked) continue;

            $gallery = Gallery::create([
                'title'      => $request->title ?: $img->original_filename,
                'category'   => $request->category,
                'sort_order' => $nextOrder++,
                'is_active'  => true,
            ]);

            ImageRelation::create([
                'image_id'    => $img->id,
                'relation'    => 'galleries',
                'relation_id' => $gallery->id,
                'type'        => 'primary',
                'sort_order'  => 0,
            ]);

            $count++;
        }

        return redirect()->route('admin.gallery.index')
            ->with('success', $count . ' image(s) added to gallery.');
    }

    public function show(Gallery $gallery)
    {
        return view('admin.gallery.show', compact('gallery'));
    }

    public function edit(Gallery $gallery)
    {
        $categories = Gallery::distinct()->whereNotNull('category')->pluck('category');
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $gallery->update([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'sort_order'  => $request->input('sort_order', $gallery->sort_order),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery item updated.');
    }

    public function destroy(Gallery $gallery)
    {
        // Detach image relations (images stay in the images library)
        ImageRelation::where('relation', 'galleries')
            ->where('relation_id', $gallery->id)
            ->delete();

        $gallery->delete();
        return redirect()->route('admin.gallery.index')->with('success', 'Image removed from gallery.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:galleries,id']);

        $galleries = Gallery::whereIn('id', $request->ids)->get();

        foreach ($galleries as $gallery) {
            ImageRelation::where('relation', 'galleries')
                ->where('relation_id', $gallery->id)
                ->delete();
            $gallery->delete();
        }

        return redirect()->route('admin.gallery.index')
            ->with('success', $galleries->count() . ' item(s) removed from gallery.');
    }

    /**
     * POST /admin/gallery/reorder
     * Bulk-update sort_order via drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items'      => 'required|array',
            'items.*.id' => 'required|integer|exists:galleries,id',
        ]);

        foreach ($request->items as $i => $row) {
            Gallery::where('id', $row['id'])->update(['sort_order' => $i]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * GET /admin/gallery/cloudinary-library
     * Lists all assets from Cloudinary Admin API, flagging which are already in the gallery.
     */
    public function cloudinaryLibrary(Request $request)
    {
        try {
            $cloudinary = new \Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key'    => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            $options = ['resource_type' => 'image', 'max_results' => 50, 'type' => 'upload'];
            if ($request->filled('next_cursor')) {
                $options['next_cursor'] = $request->next_cursor;
            }

            $result    = $cloudinary->adminApi()->assets($options);
            $resources = $result['resources'] ?? [];
            $cloud     = config('cloudinary.cloud_name');

            // Find which public_ids are already linked to a gallery entry
            $publicIds     = collect($resources)->pluck('public_id')->filter()->values();
            $imagesByPubId = Image::whereIn('public_id', $publicIds)->pluck('id', 'public_id');

            $linkedImageIds = ImageRelation::where('relation', 'galleries')
                ->where('type', 'primary')
                ->whereIn('image_id', $imagesByPubId->values())
                ->pluck('image_id')
                ->flip(); // flip so we can use ->has()

            $items = collect($resources)->map(function ($r) use ($imagesByPubId, $linkedImageIds, $cloud) {
                $pid      = $r['public_id'];
                $thumbUrl = "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,w_300,h_220,q_auto,f_auto/{$pid}";
                $fullUrl  = $r['secure_url'] ?? "https://res.cloudinary.com/{$cloud}/image/upload/{$pid}";
                $imgId    = $imagesByPubId->get($pid);
                return [
                    'public_id'  => $pid,
                    'url'        => $fullUrl,
                    'thumb_url'  => $thumbUrl,
                    'filename'   => basename($pid),
                    'format'     => $r['format'] ?? '',
                    'width'      => $r['width'] ?? null,
                    'height'     => $r['height'] ?? null,
                    'bytes'      => $r['bytes'] ?? null,
                    'created_at' => $r['created_at'] ?? null,
                    'in_gallery' => $imgId && $linkedImageIds->has($imgId),
                ];
            });

            return response()->json([
                'items'       => $items,
                'next_cursor' => $result['next_cursor'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /admin/gallery/import
     * Imports a Cloudinary image (by public_id) into the gallery via the pivot.
     */
    public function importToGallery(Request $request)
    {
        $request->validate([
            'public_id' => 'required|string',
            'url'       => 'required|url',
            'filename'  => 'nullable|string',
            'category'  => 'required|string|max:100',
            'title'     => 'nullable|string|max:255',
        ]);

        // Find or create the Image record
        $image = Image::firstOrCreate(
            ['public_id' => $request->public_id],
            [
                'url'               => $request->url,
                'url_thumb'         => $this->buildThumbUrl($request->public_id),
                'original_filename' => $request->filename ?: basename($request->public_id),
                'source'            => 'cloudinary',
            ]
        );

        // Check if already linked to a gallery entry
        $alreadyLinked = ImageRelation::where('image_id', $image->id)
            ->where('relation', 'galleries')
            ->where('type', 'primary')
            ->exists();

        if ($alreadyLinked) {
            return response()->json(['already_exists' => true, 'message' => 'This image is already in the gallery.']);
        }

        $gallery = Gallery::create([
            'title'      => $request->title ?: $image->original_filename,
            'category'   => $request->category,
            'sort_order' => (Gallery::max('sort_order') ?? 0) + 1,
            'is_active'  => true,
        ]);

        ImageRelation::create([
            'image_id'    => $image->id,
            'relation'    => 'galleries',
            'relation_id' => $gallery->id,
            'type'        => 'primary',
            'sort_order'  => 0,
        ]);

        return response()->json(['already_exists' => false, 'gallery_id' => $gallery->id, 'message' => 'Added to gallery.']);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function buildThumbUrl(string $publicId): string
    {
        $cloud = config('cloudinary.cloud_name');
        if (!$cloud) return '';
        return "https://res.cloudinary.com/{$cloud}/image/upload/c_fill,w_200,h_200,q_auto,f_auto/{$publicId}";
    }
}

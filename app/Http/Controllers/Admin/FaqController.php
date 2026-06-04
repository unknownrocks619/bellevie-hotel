<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::orderBy('category')->orderBy('sort_order')->orderBy('title');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $faqs       = $query->paginate(20)->appends($request->only('category', 'search'));
        $categories = Faq::CATEGORIES;

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create()
    {
        $categories = Faq::CATEGORIES;
        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:500',
            'description' => 'required|string',
            'category'    => 'required|in:' . implode(',', array_keys(Faq::CATEGORIES)),
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        Faq::create([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => $request->input('sort_order', 0),
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully.');
    }

    public function edit(Faq $faq)
    {
        $categories = Faq::CATEGORIES;
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'title'       => 'required|string|max:500',
            'description' => 'required|string',
            'category'    => 'required|in:' . implode(',', array_keys(Faq::CATEGORIES)),
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $faq->update([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'is_active'   => $request->boolean('is_active'),
            'sort_order'  => $request->input('sort_order', $faq->sort_order),
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted.');
    }

    /**
     * GET /admin/faqs/by-category?category=hotel
     * Returns FAQs as JSON — used by the page builder to preview available FAQs.
     */
    public function byCategory(Request $request)
    {
        $faqs = Faq::active()
            ->when($request->category && $request->category !== 'all', fn($q) => $q->where('category', $request->category))
            ->orderBy('sort_order')
            ->select('id', 'title', 'category')
            ->get();

        return response()->json($faqs);
    }
}

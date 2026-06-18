<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageBuilderController extends Controller
{
    /** GET /admin/pages/{page}/builder */
    public function editPage(Page $page)
    {
        $builderData = $page->builder_data ?? [];
        return view('admin.builder.editor', [
            'title'       => 'Page Builder: ' . $page->title,
            'saveUrl'     => route('admin.builder.savePage', $page),
            'previewUrl'  => route('page.show', $page->slug),
            'backUrl'     => route('admin.pages.edit', $page),
            'builderData' => $builderData,
            'context'     => 'page',
        ]);
    }

    /** POST /admin/pages/{page}/builder/save */
    public function savePage(Request $request, Page $page)
    {
        $request->validate(['sections' => 'required|array']);
        $page->update([
            'builder_data' => $request->sections,
            'use_builder'  => true,
        ]);
        return response()->json(['success' => true, 'message' => 'Page saved successfully.']);
    }

    /** GET /admin/home/builder */
    public function editHome()
    {
        $builderData = json_decode(Setting::get('home_builder_data', '[]'), true) ?? [];
        return view('admin.builder.editor', [
            'title'       => 'Home Page Builder',
            'saveUrl'     => route('admin.builder.saveHome'),
            'previewUrl'  => route('home'),
            'backUrl'     => route('admin.settings.index'),
            'builderData' => $builderData,
            'context'     => 'home',
        ]);
    }

    /** POST /admin/home/builder/save */
    public function saveHome(Request $request)
    {
        $request->validate(['sections' => 'required|array']);
        Setting::set('home_builder_data', json_encode($request->sections));
        Cache::flush();
        return response()->json(['success' => true, 'message' => 'Home page saved successfully.']);
    }

    /** GET /admin/contact/builder */
    public function editContact()
    {
        $raw = Setting::get('contact_builder_data', '');
        $builderData = $raw ? (json_decode($raw, true) ?? []) : [];

        // Pre-populate with default blocks on first visit (unlocked — user can hide but not delete)
        if (empty($builderData)) {
            $builderData = [
                ['id' => 'chero_'.uniqid(),  'type' => 'contact-hero',        'config' => ['eyebrow' => 'GET IN TOUCH', 'title' => 'Contact Us', 'subtitle' => "We'd love to hear from you. Reach out with any questions, reservations or special requests."]],
                ['id' => 'cform_'.uniqid(),  'type' => 'contact-form',        'config' => ['title' => 'Send us a Message', 'description' => "Fill in the form below and we'll get back to you within 24 hours."]],
                ['id' => 'cinfo_'.uniqid(),  'type' => 'contact-info',        'config' => []],
                ['id' => 'clinks_'.uniqid(), 'type' => 'contact-quick-links', 'config' => []],
            ];
        }

        return view('admin.builder.editor', [
            'title'       => 'Contact Page Builder',
            'saveUrl'     => route('admin.builder.saveContact'),
            'previewUrl'  => route('contact'),
            'backUrl'     => route('admin.settings.index'),
            'builderData' => $builderData,
            'context'     => 'contact',
        ]);
    }

    /** POST /admin/contact/builder/save */
    public function saveContact(Request $request)
    {
        $request->validate(['sections' => 'required|array']);

        // Ensure all required contact block types exist somewhere in the sections tree
        // (they may be hidden or nested inside a columns block, but must not be deleted)
        $required = ['contact-hero', 'contact-form', 'contact-info', 'contact-quick-links'];
        $found    = $this->collectBlockTypes($request->sections);
        $missing  = array_diff($required, $found);

        if (!empty($missing)) {
            $labels = [
                'contact-hero'        => 'Contact Hero',
                'contact-form'        => 'Contact Form',
                'contact-info'        => 'Contact Info',
                'contact-quick-links' => 'Contact Quick Links',
            ];
            $names = implode(', ', array_map(fn($t) => $labels[$t] ?? $t, $missing));
            return response()->json([
                'success' => false,
                'message' => "Cannot save — the following required blocks are missing from the page: {$names}. You may hide them, but they must remain on the page.",
            ], 422);
        }

        Setting::set('contact_builder_data', json_encode($request->sections));
        Cache::flush();
        return response()->json(['success' => true, 'message' => 'Contact page saved successfully.']);
    }

    /**
     * Recursively collect all block type strings from a sections array,
     * including blocks nested inside 'columns' blocks.
     */
    private function collectBlockTypes(array $sections): array
    {
        $types = [];
        foreach ($sections as $section) {
            $types[] = $section['type'] ?? '';
            if (($section['type'] ?? '') === 'columns') {
                foreach ($section['config']['columns'] ?? [] as $col) {
                    foreach ($col['blocks'] ?? [] as $block) {
                        $types[] = $block['type'] ?? '';
                    }
                }
            }
        }
        return array_unique(array_filter($types));
    }
}

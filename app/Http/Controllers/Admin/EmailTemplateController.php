<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();
        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', [
            'template'   => $emailTemplate,
            'shortcodes' => EmailTemplate::$shortcodes,
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $emailTemplate->update([
            'subject' => $request->subject,
            'body'    => $request->body,
        ]);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully');
    }
}

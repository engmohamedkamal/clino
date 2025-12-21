<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $services = Service::where('status','1')->get();
        return view('contact.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // لأن user_id unique → نستخدم updateOrCreate
        Contact::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'service' => $validated['service'],
                'message' => $validated['message'],
            ]
        );

        return redirect()->back()->with('success', 'تم حفظ بيانات التواصل بنجاح.');
    }

    /**
     * Show edit form.
     */
    public function edit()
    {
        $contact = Auth::user()->contact;

        if (! $contact) {
            return redirect()->route('contact.create')
                ->with('error', 'من فضلك أضف بيانات التواصل أولاً.');
        }

        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update contact.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Auth::user()->contact;

        if (! $contact) {
            return redirect()->route('contact.create')
                ->with('error', 'لا يوجد بيانات تواصل لتعديلها.');
        }

        $contact->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات التواصل بنجاح.');
    }

    /**
     * Delete contact.
     */
    public function destroy()
    {
        $contact = Auth::user()->contact;

        if ($contact) {
            $contact->delete();
        }

        return redirect()->back()->with('success', 'تم حذف بيانات التواصل.');
    }
}

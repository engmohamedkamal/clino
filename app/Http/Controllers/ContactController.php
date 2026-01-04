<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
     public function index()
    {
        $services = Service::where('status','1')->get();
        $setting = Setting::first();
        return view('contact.index', compact('services','setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
          Contact::create([
        'user_id' => Auth::id(),
        'service' => $validated['service'],
        'message' => $validated['message'],
    ]);

        return redirect()->back()->with('success', 'Message Sent Successfully');
    }


    public function destroy()
    {
        $contact = Auth::user()->contact;

        if ($contact) {
            $contact->delete();
        }

        return redirect()->back()->with('success', 'تم حذف بيانات التواصل.');
    }


public function show(Request $request)
{
    $query = Contact::with('user'); // eager loading

    if ($request->filled('q')) {
        $q = $request->q;

        $query->where(function ($sub) use ($q) {

            // 🔹 الاسم من جدول users
            $sub->whereHas('user', function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%");
            })

            // 🔹 باقي الأعمدة من messages
            ->orWhere('service', 'like', "%{$q}%")
            ->orWhere('message', 'like', "%{$q}%");
        });
    }

    $messages = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('contact.show', compact('messages'));
}

public function bulkDestroy(Request $request)
{
    $ids = $request->ids;

    if (!$ids || count($ids) === 0) {
        return back()->withErrors('اختار رسالة واحدة على الأقل للحذف');
    }

    Contact::whereIn('id', $ids)->delete();

    return back()->with('success', 'تم حذف الرسائل المختارة بنجاح');
}
}

<?php

namespace App\Http\Controllers\Patient;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('dashboard.feedback.index');
    }
   public function store(Request $request)
{

    if (Feedback::where('user_id', $request->user_id)->exists()) {
        return back()->with('success', 'You have already submitted feedback before.');
    }
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'comment' => 'required|string|max:255',
    ]);

    Feedback::create($validated);

    return back()->with('success', 'Feedback added successfully');
}


}

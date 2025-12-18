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
        return view('patients.feedback.index');
    }
    public function store(Request $request)
    {
            $validated = $request->validate([
        'comment' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id|unique:feedback,user_id',
    ], [
        'user_id.unique' => 'You have already submitted feedback.',
    ]);


        Feedback::create($validated);

        return back()->with('success', 'Feedback added successfully');
    }

}

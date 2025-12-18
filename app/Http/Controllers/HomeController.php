<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Setting;
use App\Models\Feedback;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
      $services = Service::latest()->take(9)->get();
      $feedbacks = Feedback::latest()->take(9)->get();
      $setting = Setting::first();
      return view('draft',compact('services','feedbacks','setting'));
    }
}

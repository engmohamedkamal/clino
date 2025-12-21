<?php

namespace App\Http\Controllers;

use App\Models\DoctorInfo;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Feedback;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
      $services = Service::where('status', 1)
    ->latest()
    ->take(9)
    ->get();

      $feedbacks = Feedback::latest()->take(9)->get();
      $setting = Setting::first();
      return view('draft',compact('services','feedbacks','setting'));
    }

    public function about(){
      $doctors = DoctorInfo::get();
      return view('about.index',compact('doctors'));
    }

    public function service(){
       $services = Service::paginate(6);
      return view('services.index',compact('services'));
    }
}

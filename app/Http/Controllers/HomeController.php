<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
      $services = Service::latest()->take(9)->get();

        return view('draft',compact('services'));
    }
}

<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
     public function index(){
      return view('dashboard');
    }
}


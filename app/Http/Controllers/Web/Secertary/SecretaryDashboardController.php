<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecretaryDashboardController extends Controller
{

     public function index(){
       return view('dashboard');
    }
}

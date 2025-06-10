<?php

use App\Http\Controllers\Api\DiseasesController;
use App\Http\Controllers\API\PatientProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Auth_Otp\ForgetPasswordController;
use App\Http\Controllers\Web\Auth_Otp\LoginController;
use App\Http\Controllers\Web\Auth_Otp\LoginWithOtpController;
use App\Http\Controllers\Web\Auth_Otp\RegisterWithOtpController;
use App\Http\Controllers\Web\Doctor\DoctorDashboardController;
use App\Http\Controllers\Web\Secertary\SecretaryDashboardController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('password', [ForgetPasswordController::class, 'update'])->name('password.update');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterWithOtpController::class, 'create'])->name('register');
    Route::post('register', [RegisterWithOtpController::class, 'store']);
    Route::get('verify/otp', [RegisterWithOtpController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('verify/otp/store', [RegisterWithOtpController::class, 'verifyOtpStore'])->name('verify.otp.store');
    Route::get('login', [LoginWithOtpController::class, 'create'])->name('login');
    Route::post('login/store', [LoginController::class, 'store'])->name('login.store');
   // Route::get('verify/otp/login', [LoginWithOtpController::class, 'verifyOtp'])->name('verify.otp.login');
 //   Route::post('verify/otp/login/store', [LoginWithOtpController::class, 'verifyOtpStore'])->name('verify.otp.login.store');
    Route::get('forgot-password', [ForgetPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgetPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/verify-otp', [ForgetPasswordController::class, 'verifyOtpForm'])->name('password.otp.verify');
    Route::post('/verify-otp', [ForgetPasswordController::class, 'verifyOtp'])->name('password.otp.check');
    Route::get('/reset-password-otp', [ForgetPasswordController::class, 'showResetForm'])->name('password.otp.reset.form');
    Route::post('/reset-password-otp', [ForgetPasswordController::class, 'resetPassword'])->name('password.otp.reset');

});
Route::middleware(['auth', 'role:doctor', 'verified'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    Route::get('/doctor/password/edit', [ForgetPasswordController::class, 'edit'])->name('doctor.password.edit');
    Route::put('/doctor/password/update', [ForgetPasswordController::class, 'update'])->name('doctor.password.update');
    //patient_profile
    Route::put('patient-profile', [PatientProfileController::class, 'update']); // PUT
    Route::put('diseases/{disease}', [DiseasesController::class, 'update']);
    Route::delete('diseases/{disease}', [DiseasesController::class, 'destroy']);

});

Route::middleware(['auth', 'role:secretary', 'verified'])->group(function () {
    Route::get('/secretary/dashboard', [SecretaryDashboardController::class, 'index'])->name('secretary.dashboard');
    Route::get('/secretary/password/edit', [ForgetPasswordController::class, 'edit'])->name('secretary.password.edit');
    Route::put('/secretary/password/update', [ForgetPasswordController::class, 'update'])->name('secretary.password.update');
});
/* Route::middleware(['auth', 'verified', 'role:doctor,secretary'])->group(function () {
    Route::get('/password/update', [ForgetPasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password/update', [ForgetPasswordController::class, 'update'])->name('password.update');
}); */

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\API\AllergyController;
use App\Http\Controllers\API\Auth\Profile\ProfileController;
use App\Http\Controllers\API\DiseasesController;
use App\Http\Controllers\API\FamilyHistoryController;
use App\Http\Controllers\API\MedicalFileController;
use App\Http\Controllers\API\MedicationController;
use App\Http\Controllers\API\OperationController;
use App\Http\Controllers\API\PatientProfileController;
use App\Http\Controllers\API\PatientRecordController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\Auth_Otp\LoginController;
use App\Http\Controllers\Auth_Otp\ResetPasswordController;
use App\Http\Controllers\Auth_Otp\ForgetPasswordWithOtpController;
use App\Http\Controllers\Auth_Otp\LoginWithOtpController;
use App\Http\Controllers\Auth_Otp\RegisterWithOtpController;
use App\Http\Controllers\Socialite\GoogleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})/* ->middleware('auth:sanctum') */ ;

Route::prefix('auth')->middleware([SetLocale::class])->group(function () {
    Route::post('google', [GoogleController::class, 'googleAuth'])->name('google.auth');
    Route::any('google/callback', [GoogleController::class, 'handleGoogleLogin'])->name('google.callback');
});
Route::middleware([SetLocale::class])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::middleware('api')->post('register_app', [RegisterWithOtpController::class, 'register']);
        Route::post('verify/otp/store', [RegisterWithOtpController::class, 'verifyOtpStore']);
        Route::middleware('api')->post('login_app', [LoginController::class, 'login1']);
        Route::post('forget-password', [ForgetPasswordWithOtpController::class, 'sendOtp']);
        Route::post('verify/otp/reset', [ForgetPasswordWithOtpController::class, 'verifyOtp']);
        Route::post('set-new-Password', [ForgetPasswordWithOtpController::class, 'setNewPassword']);
        //Route::middleware('api')->post('login_app_otp', [LoginWithOtpController::class, 'login']);
        // Route::post('verify/otp/login/store', [LoginWithOtpController::class, 'verifyOtpStore']);

    });
});
Route::middleware(['auth:sanctum', SetLocale::class])->group(function () {
    Route::post('profile', [ProfileController::class, 'update']);
    Route::get('get_profile', [ProfileController::class, 'show']);

});
Route::middleware(['auth:sanctum', SetLocale::class])->group(function () {
    Route::post('change-password', [ResetPasswordController::class, 'changePassword']);
    //السجل الطبي
/*     Route::get('/patient-records/{id}', [PatientRecordController::class, 'show']);
    Route::post('/patient-records', [PatientRecordController::class, 'store']);
    Route::put('/patient-records/{id}', [PatientRecordController::class, 'update']);
    Route::delete('/patient-records/{id}', [PatientRecordController::class, 'destroy']);*/
/*
    Route::get('patient-profile', [PatientProfileController::class, 'show']);   // GET
    Route::post('patient-profile', [PatientProfileController::class, 'store1']); // POST
    //Diseases
    Route::get('/diseases', [DiseasesController::class, 'index']);
    Route::get('diseases/{disease}', [DiseasesController::class, 'show']);
    Route::post('diseases/store', [DiseasesController::class, 'store']);
    Route::post('diseases/submit', [DiseasesController::class, 'submit']);
    //Medication
    Route::get('/medication', [MedicationController::class, 'index']);
    Route::get('medication/{medication}', [MedicationController::class, 'show']);
    Route::post('medication/store', [MedicationController::class, 'store']);
    Route::post('medication/submit', [MedicationController::class, 'submit']);
    //Operation
    Route::get('/operation', [OperationController::class, 'index']);
    Route::get('operation/{operation}', [OperationController::class, 'show']);
    Route::post('operation/store', [OperationController::class, 'store']);
    Route::post('operation/submit', [OperationController::class, 'submit']);
    //Test
    Route::get('/test', [TestController::class, 'index']);
    Route::get('test/{test}', [TestController::class, 'show']);
    Route::post('test/store', [TestController::class, 'store']);
    Route::post('test/submit', [TestController::class, 'submit']);
    //Allergy
    Route::get('/allergy', [AllergyController::class, 'index']);
    Route::get('allergy/{allergy}', [AllergyController::class, 'show']);
    Route::post('allergy/store', [AllergyController::class, 'store']);
    Route::post('allergy/submit', [AllergyController::class, 'submit']);
    //FamilyHistory
    Route::get('/familyHistory', [FamilyHistoryController::class, 'index']);
    Route::get('familyHistory/{familyHistory}', [FamilyHistoryController::class, 'show']);
    Route::post('familyHistory/store', [FamilyHistoryController::class, 'store']);
    Route::post('familyHistory/submit', [FamilyHistoryController::class, 'submit']);
    //MedicalFile
    Route::get('/medicalFile', [MedicalFileController::class, 'index']);
    Route::get('medicalFile/{medicalFile}', [MedicalFileController::class, 'show']);
    Route::post('medicalFile/store', [MedicalFileController::class, 'store']);
    Route::post('medicalFile/submit', [MedicalFileController::class, 'submit']);
 */
});




<?php

use App\Http\Controllers\API\Appointement\AppointmentController;
use App\Http\Controllers\API\Appointement\NotoficationController;
use App\Http\Controllers\API\Doctor\DoctorScheduleController;
use \App\Http\Controllers\API\PatientRecord\AllergyController;
use App\Http\Controllers\API\Auth\Profile\ProfileController;
use App\Http\Controllers\API\Doctor\DoctorController;

use App\Http\Controllers\API\PatientRecord\DiseaseController;
use App\Http\Controllers\API\PatientRecord\MediactionController;
use App\Http\Controllers\API\PatientRecord\MedicalAttachmentController;
use App\Http\Controllers\API\PatientRecord\MedicalFileController;
use App\Http\Controllers\Api\PatientRecord\MedicationAlarmController;
use App\Http\Controllers\API\PatientRecord\OperationController;
use App\Http\Controllers\Auth_Otp\LoginController;
use App\Http\Controllers\Auth_Otp\ResetPasswordController;
use App\Http\Controllers\Auth_Otp\ForgetPasswordWithOtpController;
use App\Http\Controllers\Auth_Otp\LoginWithOtpController;
use App\Http\Controllers\Auth_Otp\RegisterWithOtpController;
use App\Http\Controllers\Socialite\GoogleController;
use App\Http\Controllers\API\PatientRecord\PatientProfileController;
use App\Http\Middleware\SetLocale;
use App\Models\Appointment;
use Carbon\Carbon;
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
        Route::post('resend-otp', [RegisterWithOtpController::class, 'resendOtp']);
        Route::middleware('api')->post('login_app', [LoginController::class, 'login1']);
        Route::post('forget-password', [ForgetPasswordWithOtpController::class, 'sendOtp']);
        Route::post('verify/otp/reset', [ForgetPasswordWithOtpController::class, 'verifyOtp']);
        Route::post('set-new-Password', [ForgetPasswordWithOtpController::class, 'setNewPassword']);


        //Route::middleware('api')->post('login_app_otp', [LoginWithOtpController::class, 'login']);
        // Route::post('verify/otp/login/store', [LoginWithOtpController::class, 'verifyOtpStore']);

    });
});
Route::middleware([SetLocale::class, 'auth:sanctum'])->group(function () {
    Route::post('user/device-token', [RegisterWithOtpController::class, 'updateDeviceToken']);
    Route::post('profile', [ProfileController::class, 'update'])->name('patient.profile.edit');
    Route::get('get_profile', [ProfileController::class, 'show']);

});
Route::middleware([SetLocale::class, 'auth:sanctum'])->group(function () {
    Route::post('change-password', [ResetPasswordController::class, 'changePassword']);
    //السجل الطبي
/*     Route::get('/patient-records/{id}', [PatientRecordController::class, 'show']);
    Route::post('/patient-records', [PatientRecordController::class, 'store']);
    Route::put('/patient-records/{id}', [PatientRecordController::class, 'update']);
    Route::delete('/patient-records/{id}', [PatientRecordController::class, 'destroy']);*/
    Route::post('/patient-record/profile', [PatientProfileController::class, 'store']);
    Route::get('/patient-record/profile', [PatientProfileController::class, 'showMyProfile']);
    // للطبيب
    Route::get('/patient-record/profile/{patientId}', [PatientProfileController::class, 'showForDoctor']);
    //allergies
    Route::get('/patient-record/allergies', [AllergyController::class, 'show']);
    Route::post('/patient-record/allergies', [AllergyController::class, 'storeOneByOne']);
    Route::get('/patient-record/allergiesGroup', [AllergyController::class, 'showGrouped']);
    //MedicalFileTest
    Route::post('/patient-record/MedicalFileTest', [MedicalFileController::class, 'store']);
    Route::get('/patient-record/MedicalFileTest', [MedicalFileController::class, 'indexGrouped']);
    Route::get('/patient-record/MedicalFileTest/images', [MedicalFileController::class, 'getImages']);
    Route::get('/patient-record/MedicalFileTest/documents', [MedicalFileController::class, 'getDocuments']);
    //MedicalAttachment
    Route::post('/patient-record/MedicalAttachment', [MedicalAttachmentController::class, 'store']);
    Route::get('/patient-record/MedicalAttachment', [MedicalAttachmentController::class, 'index']);
    //Diseases
    Route::post('/patient-record/diseases', [DiseaseController::class, 'store']);
    Route::get('/patient-record/diseasesGroup', [DiseaseController::class, 'show']);

    //Medications
    Route::post('/patient-record/medications', [MediactionController::class, 'store']);
    Route::get('/patient-record/medications', [MediactionController::class, 'index']);
    //Operation
    Route::post('/patient-record/operations', [OperationController::class, 'store']);
    Route::get('/patient-record/operations', [OperationController::class, 'index']);
    //PatientRecordSave
    Route::post('/patient-record/PatientRecordSave', [PatientProfileController::class, 'saveRecord']);
    //MedicationAlarm
    //Route::apiResource('medication-alarms', MedicationAlarmController::class)->only(['index', 'store', 'destroy']);
    Route::post('medication-alarms', [MedicationAlarmController::class, 'store']);
    Route::get('medication-alarms', [MedicationAlarmController::class, 'index']);
    Route::delete('medication-alarms/{id}', [MedicationAlarmController::class, 'destroy']);
    //book-appointment
    Route::post('doctor/{doctor}/book-appointment', [AppointmentController::class, 'book']);
    Route::post('/appointments/{appointmentId}/setArrivvedTime', [AppointmentController::class, 'setArrivvedTime']);
    Route::delete('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancelByPatient']);
    Route::get('/patient/appointments', [AppointmentController::class, 'getPatientAppointmentsGroupedByStatus']);
    Route::get('/appointments/confirmed/nearestAll', [AppointmentController::class, 'getConfirmedAppointmentsGroupedByDay']);
    Route::get('/appointments/confirmed/nearest', [AppointmentController::class, 'getNearestConfirmedAppointmentForCurrentUser']);



});
//notifiaction
Route::middleware([SetLocale::class], 'auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotoficationController::class, 'all']);
    Route::get('/unread', [NotoficationController::class, 'unread']);
    Route::post('/{id}/read', [NotoficationController::class, 'markAsRead']);
    Route::post('/read-all', [NotoficationController::class, 'markAllAsRead']);
});

Route::middleware([SetLocale::class])->group(function () {
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctor/details/{id}', [DoctorController::class, 'show2']);
    Route::get('doctor/{doctorId}/slots', [DoctorController::class, 'getDaySlots']);

    // عرض تفاصيل الطبيب + الجدول الزمني + الفترات الزمنية
    Route::get('doctor/{doctor}/details', [DoctorController::class, 'show']);
    Route::get('doctor/{doctor}/month-days', [DoctorScheduleController::class, 'monthDays']);
    Route::get('doctor/{doctor}/day-slots', [DoctorScheduleController::class, 'daySlots']);


    // حجز موعد مع طبيب
//    Route::post('{doctor}/appointments', [AppointmentController::class, 'book']);



});

Route::get('/nbmb/{date}', function ($date) {
    return \App\Models\Appointment::with(['doctor', 'patient'])
        ->whereDate('date', $date)
        ->where('status', 'confirmed')
        ->orderBy('start_time')
        ->get();
});



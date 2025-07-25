<?php

use App\Http\Controllers\Api\DiseasesController;
use App\Http\Controllers\API\PatientProfileController;
use App\Http\Controllers\Web\Secertary\LoginSecretaryController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\RegisterAdminController;
use App\Http\Controllers\Web\Auth_Otp\DashProfileController;
use App\Http\Controllers\Web\Auth_Otp\ForgetPasswordController;
use App\Http\Controllers\Web\Admin\LoginAdminController;
use App\Http\Controllers\Web\Auth_Otp\LoginController;
use App\Http\Controllers\Web\Auth_Otp\LoginWithOtpController;
use App\Http\Controllers\Web\Auth_Otp\RegisterWithOtpController;
use App\Http\Controllers\Web\Doctor\DoctorDashboardController;
use App\Http\Controllers\Web\Doctor\DoctorProfileController;
use App\Http\Controllers\Web\Doctor\Schedule\DoctorScheduleController;
use App\Http\Controllers\Web\Secertary\SecertaryPatientController;
use App\Http\Controllers\Web\Secertary\SecretaryAppointmentController;
use App\Http\Controllers\Web\Secertary\SecretaryController;
use App\Http\Controllers\Web\Secertary\SecretaryDoctorController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


Route::get('/', function () {
    if (auth()->check()) {
        $user = Auth::user();
        if ($user->role == 'admin') {
            return redirect()->route('admin.index');
        } elseif ($user->role == 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->role == 'secretary') {
            return redirect()->route('secretary.dashboard');
        } else {
            abort(403, 'Unauthorized.');
        }
    }
    return redirect()->route('login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [DashProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [DashProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [DashProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('password', [ForgetPasswordController::class, 'update'])->name('password.update');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterAdminController::class, 'create']);
    Route::post('register', [RegisterAdminController::class, 'store'])->name('admin.register');
    Route::get('login', [LoginAdminController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginAdminController::class, 'store'])->name('admin.login');
    Route::get('login', [LoginAdminController::class, 'create'])->name('login');

});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/secretary', [AdminController::class, 'secretary'])->name('admin.secretary');
    Route::get('/admin/secretary/add', [AdminController::class, 'secretary_add'])->name('admin.secretary.add');
    Route::post('/admin/secretary/store', [AdminController::class, 'secretary_store'])->name('admin.secretary.store');
    Route::get('/admin/secretary/{id}/replace', [AdminController::class, 'secretary_replace'])->name('admin.secretary.replace');
    Route::put('/admin/secretary/update', [AdminController::class, 'secretary_update'])->name('admin.secretary.update');
    //Route::delete('/admin/secretary/{id}/delete', [AdminController::class, 'secretary_delete'])->name('admin.secretary.delete');
    Route::get('/admin/doctor', [AdminController::class, 'doctor'])->name('admin.doctor');
    Route::get('/admin/doctor/{id}/details', [AdminController::class, 'doctor_details'])->name('admin.doctor.details');
    Route::get('/admin/doctor/add', [AdminController::class, 'doctor_add'])->name('admin.doctor.add');
    Route::post('/admin/doctor/store', [AdminController::class, 'doctor_store'])->name('admin.doctor.store');
    Route::get('/admin/doctor/{id}/edit', [AdminController::class, 'doctor_edit'])->name('admin.doctor.edit');
    Route::put('/admin/doctor/{id}/update', [AdminController::class, 'doctor_update'])->name('admin.doctor.update');
    Route::delete('/admin/doctor/{id}/delete', [AdminController::class, 'doctor_delete'])->name('admin.doctor.delete');

});


Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisterAdminController::class, 'create'])->name('admin.register');
    //Route::post('register', [RegisterAdminController::class, 'store'])->name('admin.register');

    // لا تظهر صفحة تسجيل الـ Admin إلا إذا ما فيه Admin بعد
    /*  if (!User::where('role', 'admin')->exists()) {
         Route::get('/register-admin', [RegisterAdminController::class, 'showAdminForm'])->name('admin.register');
         Route::post('/register-admin', [RegisterAdminController::class, 'store'])->name('admin.register.store');
     } */
    //  Route::post('register', [RegisterWithOtpController::class, 'store']);
    //Route::get('verify/otp', [RegisterWithOtpController::class, 'verifyOtp'])->name('verify.otp');
    //Route::post('verify/otp/store', [RegisterWithOtpController::class, 'verifyOtpStore'])->name('verify.otp.store');
    // Route::get('login', [LoginWithOtpController::class, 'create'])->name('login');
    //Route::post('login/store', [LoginController::class, 'store'])->name('login.store');
    // Route::get('verify/otp/login', [LoginWithOtpController::class, 'verifyOtp'])->name('verify.otp.login');
    // Route::post('verify/otp/login/store', [LoginWithOtpController::class, 'verifyOtpStore'])->name('verify.otp.login.store');
    Route::get('forgot-password', [ForgetPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgetPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/verify-otp', [ForgetPasswordController::class, 'verifyOtpForm'])->name('password.otp.verify');
    Route::post('/verify-otp', [ForgetPasswordController::class, 'verifyOtp'])->name('password.otp.check');
    Route::get('/reset-password-otp', [ForgetPasswordController::class, 'showResetForm'])->name('password.otp.reset.form');
    Route::post('/reset-password-otp', [ForgetPasswordController::class, 'resetPassword'])->name('password.otp.reset');

});
Route::middleware(['auth', 'role:doctor'])->group(function () {
    //force-change for first login
    Route::get('/doctor/first-login', [DoctorDashboardController::class, 'showForceChangeForm'])->name('doctor.first-login');
    Route::post('/doctor/first-login/update', [DoctorDashboardController::class, 'updateCredentials'])->name('doctor.first-login.update');

    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    // مسارات الطبيب العادية هنا

    Route::put('/doctor/profile/photo', [DoctorDashboardController::class, 'updateProfilePhoto'])->name('doctor.profile.photo.update');
    Route::get('/doctor/password/edit', [ForgetPasswordController::class, 'edit'])->name('doctor.password.edit');
    Route::put('/doctor/password/update', [ForgetPasswordController::class, 'update'])->name('doctor.password.update');

    //Doctor Profile
    Route::get('/doctor/profile/create', [DoctorProfileController::class, 'createProfile'])->name('doctor-profile.create');
    Route::post('/doctor/profile/store', [DoctorProfileController::class, 'storeProfile'])->name('doctor-profile.store');
    Route::get('/doctor/profile/show', [DoctorProfileController::class, 'showProfile'])->name('doctor-profile.show');
    Route::get('/doctor-profile/{id}/edit', [DoctorProfileController::class, 'edit'])->name('doctor-profile.edit');
    Route::put('/doctor-profile/{id}/update', [DoctorProfileController::class, 'updateProfile'])->name('doctor-profile.update');
    //schedules
    Route::get('/doctor/schedules', [DoctorScheduleController::class, 'index'])->name('doctor-schedule.index');
    //indexAnother
    Route::get('/doctor/schedules/indexAnother', [DoctorScheduleController::class, 'indexAnother'])->name('doctor-schedule.indexAnother');
    Route::get('/doctor/schedules/create', [DoctorScheduleController::class, 'create'])->name('doctor-schedule.create');
    Route::post('/doctor/schedules', [DoctorScheduleController::class, 'store'])->name('doctor-schedule.store');
    Route::get('/doctor/schedules/{schedule}/edit', [DoctorScheduleController::class, 'edit'])->name('doctor-schedule.edit');
    Route::put('/doctor/schedules/{schedule}', [DoctorScheduleController::class, 'update'])->name('doctor-schedule.update');
    Route::delete('/doctor/schedule/{schedule}', [DoctorScheduleController::class, 'destroy'])->name('doctor-schedule.destroy');
    Route::get('/doctor/{doctor}', [DoctorDashboardController::class, 'show'])->name('doctors.show');





});



Route::middleware('guest')->group(function () {
    //patient_profile
    Route::put('patient-profile', [PatientProfileController::class, 'update']); // PUT
    Route::put('diseases/{disease}', [DiseasesController::class, 'update']);
    Route::delete('diseases/{disease}', [DiseasesController::class, 'destroy']);
    // Route::get('login', [LoginSecretaryController::class, 'create'])->name('secretary.login');
    // Route::post('login', [LoginSecretaryController::class, 'store'])->name('secretary.login');
    // Route::get('login', [LoginSecretaryController::class, 'create'])->name('login');

});
Route::middleware(['web','auth', 'role:secretary'])->prefix('secretary')->group(function () {
    Route::get('/dashboard', [SecretaryController::class, 'index'])->name('secretary.dashboard');
    Route::get('/patient/add', [SecretaryController::class, 'patient_add'])->name('secretary.patient.add');
    Route::post('/patient/store', [SecretaryController::class, 'patient_store'])->name('secretary.patient.store');
    //Route::get('/password/edit', [ForgetPasswordController::class, 'edit'])->name('secretary.password.edit');
    //Route::put('/password/update', [ForgetPasswordController::class, 'update'])->name('secretary.password.update');
    //Appointement

    // عرض قائمة المواعيد التي تحتاج تأكيد
    Route::get('/appointments/pending/{doctorId}', [SecretaryAppointmentController::class, 'pendingByDoctor'])->name('secretary.appointments.pending');
    Route::post('/appointments/{appointment}/confirm', [SecretaryAppointmentController::class, 'confirm1'])->name('secretary.appointment.confirm');
    Route::post('/appointments/{appointment}/cancel', [SecretaryAppointmentController::class, 'cancel1'])->name('secretary.appointment.cancel');
    // Route::post('sendNotification', [SecretaryAppointmentController::class, 'sendNotification'])->name('secretary.send.notification');
    //عرض كل المواعيد التي : تخص اليوم الحالي

    Route::get('/appointments/today', [SecretaryAppointmentController::class, 'todayAppointments'])->name('secretary.appointments.today');
    //عرض الأطباء
    Route::get('/doctors', [SecretaryDoctorController::class, 'doctors'])->name('secretary.doctors');
    //عرض جدول مواعيد الأطباء الأسبوعي
    Route::get('/doctors/{id}/schedule', [SecretaryDoctorController::class, 'doctorSchedule'])->name('secretary.doctor.schedule');
    Route::get('/doctors/{id}/Appointment', [SecretaryDoctorController::class, 'doctorAppointmentsDetails'])->name('secretary.doctor.appointment');


    //عرض المرضى
    Route::get('/patients', [SecertaryPatientController::class, 'patients'])->name('secretary.patients');

    //عرض المواعيد
    Route::get('/appointments', [SecretaryAppointmentController::class, 'index'])->name('secretary.appointments');
    //عرض
    Route::get('/appointments/{appointment}', [SecretaryAppointmentController::class, 'show'])->name('secretary.appointments.show');

});


require __DIR__ . '/auth.php';

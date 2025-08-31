<?php

use App\Http\Controllers\Api\DiseasesController;
use App\Http\Controllers\API\PatientProfileController;
use App\Http\Controllers\Web\Doctor\Appointment\DoctorAppointmentController;
use App\Http\Controllers\Web\Doctor\Appointment\DoctorMaterialController;
use App\Http\Controllers\Web\Doctor\Appointment\PatientMedicalRecordController;
use App\Http\Controllers\Web\Doctor\Appointment\PrescriptionController;
use App\Http\Controllers\Web\Secertary\FinanceController;
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
use App\Http\Controllers\Web\Doctor\NurseController;
use App\Http\Controllers\Web\Doctor\DoctorProfileController;
use App\Http\Controllers\Web\Doctor\Schedule\DoctorScheduleController;
use App\Http\Controllers\Web\Secertary\SecertaryPatientController;
use App\Http\Controllers\Web\Secertary\SecretaryAppointmentController;
use App\Http\Controllers\Web\Secertary\SecretaryController;
use App\Http\Controllers\Web\Secertary\SecretaryDoctorController;
use App\Http\Controllers\Web\Secertary\SecretaryMaterialController;
use App\Http\Controllers\Web\Secertary\SecretarySupplierController;
use App\Http\Controllers\Web\Secertary\SecretaryVisitController;
use App\Models\Material;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\Secertary\Notification\AppointementStatusArrivvedNotificationService;


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
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/doctor/search', [AdminController::class, 'search'])->name('admin.doctors.search');
    Route::put('/admin/profile/update', [LoginAdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::get('/admin/finance', [FinanceController::class, 'finance'])->name('admin.finance');



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
    Route::post('doctor/logout', [AdminController::class, 'logout'])->name('doctor.logout');
    //force-change for first login
    Route::get('/doctor/first-login', [DoctorDashboardController::class, 'showForceChangeForm'])->name('doctor.first-login');
    Route::post('/doctor/first-login/update', [DoctorDashboardController::class, 'updateCredentials'])->name('doctor.first-login.update');

    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    // مسارات الطبيب العادية هنا

    Route::put('/doctor/profile/photo', [DoctorDashboardController::class, 'updateProfilePhoto'])->name('doctor.profile.photo.update');
    Route::get('/doctor/password/edit', [ForgetPasswordController::class, 'edit'])->name('doctor.password.edit');
    Route::put('/doctor/password/update', [ForgetPasswordController::class, 'update'])->name('doctor.password.update');
    Route::put('/doctor/profile/update', [DoctorDashboardController::class, 'updateProfile'])->name('doctor.profile.update');


    //Doctor Profile
    Route::get('/doctor/profile/create', [DoctorProfileController::class, 'createProfile'])->name('doctor-profile.create');
    Route::post('/doctor/profile/store', [DoctorProfileController::class, 'storeProfile'])->name('doctor-profile.store');
    Route::get('/doctor/profile/show', [DoctorProfileController::class, 'showProfile'])->name('doctor-profile.show');
    Route::get('/doctor-profile/{id}/edit', [DoctorProfileController::class, 'edit'])->name('doctor-profile.edit');
    Route::post('/doctor-profile/{id}/update', [DoctorProfileController::class, 'updateProfile'])->name('doctor-profile.update');
    //schedules
    Route::get('/doctor/schedules', [DoctorScheduleController::class, 'index'])->name('doctor-schedule.index');
    //عرض المرضى يلي في العيادة
    Route::get('/doctor/clinic', [DoctorAppointmentController::class, 'patientsInClinic'])->name('doctor.appointments.in_clinic');

    //indexAnother
    Route::get('/doctor/schedules/indexAnother', [DoctorScheduleController::class, 'indexAnother'])->name('doctor-schedule.indexAnother');
    Route::get('/doctor/schedules/create', [DoctorScheduleController::class, 'create'])->name('doctor-schedule.create');
    Route::post('/doctor/schedules', [DoctorScheduleController::class, 'store'])->name('doctor-schedule.store');
    Route::get('/doctor/schedules/{schedule}/edit', [DoctorScheduleController::class, 'edit'])->name('doctor-schedule.edit');
    Route::put('/doctor/schedules/{schedule}', [DoctorScheduleController::class, 'update'])->name('doctor-schedule.update');
    Route::delete('/doctor/schedule/{schedule}', [DoctorScheduleController::class, 'destroy'])->name('doctor-schedule.destroy');
    Route::get('/doctor/{doctor}', [DoctorDashboardController::class, 'show'])->name('doctors.show');

    // صفحة جميع المرضى اليوم وما بعده
    Route::get('/doctor/appointments/patients', [DoctorAppointmentController::class, 'patientsall'])->name('doctor.patients.index');
    Route::post('doctor/appointments/patients/{appointment}/cancel', [DoctorAppointmentController::class, 'cancel1'])->name('doctor.patients.appointment.cancel');

    //أدخل المريض
    Route::post('/doctor/appointments/{appointment}/start', [DoctorAppointmentController::class, 'enterConsultation'])
        ->name('doctor.appointments.enterConsultation');
    //عرض السجل الطبي للمريض
    Route::get('/doctor/patients/{patient}/medical-record', [PatientMedicalRecordController::class, 'show'])
        ->name('doctor.patients.medicalRecord.show');

    Route::prefix('/doctor/medical-record')->group(function () {

        Route::get('/{patientRecordId}/patient_profile', [PatientMedicalRecordController::class, 'patient_profile'])->name('doctor.medical-record.patient_profile');
        Route::get('/{patientProfileId}/patient_profile/edit', [PatientMedicalRecordController::class, 'patient_profile_Edit'])->name('doctor.medical-record.patient_profile.edit');
        Route::put('/{patientProfileId}/patient_profile/update', [PatientMedicalRecordController::class, 'patient_profile_Update'])->name('doctor.medical-record.patient_profile.update');
        Route::get('/{patientId}/patient_profile/create', [PatientMedicalRecordController::class, 'patient_profile_Create'])->name('doctor.medical-record.patient_profile.create');
        Route::post('/{patientId}/patient_profile/store', [PatientMedicalRecordController::class, 'patient_profile_Store'])->name('doctor.medical-record.patient_profile.store');


        Route::get('/{patientRecordId}/diseases', [PatientMedicalRecordController::class, 'diseases'])->name('doctor.medical-record.diseases');
        Route::get('/{diseaseId}/diseases/edit', [PatientMedicalRecordController::class, 'diseases_Edit'])->name('doctor.medical-record.diseases.edit');
        Route::post('/{diseaseId}/diseases/update', [PatientMedicalRecordController::class, 'diseases_Update'])->name('doctor.medical-record.diseases.update');
        Route::get('/{patientId}/diseases/create', [PatientMedicalRecordController::class, 'diseases_Create'])->name('doctor.medical-record.diseases.create');
        Route::post('/{patientId}/diseases/store', [PatientMedicalRecordController::class, 'diseases_Store'])->name('doctor.medical-record.diseases.store');
        Route::delete('/{diseaseId}/diseases/delete', [PatientMedicalRecordController::class, 'diseases_Delete'])->name('doctor.medical-record.diseases.delete');
        Route::get('/{patientRecordId}/diseases/deleteAll', [PatientMedicalRecordController::class, 'diseases_DeleteAll'])->name('doctor.medical-record.diseasesAll');

        Route::get('/{patientRecordId}/medications', [PatientMedicalRecordController::class, 'medications'])->name('doctor.medical-record.medications');
        Route::get('/{medicationId}/medications/edit', [PatientMedicalRecordController::class, 'medications_Edit'])->name('doctor.medical-record.medications.edit');
        Route::post('/{medicationId}/medications/update', [PatientMedicalRecordController::class, 'medications_Update'])->name('doctor.medical-record.medications.update');
        Route::get('/{patientId}/medications/create', [PatientMedicalRecordController::class, 'medications_Create'])->name('doctor.medical-record.medications.create');
        Route::post('/{patientId}/medications/store', [PatientMedicalRecordController::class, 'medications_Store'])->name('doctor.medical-record.medications.store');
        Route::delete('/{medicationId}/medications/delete', [PatientMedicalRecordController::class, 'medications_Delete'])->name('doctor.medical-record.medications.delete');
        Route::get('/{medicationId}/medications/showone', [PatientMedicalRecordController::class, 'medications_show'])->name('doctor.medical-record.medications.showone');
        Route::get('/{patientRecordId}/medications/deleteAll', [PatientMedicalRecordController::class, 'medications_DeleteAll'])->name('doctor.medical-record.medicationsAll');



        Route::get('/{patientRecordId}/operations', [PatientMedicalRecordController::class, 'operations'])->name('doctor.medical-record.operations');
        Route::get('/{operationId}/operations/edit', [PatientMedicalRecordController::class, 'operations_Edit'])->name('doctor.medical-record.operations.edit');
        Route::post('/{operationId}/operations/update', [PatientMedicalRecordController::class, 'operations_Update'])->name('doctor.medical-record.operations.update');
        Route::get('/{patientId}/operations/create', [PatientMedicalRecordController::class, 'operations_Create'])->name('doctor.medical-record.operations.create');
        Route::post('/{patientId}/operations/store', [PatientMedicalRecordController::class, 'operations_Store'])->name('doctor.medical-record.operations.store');
        Route::delete('/{operationId}/operations/delete', [PatientMedicalRecordController::class, 'operations_Delete'])->name('doctor.medical-record.operations.delete');
        Route::get('/{operationId}/operations/show', [PatientMedicalRecordController::class, 'operations_show'])->name('doctor.medical-record.operations.show');
        Route::get('/{patientRecordId}/operations/deleteAll', [PatientMedicalRecordController::class, 'operations_DeleteAll'])->name('doctor.medical-record.operations.deleteAll');

        // ضمن مجموعة route الخاصة بالسجل الطبي
        Route::get('/{patientRecordId}/allergies', [PatientMedicalRecordController::class, 'allergies'])->name('doctor.medical-record.allergies.index');
        Route::get('/{patientRecordId}/allergies/create', [PatientMedicalRecordController::class, 'allergies_Create'])->name('doctor.medical-record.allergies.create');
        Route::post('/{patientRecordId}/allergies/store', [PatientMedicalRecordController::class, 'allergies_Store'])->name('doctor.medical-record.allergies.store');
        Route::get('/{allergyId}/allergies/edit', [PatientMedicalRecordController::class, 'allergies_Edit'])->name('doctor.medical-record.allergies.edit');
        Route::post('/{allergyId}/allergies/update', [PatientMedicalRecordController::class, 'allergies_Update'])->name('doctor.medical-record.allergies.update');
        Route::delete('/{allergyId}/allergies/delete', [PatientMedicalRecordController::class, 'allergies_Delete'])->name('doctor.medical-record.allergies.delete');
        Route::delete('/{allergyId}/allergies/deleteAll', [PatientMedicalRecordController::class, 'allergies_DeleteAll'])->name('doctor.medical-record.allergies.deleteAll');
        Route::get('/{allergyId}/allergies/show', [PatientMedicalRecordController::class, 'allergies_show'])->name('doctor.medical-record.allergies.show');


        Route::get('/{patientRecordId}/medicalFiles', [PatientMedicalRecordController::class, 'medicalFiles'])->name('doctor.medical-record.medicalFiles');
        Route::get('/{medicalFileId}/medicalFiles/edit', [PatientMedicalRecordController::class, 'medicalFiles_Edit'])->name('doctor.medical-record.medicalFiles.edit');
        Route::post('/{medicalFileId}/medicalFiles/update', [PatientMedicalRecordController::class, 'medicalFiles_Update'])->name('doctor.medical-record.medicalFiles.update');
        Route::get('/{patientId}/medicalFiles/create', [PatientMedicalRecordController::class, 'medicalFiles_Create'])->name('doctor.medical-record.medicalFiles.create');
        Route::post('/{patientId}/medicalFiles/store', [PatientMedicalRecordController::class, 'medicalFiles_Store'])->name('doctor.medical-record.medicalFiles.store');
        Route::delete('/{medicalFileId}/medicalFiles/delete', [PatientMedicalRecordController::class, 'medicalFiles_Delete'])->name('doctor.medical-record.medicalFiles.delete');
        Route::delete('/{patientRecordId}/medicalFiles/deleteAll', [PatientMedicalRecordController::class, 'medicalFiles_DeleteAll'])->name('doctor.medical-record.medicalFiles.deleteAll');
        Route::get('/{medicalFileId}/medicalFiles/show', [PatientMedicalRecordController::class, 'medicalFiles_Show'])->name('doctor.medical-record.medicalFiles.show');

        Route::get('/{patientRecordId}/medicalAttachments', [PatientMedicalRecordController::class, 'medicalAttachments'])->name('doctor.medical-record.medicalAttachments');
        Route::get('/{medicalAttachmentId}/medicalAttachments/edit', [PatientMedicalRecordController::class, 'medicalAttachments_Edit'])->name('doctor.medical-record.medicalAttachments.edit');
        Route::post('/{medicalAttachmentId}/medicalAttachments/update', [PatientMedicalRecordController::class, 'medicalAttachments_Update'])->name('doctor.medical-record.medicalAttachments.update');
        Route::get('/{patientId}/medicalAttachments/create', [PatientMedicalRecordController::class, 'medicalAttachments_Create'])->name('doctor.medical-record.medicalAttachments.create');
        Route::post('/{patientId}/medicalAttachments/store', [PatientMedicalRecordController::class, 'medicalAttachments_Store'])->name('doctor.medical-record.medicalAttachments.store');
        Route::delete('/{medicalAttachmentId}/medicalAttachments/delete', [PatientMedicalRecordController::class, 'medicalAttachments_Delete'])->name('doctor.medical-record.medicalAttachments.delete');
        Route::delete('/{patientRecordId}/medicalAttachments/deleteAll', [PatientMedicalRecordController::class, 'medicalAttachments_DeleteAll'])->name('doctor.medical-record.medicalAttachments.deleteAll');
        Route::get('/{medicalAttachmentId}/medicalAttachments/show', [PatientMedicalRecordController::class, 'medicalAttachment_Show'])->name('doctor.medical-record.medicalAttachment.show');

    });



    Route::post('doctor/prescriptions/create', [PrescriptionController::class, 'createPrescription'])->name('doctor.prescription.create');
    Route::get('doctor/{prescriptionId}/prescriptions/addItemForm', [PrescriptionController::class, 'addItemForm'])->name('doctor.prescription.addItemForm');
    Route::post('doctor/{prescriptionId}/prescriptions/store', [PrescriptionController::class, 'addMedicineToPrescription'])->name('doctor.prescription.store');
    Route::get('doctor/prescriptions', [PrescriptionController::class, 'prescription'])->name('doctor.prescription');

    Route::prefix('doctor/materials')->middleware('auth')->group(function () {
        Route::get('/create', [DoctorMaterialController::class, 'create'])->name('doctor.material.create');
        Route::post('/store', [DoctorMaterialController::class, 'storeMultiple'])->name('doctor.materials.storeMultiple');
        Route::get('/used', [DoctorMaterialController::class, 'index'])->name('doctor.material.index');
    });

    Route::post('doctor/appointments/patients/{visitId}/finishVisit', [DoctorAppointmentController::class, 'finishVisit'])->name('doctor.visits.finish');

    Route::post('doctor/appointments/patients/cancelToday', [DoctorAppointmentController::class, 'cancelTodayAppointments'])->name('doctor.cancelToday');

    Route::post('doctor/appointments/postpone', [DoctorAppointmentController::class, 'postpone'])->name('doctor.appointments.postpone');
    Route::post('doctor/nurse/nurseStore', [NurseController::class, 'nurseStore'])->name('doctor.nurse.nurseStor');
    Route::get('/doctor/nurses', [NurseController::class, 'index'])->name('nurses.index');


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
Route::middleware(['web', 'auth', 'role:secretary'])->group(function () {

    Route::get('/secretary/dashboard', [SecretaryController::class, 'index'])->name('secretary.dashboard');
    Route::get('/secretary/patient/add', [SecretaryController::class, 'patient_add'])->name('secretary.patient.add');
    Route::post('/secretary/patient/store', [SecretaryController::class, 'patient_store'])->name('secretary.patient.store');
    Route::get('/secretary/patient/book/add', [SecretaryController::class, 'book_add'])->name('secretary.patient.book.add')->middleware('web');
    Route::post('/secretary/patient/book/store', [SecretaryController::class, 'book_store'])->name('secretary.patient.book.store')->middleware('web');
    Route::get('/session/keepalive', function () {
        return response()->json(['status' => 'session refreshed']);
    })->middleware('auth')->name('session.keepalive');
    //Appointement
    Route::post('/secretary/patient/moveToClinic/{appointment}', [SecretaryController::class, 'moveToClinic'])->name('secretary.patient.appointments.moveto.clinic');
    Route::post('/secretary/patient/{appointmentId}/ConfirmPay', [SecretaryController::class, 'ConfirmPay'])->name('secretary.patient.appointments.ConfirmPay');


    // عرض قائمة المواعيد التي تحتاج تأكيد
    Route::get('/secretary/appointments/pending/{doctorId}', [SecretaryAppointmentController::class, 'pendingByDoctor'])->name('secretary.appointments.pending');
    Route::post('/secretary/appointments/{appointment}/confirm', [SecretaryAppointmentController::class, 'confirm1'])->name('secretary.appointment.confirm');
    Route::post('/secretary/appointments/{appointment}/cancel', [SecretaryAppointmentController::class, 'cancel1'])->name('secretary.appointment.cancel');
    Route::post('/secretary/appointments/{doctor}/cancel-all', [SecretaryAppointmentController::class, 'cancelAllUpcoming'])->name('secretary.appointments.cancelAll');

    // Route::post('sendNotification', [SecretaryAppointmentController::class, 'sendNotification'])->name('send.notification');
    //عرض كل المواعيد التي : تخص اليوم الحالي

    Route::get('/secretary/appointments/today', [SecretaryAppointmentController::class, 'todayAppointments'])->name('secretary.appointments.today');
    //ل تحديث الحالة انو في العيادة
    Route::get('/secretary/appointments/today/{appointment}/arrived', [SecretaryAppointmentController::class, 'markArrived'])->name('secretary.appointments.inClinic');

    //عرض الأطباء
    Route::get('/secretary/doctors', [SecretaryDoctorController::class, 'doctors'])->name('secretary.doctors');
    //عرض جدول مواعيد الأطباء الأسبوعي
    Route::get('/secretary/doctors/{id}/schedule', [SecretaryDoctorController::class, 'doctorSchedule'])->name('secretary.doctor.schedule');
    Route::get('/secretary/doctors/{id}/Appointment', [SecretaryDoctorController::class, 'doctorAppointmentsDetails'])->name('secretary.doctor.appointment');


    //عرض المرضى
    Route::get('/secretary/patients', [SecertaryPatientController::class, 'patients'])->name('secretary.patients');
    Route::get('/secretary/patients/{id}/show', [SecertaryPatientController::class, 'patient_show'])->name('secretary.patient.show');
    Route::delete('/secretary/patients/{id}/delete', [SecertaryPatientController::class, 'patient_delete'])->name('secretary.patient.delete');

    //عرض المواعيد
    Route::get('/secretary/appointments', [SecretaryAppointmentController::class, 'index'])->name('secretary.appointments');
    Route::get('/secretary/appointments/{id}/getNearestAvailableSlot', [SecretaryAppointmentController::class, 'getNearestAvailableRangeSlot'])->name('secretary.appointments.getNextAvailableSlot');
    Route::get(
        '/secretary/appointments/{doctor_id}/{date}/{time}/book',
        [SecretaryAppointmentController::class, 'book']
    )->name('secretary.appointments.book');
    Route::post('/secretary/appointments/bookstore', [SecretaryAppointmentController::class, 'bookstore'])->name('secretary.appointments.bookstore');

    //عرض
    Route::get('/secretary/appointments/{appointment}', [SecretaryAppointmentController::class, 'show'])->name('secretary.appointments.show');
    //supplier
    Route::get('/secretary/supllier', [SecretarySupplierController::class, 'index'])->name('secretary.supplier');
    Route::get('/secretary/supllier/create', [SecretarySupplierController::class, 'create'])->name('secretary.supplier.create');
    Route::post('/secretary/supllier/store', [SecretarySupplierController::class, 'store'])->name('secretary.supplier.store');
    Route::get('/secretary/supllier/{supplierId}/edit', [SecretarySupplierController::class, 'edit'])->name('secretary.supplier.edit');
    Route::put('/secretary/supplier/{supplier}/update', [SecretarySupplierController::class, 'update'])
        ->name('secretary.supplier.update');
    Route::delete('/secretary/supllier/{supplierId}/delete', [SecretarySupplierController::class, 'delete'])->name('secretary.supplier.delete');
    Route::delete('/secretary/supllier/deleteAll', [SecretarySupplierController::class, 'deleteAll'])->name('secretary.supplier.deleteAll');

    //material
    Route::get('/secretary/material', [SecretaryMaterialController::class, 'index'])->name('secretary.material');
    Route::get('/secretary/material/create', [SecretaryMaterialController::class, 'create'])->name('secretary.material.create');
    Route::post('/secretary/material/store', [SecretaryMaterialController::class, 'store'])->name('secretary.material.store');
    Route::get('/secretary/material/{materialId}/edit', [SecretaryMaterialController::class, 'edit'])->name('secretary.material.edit');
    Route::post('/secretary/material/{materialId}/update', [SecretaryMaterialController::class, 'update'])->name('secretary.material.update');
    Route::delete('/secretary/material/{materialId}/delete', [SecretaryMaterialController::class, 'delete'])->name('secretary.material.delete');
    Route::delete('/secretary/material/deleteAll', [SecretaryMaterialController::class, 'deleteAll'])->name('secretary.material.deleteAll');
    Route::get('/secretary/material/{materialId}/recommended-suppliers', [SecretaryMaterialController::class, 'recommendedSuppliers'])->name('secretary.material.recommendedSuppliers');


    Route::get('secretary/visits/pending-payments', [SecretaryVisitController::class, 'pendingPayments'])->name('secretary.visits.pendingPayments');
    Route::put('secretary/visits/{id}/confirm-payment', [SecretaryVisitController::class, 'confirmPayment'])->name('secretary.visits.confirmPayment');
    Route::get('/secretary/finance', [FinanceController::class, 'finance'])->name('secretary.finance');
    Route::post('/clinic-bills', [FinanceController::class, 'storeClinicBill'])->name('clinic.bills.store');

    Route::post('secretary/logout', [AdminController::class, 'logout'])->name('secretary.logout');

});

Route::get('/materials/{material}/suppliers', function (Material $material) {
    $suppliers = $material->supplierMaterials()->with('supplier')->get();

    return $suppliers->map(function ($supplierMaterial) {
        return [
            'id' => $supplierMaterial->supplier->id,
            'name' => $supplierMaterial->supplier->sup_name,
            'quantity' => $supplierMaterial->sup_material_quantity,
            'price' => $supplierMaterial->sup_material_price,
            'delivered_at' => $supplierMaterial->sup_material_delivered_at
                ? \Carbon\Carbon::parse($supplierMaterial->sup_material_delivered_at)->format('Y-m-d')
                : 'غير محدد',
            'quality' => $supplierMaterial->sup_material_is_damaged ? 'تالفة' : 'جيدة'
        ];
    });
});
Route::get('/test-notification/{appointmentId}', function ($appointmentId) {
    $appointment = Appointment::findOrFail($appointmentId);
    $service = new AppointementStatusArrivvedNotificationService();

    // اختبار إشعار Firebase
    if ($appointment->patient->user->fcm_token) {
        $result = $service->sendFirebaseNotification(
            $appointment->patient->user->fcm_token,
            'اختبار إشعار',
            'هذا إشعار اختبار فقط',
            $appointment->id
        );

        return response()->json(['success' => $result]);
    }

    return response()->json(['error' => 'لا يوجد token للمستخدم']);
});

require __DIR__ . '/auth.php';

<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Patient_record;
use App\Models\User;
use App\Models\WaitingList;
use App\Notifications\AppointmentConfirmedNotification;
use App\Services\Api\Doctor\DoctorScheduleService;
use App\Services\Secertary\Appointement\AppointementSerivce;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class SecretaryController extends Controller
{
    protected $service, $serviceSchedule;


    public function __construct(AppointementSerivce $service, DoctorScheduleService $serviceSchedule)
    {
        $this->service = $service;
        $this->serviceSchedule = $serviceSchedule;
    }

    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $doctors = Doctor::with(['user', 'appointments', 'doctorSchedule'])->get();

        // فلترة حسب الطبيب إذا تم اختياره
        $doctorId = $request->query('doctor_id');
        $selectedDoctor = null;
        $doctorSchedules = collect();

        if ($doctorId) {
            $selectedDoctor = Doctor::with('doctorSchedule')->find($doctorId);
            $doctorSchedules = $selectedDoctor->doctorSchedule ?? collect();
        }
        // جلب المواعيد القادمة مرتبة حسب التاريخ والوقت
        $upcomingAppointments = Appointment::with(['patient.user', 'doctor', 'waitinglist'])
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'data' => $upcomingAppointments->items(),
                'current_page' => $upcomingAppointments->currentPage(),
                'last_page' => $upcomingAppointments->lastPage()
            ]);
        }


        $waitingPatients = Appointment::with(['patient.user', 'doctor.user', 'waitinglist'])

            ->where('location_type', 'in_Clinic')
            ->whereDate('date', '>=', $today)
            ->where('status', 'confirmed')
            ->whereHas('waitingList', function ($query) {
                $query->where('w_status', 'waiting');
            })
            ->get()
            ->sortBy('waitingList.w_check_in_time')
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'doctor_id' => $item->doctor_id,
                    'patient' => [
                        'photo' => $item->patient->photo,
                        'user' => [
                            'name' => $item->patient->user->name
                        ]
                    ],
                    'doctor' => [
                        'user' => [
                            'name' => $item->doctor->user->name
                        ]
                    ],
                    'waiting_list' => $item->waitingList->first() ? [
                        'w_check_in_time' => $item->waitingList->first()->w_check_in_time
                    ] : null
                ];
            });
        $patientsJsonClinic = $waitingPatients->toJson();

        // المرضى على الطريق (سيصلون قريباً)
        $onStreetPatients = Appointment::with(['patient.user', 'doctor'])
            ->whereDate('date', $today)
            ->where('location_type', 'on_Street')
            ->where('status', 'confirmed')
            ->get();

        // المرضى عند الطبيب
        $DoctorPatients = Appointment::with(['patient.user', 'doctor', 'waitinglist', 'visit'])
            ->where('location_type', 'at_Doctor')
            ->whereDate('date', $today)
            ->where('status', 'confirmed')
            ->whereHas('waitingList', function ($query) {
                $query->where('w_status', 'in_progress');
            })
            ->get()
            ->sortBy('waitingList.w_start_time');
        //dd( $DoctorPatients);

        $patientsJsonDoctor = $DoctorPatients->map(fn($item) => ['doctor_id' => $item->doctor_id])->toJson();

        // المرضى الجاهزين للدفع
        $paymentPatients = Appointment::with(['patient.user', 'doctor', 'waitinglist', 'visit'])
            ->where('location_type', 'in_Payment')
            ->where('status', 'completed')
            ->whereHas('waitingList', function ($query) {
                $query->where('w_status', 'done');
            })
            ->whereHas('visit', function ($query) {
                $query->where('v_status', 'in_payment');
            })
            ->get()
            ->sortBy('visit.v_ended_at');

        $patientsJsonPayment = $paymentPatients->map(fn($item) => ['doctor_id' => $item->doctor_id])->toJson();

        // المرضى الذين انتهت زيارتهم
        $finishedPatients = Appointment::with(['patient.user', 'doctor'])
            ->whereDate('date', $today)
            ->where('location_type', 'finished')
            ->where('status', 'completed')
            ->get();

        // معالجة تاريخ التقويم
        $currentDate = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::now();
        $prevMonth = $currentDate->copy()->subMonth()->toDateString();
        $nextMonth = $currentDate->copy()->addMonth()->toDateString();

        return view('secretary.home.dashboard', compact(
            'waitingPatients',
            'onStreetPatients',
            'paymentPatients',
            'finishedPatients',
            'today',
            'doctors',
            'patientsJsonClinic',
            'DoctorPatients',
            'patientsJsonDoctor',
            'patientsJsonPayment',
            'currentDate',
            'prevMonth',
            'nextMonth',
            'doctorId',
            'selectedDoctor',
            'doctorSchedules',
            'upcomingAppointments'
        ));
    }
    // نقل المريض من الطريق إلى العيادة (إضافة إلى قائمة الانتظار)
    public function moveToClinic($appointmentId)
    {
        // تحقق من وجود الموعد والتأكد أن تاريخ الموعد هو اليوم
        $appointment = Appointment::where('id', $appointmentId)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$appointment) {
            return back()->withErrors(['error' => 'الموعد غير موجود أو ليس ليوم اليوم']);
        }

        // تحديث حالة الموعد لمكان "في العيادة"
        $appointment->update([
            'location_type' => 'in_Clinic'
        ]);

        // التحقق من وجود سجل في قائمة الانتظار لهذا الموعد
        $waitingEntry = WaitingList::where('appointment_id', $appointmentId)->first();

        if (!$waitingEntry) {
            // إنشاء سجل جديد في قائمة الانتظار
            WaitingList::create([
                'appointment_id' => $appointmentId,
                'w_status' => 'waiting',
                'w_check_in_time' => now()
            ]);
        } else {
            // تحديث السجل الحالي إن وجد
            $waitingEntry->update([
                'w_status' => 'waiting',
                'w_check_in_time' => now()
            ]);
        }

        return back()->with('status', 'تم نقل المريض إلى العيادة وإضافته لقائمة الانتظار');
    }

    public function ConfirmPay($appointmentId)
    {
        // جلب الموعد مع الزيارة المرتبطة
        $appointment = Appointment::with('visit')->find($appointmentId);

        if (!$appointment || !$appointment->visit) {
            return back()->withErrors(['error' => 'الموعد أو الزيارة غير موجودة']);
        }

        $visit = $appointment->visit()->first(); // جلب موديل واحد وليس Collection

        if (!$visit) {
            return back()->withErrors(['error' => 'الزيارة غير موجودة']);
        }

        // تحديث حالة الزيارة
        $visit->update([
            'v_paid' => true,
            'v_status' => 'completed',
            'v_ended_at' => $visit->v_ended_at ?? now(),
        ]);


        // تحديث حالة الموعد إذا أحببت
        $appointment->update([
            'status' => 'completed',
            'location_type' => 'finished',
        ]);

        return back()->with('status', 'تم تأكيد الدفع وإنهاء الزيارة بنجاح');
    }
    public function index1()
    {
        $today = Carbon::today()->toDateString();
        $now = now();

        // جلب الأطباء مع المواعيد حسب الحالات المطلوبة
        $doctors = Doctor::with(['user'])
            ->with([
                'appointments' => function ($query) use ($today, $now) {
                    $query->where(function ($q) use ($today, $now) {
                        $q->whereDate('date', $today)
                            ->whereIn('status', ['confirmed', 'completed', 'canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary']);
                    })->orWhere(function ($q) use ($now) {
                        $q->where('status', 'pending')
                            ->where(function ($q2) use ($now) {
                                $q2->where('date', '>', $now->toDateString())
                                    ->orWhere(function ($q3) use ($now) {
                                        $q3->whereDate('date', $now->toDateString())
                                            ->whereTime('start_time', '>', $now->toTimeString());
                                    });
                            });
                    })->with('patient.user');
                }
            ])->get();

        // الإحصائيات العامة لجميع المواعيد لليوم الحالي
        $globalCounts = Appointment::whereDate('date', $today)
            ->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status LIKE 'canceled%' THEN 1 ELSE 0 END) as canceled
        ")->first();

        return view('secretary.home.dashboard', compact('doctors', 'globalCounts', 'today'));
    }
    public function patient_add()
    {
        return view('secretary.home.patient_add');
    }
    public function patient_store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
        ]);
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'role' => 'patient',
                'created_by' => 'secretary',
                'created_by_user_id' => Auth::user()->secretary->id,
                'has_changed_credentials' => false,
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'photo' => 'avatars/6681221.png',
            ]);
            Patient_record::create([
                'patient_id' => $patient->id,
            ]);

            DB::commit();

            return redirect()->route('secretary.patients')->with('status', 'تمت إضافة المريض بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة المريض'])->withInput();
        }
    }
    public function patient()
    {
        return view('secretary.patient');
    }
    //عرض كل الأطباء
    public function doctors()
    {
        $doctors = Doctor::with('user', 'doctorProfile', 'room')->get();
        return view('secretary.doctors', compact('doctors'));
    }

    public function book_add()
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();

        return view('secretary.home.appointment-add', compact('patients', 'doctors'));

    }


    public function monthDays(Request $request, $doctorId)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|numeric|between:1,12'
        ]);

        try {
            $data = $this->serviceSchedule->getMonthDaysWithStatus($doctorId, $request->year, $request->month);
            return response()->json(collect($data));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

    }
    public function daySlots(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {

            $data = $this->serviceSchedule->getDaySlots($doctorId, $request->date);
            return response()->json(collect($data));

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function bookAppointment(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time' => 'required|string'
        ]);

        [$start, $end] = explode('-', $request->time);

        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('date', $request->date)
            ->where('start_time', $start)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'الوقت محجوز بالفعل'], 400);
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'pending'
        ]);

        return response()->json(['status' => 'تم حجز الموعد بنجاح']);
    }

    public function book_store(Request $request)
    {
        try {
            $validated = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'patient_id' => 'required|exists:patients,id',
                'date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'type_visit' => 'required|in:appointment,review',
                'location_type' => 'required|in:in_Home,on_Street,in_Clinic',
                'arrivved_time' => 'required|integer|min:1'
            ]);
            $minutes = $request->arrivved_time;
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;

            $validated['arrivved_time'] = sprintf('%02d:%02d:00', $hours, $mins);

            // التحقق من شرط الموقع إذا لم يكن الموعد اليوم
            $appointmentDate = Carbon::parse($validated['date']);
            if (!$appointmentDate->isToday() && in_array($validated['location_type'], ['on_Street', 'in_Home'])) {
                throw new \Exception('لا يمكن حجز موعد في الشارع أو في العيادة إلا لمواعيد اليوم نفسه');
            }

            $doctor = Doctor::with('doctorSchedule')->findOrFail($validated['doctor_id']);
            $dayName = \Carbon\Carbon::parse($validated['date'])->format('l');
            $schedule = $doctor->doctorSchedule->firstWhere('day', $dayName);
            // تحقق من الحجز المكرر لنفس المريض
            $existingAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
                ->where('patient_id', $validated['patient_id'])
                ->whereDate('date', $validated['date'])
                ->where('start_time', $validated['start_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();

            if ($existingAppointment) {
                throw new \Exception('هذا المريض لديه موعد مسبق في نفس التوقيت');
            }
            // تحقق من سعة الفتحة
            $bookedCount = Appointment::where('doctor_id', $validated['doctor_id'])
                ->whereDate('date', $validated['date'])
                ->where('start_time', $validated['start_time'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            if ($bookedCount >= $schedule->patients_per_hour) {
                throw new \Exception('تم امتلاء هذا التوقيت');
            }
            $appointment = Appointment::create([
                'doctor_id' => $validated['doctor_id'],
                'patient_id' => $validated['patient_id'],
                'secretary_id' => Auth::user()->secretary->id,
                'date' => $validated['date'],
                'day' => Carbon::parse($validated['date'])->format('l'),
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => 'confirmed',
                'location_type' => $validated['location_type'],
                'created_by' => 'secretary',
                'type_visit' => $validated['type_visit'],
                'arrivved_time' => $validated['arrivved_time'],
            ]);

            // إذا الموعد اليوم → تحديث السجل الطبي
            if (Carbon::parse($validated['date'])->isToday()) {
                $record = $appointment->patient->patient_record;
                if ($record) {
                    $record->update([
                        'patient_id' => $validated['patient_id'],
                        'profile_submitted' => 1,
                        'diseases_submitted' => 1,
                        'operations_submitted' => 1,
                        'medicalAttachments_submitted' => 1,
                        'allergies_submitted' => 1,
                        'family_history_submitted' => 1,
                        'medications_submitted' => 1,
                        'medicalfiles_submitted' => 1
                    ]);
                }
            }

            // إشعار للمريض
            $user = $appointment->patient->user;
            $user->notify(new AppointmentConfirmedNotification($appointment));

            if (!empty($user->fcm_token)) {
                $this->sendFirebaseNotification(
                    $user->fcm_token,
                    'تم تأكيد الموعد',
                    'موعدك بتاريخ ' . $appointment->date . ' الساعة ' . $appointment->start_time
                );
            }

            // ✅ التفريق بين AJAX & Form submit
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حجز الموعد بنجاح',
                    'appointment' => $appointment
                ]);
            }

            return redirect()
                ->route('secretary.appointments')
                ->with('status', 'تم حجز الموعد بنجاح');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل الحجز: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => 'فشل الحجز: ' . $e->getMessage()])
                ->withInput();
        }
    }


    public function sendFirebaseNotification($token, $title, $body)
    {
        if (empty($token)) {
            \Log::warning("محاولة إرسال إشعار بدون FCM Token");
            return false;
        }

        try {
            $messaging = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials_file'))
                ->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData(['type' => 'appointment_update']);

            $messaging->send($message);
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error: ' . $e->getMessage() . $token);

            return false;
        }
    }

}

<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Patient_record;
use App\Models\User;
use App\Services\Secertary\Appointement\AppointementSerivce;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecretaryController extends Controller
{
    protected $service;

    public function __construct(AppointementSerivce $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $today = Carbon::today()->toDateString();
        $doctors = Doctor::with([
            'appointments' => function ($query) use ($today) {
                $query->where('date', $today)
                    ->whereIn('status', ['confirmed', 'completed', 'canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
                    ->with(['patient.user']);
            },
            'user'
        ])->get();

        // الإحصائيات الإجمالية لكل الأطباء
        $globalCounts = Appointment::whereDate('date', $today)
            ->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status LIKE 'canceled%' THEN 1 ELSE 0 END) as canceled
        ")->first();

        return view('secretary.home.dashboard', compact('doctors', 'globalCounts', 'today'));
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




}

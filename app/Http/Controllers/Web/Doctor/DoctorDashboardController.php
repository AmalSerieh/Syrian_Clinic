<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorMaterial;
use App\Models\Material;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;


class DoctorDashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->has_changed_credentials) {
            return redirect()->route('doctor.first-login');
        }
        $doctorId = Auth::user()->doctor->id;
        $today = Carbon::today()->toDateString();


        $waitingPatients = Appointment::with(['patient.user', 'doctor.user', 'waitinglist'])

            ->where('location_type', 'in_Clinic')
            ->where('doctor_id', $doctorId) // ✅ فقط مواعيد الطبيب الحالي
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
                    'doctor_id' => Auth::user()->doctor->id,
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
            })->values(); // ترتيب مصفوفة بالاندكس من 0
        $currentPatient = Appointment::with([
            'patient.user',
            'doctor.user',
            'waitingList',
            'visit',
            'patient.patient_record.patient_profile',
            'patient.patient_record.allergies',
            'patient.patient_record.diseases',
            'patient.patient_record.medications',
        ])
            ->where('location_type', 'at_Doctor')       // المريض عند الطبيب
            ->where('doctor_id', $doctorId)            // فقط للطبيب الحالي
            ->whereDate('date', $today)                // اليوم
            ->where('status', 'confirmed')             // مؤكد
            ->whereHas('visit', function ($query) {
                $query->where('v_status', 'active')   // حالة الزيارة فعالة
                    ->whereDate('v_started_at', Carbon::today()); // تمت اليوم
            })
            ->get()
            ->map(function ($item) {
                $visit = $item->visit->first();
                $waiting = $item->waitingList->first();
                $profile = $item->patient->patient_record->patient_profile ?? null;
                $allergies = $item->patient->patient_record->allergies ?? collect();
                $diseases = $item->patient->patient_record->diseases ?? collect();
                $medications = $item->patient->patient_record->medications ?? collect();

                return [
                    'id' => $item->id,
                    'doctor_id' => $item->doctor_id,
                    'patient' => [
                        'id' => $item->patient_id,
                        'photo' => $item->patient->photo,
                        'user' => [
                            'name' => $item->patient->user->name
                        ],
                        'record' => $item->patient->patient_record->id,
                        // من patient_profile
                        'weight' => $profile->weight ?? null,
                        'height' => $profile->height ?? null,
                        'gender' => $profile->gender ?? null,
                        'blood_group' => $profile->blood_type ?? null,
                        'smoker' => $profile->smoker ?? null,
                        'alcohol' => $profile->alcohol ?? null,
                        'drugs' => $profile->drug ?? null,

                        //allergies من
                        'allergies' => $allergies->map(function ($allergy) {
                            return [
                                'aller_power' => $allergy->aller_power,
                                'aller_name' => $allergy->aller_name,
                                'aller_type' => $allergy->aller_type,
                                'aller_cause' => $allergy->aller_cause,
                                'aller_treatment' => $allergy->aller_treatment,
                                'aller_pervention' => $allergy->aller_pervention,
                                'aller_reasons' => $allergy->aller_reasons,
                            ];
                        }),
                        //diseases من
                        'diseases' => $diseases->map(function ($disease) {
                            return [
                                'd_name' => $disease->d_name,
                                'd_diagnosis_date' => $disease->d_diagnosis_date,
                                'd_doctor' => $disease->d_doctor,
                                'd_advice' => $disease->d_advice,
                                'd_prohibitions' => $disease->d_prohibitions,
                            ];
                        }),
                        //medications
                        'medications' => $medications->map(function ($medication) {
                            return [
                                'med_type' => $medication->med_type,
                                'med_name' => $medication->med_name,
                                'med_start_date' => $medication->med_start_date,
                                'med_frequency' => $medication->med_frequency,
                                'med_dose' => $medication->med_dose,
                                'med_timing' => $medication->med_timing,
                                'med_prescribed_by_doctor' => $medication->med_prescribed_by_doctor,
                            ];
                        }),
                    ],
                    'doctor' => [
                        'user' => [
                            'name' => $item->doctor->user->name
                        ]
                    ],
                    'waiting_list' => $waiting ? [
                        'w_start_time' => $waiting->w_start_time
                    ] : null,
                    'visit' => $visit ? [
                        'id' => $visit->id,
                        'v_started_at' => $visit->v_started_at,
                        'v_status' => $visit->v_status
                    ] : null,
                ];
            })
            ->first(); // ⚠️ فقط مريض واحد
        // dd($currentPatient);
        // ✅ الآن هنا بالضبط تحط كود prescriptions
        $prescriptions = collect();
        if ($currentPatient && isset($currentPatient['visit']['id'])) {
            $visitId = $currentPatient['visit']['id'];

            $prescriptions = \App\Models\Prescription::with('items')
                ->where('visit_id', $visitId)
                ->get()
                ->map(function ($prescription) {
                    return [
                        'id' => $prescription->id,
                        'created_at' => $prescription->created_at->format('Y-m-d H:i'),
                        'items' => $prescription->items->map(function ($item) {
                            return [
                                'pre_type' => $item->pre_type,
                                'pre_name' => $item->pre_name,
                                'pre_scientific' => $item->pre_scientific,
                                'pre_trade' => $item->pre_trade,
                                'pre_start_date' => $item->pre_start_date,
                                'pre_end_date' => $item->pre_end_date,
                                'pre_frequency' => $item->pre_frequency,
                                'pre_frequency_value' => $item->pre_frequency_value,
                                'pre_dosage_form' => $item->pre_dosage_form,
                                'pre_dose' => $item->pre_dose,
                                'pre_timing' => $item->pre_timing,
                                'pre_quantity_per_dose' => $item->pre_quantity_per_dose,
                                'pre_total_quantity' => $item->pre_total_quantity,
                                'pre_prescribed_by_doctor' => $item->pre_prescribed_by_doctor,
                                'instructions' => $item->instructions,
                                'pre_alternatives' => $item->pre_alternatives ? json_decode($item->pre_alternatives, true) : [],
                            ];
                        }),
                    ];
                });
        }
        $materials = Material::where('material_quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('material_expiration_date')   // لو ما عندها تاريخ انتهاء
                    ->orWhere('material_expiration_date', '>', now()); // أو لسا ما انتهت
            })
            ->whereHas('supplierMaterials', function ($q) {
                $q->where('sup_material_quantity', '>', 0)
                    ->where('sup_material_is_damaged', false)
                ;
            })
            ->get();
        // ✅ المواد المستهلكة في الزيارة الحالية
        $doctorMaterials = collect();
        if ($currentPatient && isset($currentPatient['visit']['id'])) {
            $doctorMaterials = DoctorMaterial::with('material')
                ->where('visit_id', $currentPatient['visit']['id'] ?? null)
                ->get();
        }
        $totalConsumption = DoctorMaterial::where('visit_id', $currentPatient['visit']['id'] ?? 0)
            ->sum('dm_total_price');

        $services = Service::all();
        return view('doctor.home.dashboard', compact(
            'waitingPatients',
            'currentPatient',
            'prescriptions',
            'materials',
            'doctorMaterials',
            'totalConsumption',
            'services'
        ));
        // return redirect()->route('doctor-profile.create');
    }
    public function showForceChangeForm()
    {

        return view('doctor.editauth.first-login'); // view فيها فورم تغيير الإيميل والباسورد
    }

    public function updateCredentials(Request $request)
    {
        //dd($request->all());
        \Log::info('UpdateCredentials Request:', $request->all());

        $request->validate([
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->update([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'has_changed_credentials' => true,
        ]);

        return redirect()->route('doctor.dashboard')->with('status', 'Credentials updated successfully!');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = $request->user();
        $doctor = $user->doctor;

        if (!$doctor) {
            return back()->withErrors(['photo' => 'لا يوجد حساب طبيب مرتبط.']);
        }

        if ($doctor->photo && Storage::disk('public')->exists($doctor->photo)) {
            Storage::disk('public')->delete($doctor->photo);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('doctor-profile-photos', 'public');
            $doctor->photo = $path;
            $doctor->save();
        }

        return back()->with('status', 'photo-updated');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // للتحقق: عرض محتويات الـ Request كاملاً
        \Log::info('Request data:', $request->all());
        \Log::info('Files:', $request->file() ?: ['no files']);

        if ($request->hasFile('photo')) {
            \Log::info('Photo file details:', [
                'name' => $request->file('photo')->getClientOriginalName(),
                'size' => $request->file('photo')->getSize(),
                'mime' => $request->file('photo')->getMimeType()
            ]);
        }

        // القواعد مع تجاهل البريد الحالي للمستخدم
        $rules = [
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', 'min:8', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        $validated = $request->validate($rules);

        \Log::info('Validation passed');

        DB::beginTransaction();

        try {
            // تحديث البيانات الأساسية
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            // تحديث كلمة المرور إذا تم إدخالها
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            // تحديث الصورة إذا وجدت
            if ($request->hasFile('photo')) {
                \Log::info('Processing photo upload');

                // حذف الصورة القديمة إذا كانت موجودة
                /*  if ($user->doctor && $user->doctor->photo) {
                     Storage::disk('public')->delete($user->doctor->photo);
                 } */

                // حفظ الصورة الجديدة
                $path = $request->file('photo')->store('doctor-profile-photos', 'public');

                // تأكد من وجود سجل doctor
                if (!$user->doctor) {
                    $user->doctor()->create(['photo' => $path]);
                } else {
                    $user->doctor->photo = $path;
                    $user->doctor->save();
                }

                \Log::info('Photo saved to: ' . $path);
            }

            $user->save();
            DB::commit();

            return back()->with('success', 'تم تحديث بيانات الحساب بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating profile: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    public function show(Doctor $doctor)
    {
        // العلاقات
        $profile = $doctor->doctorProfile;
        $schedules = $doctor->doctorSchedule;

        // تقسيم المواعيد
        $timeRanges = [];
        /* foreach ($schedules as $schedule) {
            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);
            while ($start->lt($end)) {
                $next = $start->copy()->addMinutes($schedule->appointment_duration);
                if ($next->gt($end)) {
                    $next = $end;
                }

                $timeRanges[] = [
                    'day' => $schedule->day,
                    'from' => $start->format('H:i'),
                    'to' => $next->format('H:i'),
                ];

                $start = $next;
            }
            $schedules = DoctorSchedule::all(); // أو جدول الطبيب الحالي
 */
        $timeRanges = [];

        foreach ($schedules as $schedule) {
            $start = Carbon::createFromTimeString($schedule->start_time);
            $end = Carbon::createFromTimeString($schedule->end_time);

            while ($start->lt($end)) {
                $next = $start->copy()->addHour();
                if ($next->gt($end)) {
                    $next = $end->copy();
                }

                $timeRanges[] = [
                    'day' => $schedule->day,
                    'from' => $start->format('H:i'),
                    'to' => $next->format('H:i'),
                ];

                $start = $next;
            }
        }



        return view('doctor.show', compact('doctor', 'profile', 'schedules', 'timeRanges'));
    }


}

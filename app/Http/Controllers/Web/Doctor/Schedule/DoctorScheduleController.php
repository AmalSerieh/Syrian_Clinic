<?php
namespace App\Http\Controllers\Web\Doctor\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $schedules = auth()->user()->doctor->doctorSchedule()
            ->orderByRaw("FIELD(day, 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')")
            ->orderBy('start_time')
            ->get();
        // dd( Auth::user());
        // جلب معرف الطبيب الحقيقي (Doctor وليس DoctorProfile)
        $doctorId = Auth::user()->doctor->id;

        // جلب جميع المواعيد لهذا الطبيب
        // $schedules = DoctorSchedule::where('doctor_id', $doctorId)->get();

        // التحقق إذا ما في مواعيد
        if ($schedules->isEmpty()) {
            return redirect()->route('doctor-schedule.create')
                ->with('info', 'لم يتم إدخال مواعيد من قبل، يرجى إدخال موعد جديد.');
        }

        return view('doctor.schedules.index', compact('schedules'));
    }



    public function indexAnother()
    {
        $schedules = DoctorSchedule::all(); // أو جدول الطبيب الحالي

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


        return view('doctor.schedules.indexAnother', compact('timeRanges', 'schedules'));
    }


    public function create()
    {
        $doctor = auth()->user()->doctor;
        $profile = $doctor->doctorProfile;

        // نتحقق أن كل الحقول موجودة
        if (
            empty($profile->cer_place) ||
            empty($profile->cer_name) ||
            empty($profile->cer_images) ||
            empty($profile->cer_date) ||
            empty($profile->exp_place) ||
            empty($profile->exp_years) ||
            empty($profile->biography) ||
            empty($profile->date_birth)
        ) {
            return redirect()->route('doctor-profile.edit', ['id' => $profile->id]) // أو أي صفحة تعديل الملف المهني
                ->with('status', 'يرجى إكمال جميع بيانات الملف المهني قبل إضافة جدول المواعيد.');
        }

        return view('doctor.schedules.create');
    }

    public function store(Request $request)
    {
        $doctor = Auth::user()->doctor;

        // ✅ تأكد أن ملفه المهني مكتمل
        $profile = $doctor->doctorProfile;
        $requiredFields = [
            'cer_place',
            'cer_name',
            'cer_images',
            'cer_date',
            'exp_place',
            'exp_years',
            'biography',
            'date_birth'
        ];
        foreach ($requiredFields as $field) {
            if (empty($profile->$field)) {
                return back()->withErrors([
                    'profile' => 'يرجى إكمال جميع بيانات الملف المهني قبل إضافة جدول المواعيد.'
                ])->withInput();
            }
        }
        // ✅ تحقق من صحة البيانات
        $validated = $request->validate([
            'day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'patients_per_hour' => 'required|integer|min:1',
        ]);


        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        // حساب الفرق بالدقائق
        $durationInMinutes = $start->diffInMinutes($end);

        $doctorId = Auth::user()->doctor->id;
        $workstart = Carbon::parse('07:00');
        $workend = Carbon::parse('23:00');
        // ✅ التحقق من ساعات العمل (8 صباحاً - 8 مساءً)
        if ($start->lt($workstart) || $end->gt($workend)) {
            return back()->withErrors([
                'schedule' => 'المواعيد يجب أن تكون بين الساعة 8 صباحاً و 8 مساءً. الوقت المحدد: ' .
                    $start->format('H:i') . ' - ' . $end->format('H:i')
            ])->withInput();
        }


        // عدد المرضى في الساعة
        $patientsPerHour = $request->patients_per_hour;

        // حساب المدة لكل موعد
        $appointmentDuration = floor(60 / $patientsPerHour); // بالدقائق

        // حساب إجمالي المرضى = عدد الساعات × المرضى بالساعة
        $maxPatients = floor(($durationInMinutes / 60) * $patientsPerHour);
        // حساب عدد المواعيد
        $numberOfAppointments = ceil($durationInMinutes / $appointmentDuration);

        // تحقق من التداخل
        $hasConflict = DoctorSchedule::where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'duplicate' => 'يوجد تداخل مع موعد آخر في نفس اليوم. يرجى اختيار وقت مختلف.',
            ])->withInput();
        }

        DoctorSchedule::create([
            'doctor_id' => $doctorId,
            'day' => $request->day,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'patients_per_hour' => $validated['patients_per_hour'],
            'max_patients' => $maxPatients,
            'appointment_duration' => $appointmentDuration,
        ]);

        return redirect()->route('doctor-schedule.index')
            ->with('success', 'تم حفظ الموعد بنجاح.');
    }


    public function edit(DoctorSchedule $schedule)
    {
        //  $this->authorize('update', $schedule); // إن أردت استخدام Policies
        $doctorId = Auth::user()->doctor->id;

        if ($schedule->doctor_id !== $doctorId) {
            abort(403, 'غير مصرح لك بتعديل هذا الموعد.');
        }

        return view('doctor.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, DoctorSchedule $schedule)
    {
        $validated = $request->validate([
            'day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'patients_per_hour' => 'required|integer|min:1|max:60',
        ]);

        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);
        // حساب الفرق بالدقائق
        $durationInMinutes = $start->diffInMinutes($end);

        $doctorId = Auth::user()->doctor->id;

        $workstart = Carbon::parse('08:00');
        $workend = Carbon::parse('20:00');
        // ✅ التحقق من ساعات العمل (8 صباحاً - 8 مساءً)
        if ($start->lt($workstart) || $end->gt($workend)) {
            return back()->withErrors([
                'schedule' => 'المواعيد يجب أن تكون بين الساعة 8 صباحاً و 8 مساءً. الوقت المحدد: ' .
                    $start->format('H:i') . ' - ' . $end->format('H:i')
            ])->withInput();
        }


        // عدد المرضى في الساعة
        $patientsPerHour = $request->patients_per_hour;
        // حساب المدة لكل موعد
        $appointmentDuration = intval(60 / $patientsPerHour); // بالدقائق

        // حساب إجمالي المرضى = عدد الساعات × المرضى بالساعة
        $maxPatients = intval($durationInMinutes / $appointmentDuration);
        // حساب عدد المواعيد
        $numberOfAppointments = ceil($durationInMinutes / $appointmentDuration);
        //  dd($numberOfAppointments,$appointmentDuration,$maxPatients,$durationInMinutes,$numberOfAppointments,$start,$end);

        // تحقق من التداخل
        $hasConflict = DoctorSchedule::where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'duplicate' => 'يوجد تداخل مع موعد آخر في نفس اليوم. يرجى اختيار وقت مختلف.',
            ])->withInput();
        }
        $schedule->update([
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'appointment_duration' => $appointmentDuration,
            'max_patients' => $maxPatients,
        ]);

        return redirect()->route('doctor-schedule.index')->with('info', 'تم تحديث جدول الدوام بنجاح');

    }


    public function destroy(DoctorSchedule $schedule)
    {
        $doctorId = Auth::user()->doctor->id;

        // التأكد أن الطبيب يملك هذا الموعد
        if ($schedule->doctor_id !== $doctorId) {
            abort(403, 'غير مصرح لك بحذف هذا الموعد.');
        }

        $schedule->delete();

        return redirect()->route('doctor-schedule.index')
            ->with('success', 'تم حذف الموعد بنجاح.');
    }


}

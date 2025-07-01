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
        return view('doctor.schedules.create');
    }

    public function store(Request $request)
    {
        $doctorId = Auth::user()->doctor->id;

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);

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

        // حساب الفرق بالدقائق
        $durationInMinutes = $start->diffInMinutes($end);

        // عدد المرضى في الساعة
        $patientsPerHour = $request->patients_per_hour;

        // حساب المدة لكل موعد
        $appointmentDuration = floor(60 / $patientsPerHour); // بالدقائق

        // حساب إجمالي المرضى = عدد الساعات × المرضى بالساعة
        $maxPatients = floor(($durationInMinutes / 60) * $patientsPerHour);

        DoctorSchedule::create([
            'doctor_id' => $doctorId,
            'day' => $request->day,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
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
        $request->validate([
            'day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'patients_per_hour' => 'required|integer|min:1|max:60',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $totalMinutes = $end->diffInMinutes($start);

        $appointment_duration = intval(60 / $request->patients_per_hour);
        $max_patients = intval($totalMinutes / $appointment_duration);

        // التأكد من عدم تعارض المواعيد
        $hasConflict = DoctorSchedule::where('doctor_id', auth()->id())
            ->where('day', $request->day)
            //   ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors(['يوجد تعارض مع موعد آخر']);
        }

        $schedule->update([
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'appointment_duration' => $appointment_duration,
            'max_patients' => $max_patients,
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

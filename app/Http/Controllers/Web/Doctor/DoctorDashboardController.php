<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        if (!auth()->user()->has_changed_credentials) {
            return redirect()->route('doctor.first-login');
        }
        return view('doctor.home.dashboard');
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

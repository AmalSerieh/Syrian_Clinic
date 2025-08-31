<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\StoreSecretaryRequest;
use App\Http\Requests\Admin\UpdateSecretaryRequest;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\Room;
use App\Models\Secretary;
use App\Models\User;
use App\Repositories\Admin\DashboardRepositoryInterface;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService, DashboardRepositoryInterface $dashboardRepositoryInterface)
    {
        $this->dashboardService = $dashboardService;
        $this->dashboardRepositoryInterface = $dashboardRepositoryInterface;
    }
    public function index()
    {
        $secretary = $this->dashboardService->getDashboardData();
        return view('admin.home.dashboard', $secretary);
        /* $secretary = User::where('role', 'secretary')->latest()->first(); // فقط سكرتيرة واحدة
        return view('admin.home.dashboard', compact('secretary')); */
    }
    public function secretary_add()
    {
        if ($this->dashboardService->shouldRedirectToList()) {
            return redirect()->route('admin.secretary')->with('message', 'تمت إضافة السكرتيرة بالفعل.');
        }
        return view('admin.home.secretary-add');
    }

    public function secretary_store(StoreSecretaryRequest $request)
    {

        if ($this->dashboardRepositoryInterface->secretaryExists()) {
            return back()
                ->withErrors(['role' => 'يوجد بالفعل حساب سكرتيرة في النظام'])
                ->withInput();
        }

        try {
            // تنفيذ الخدمة
            $this->dashboardService->storeSecretary($request->validated());
            return redirect()->route('admin.secretary')->with('message', 'تمت إضافة السكرتيرة بنجاح!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة السكرتيرة'])->withInput();
        }
    }

    public function secretary()
    {
        $data = $this->dashboardService->getDashboardData();
        $secretary = $data['secretary']; // الوصول للمفتاح الصحيح

        // فقط سكرتيرة واحدة
        return view('admin.home.secretary', compact('secretary'));
    }
    public function secretary_replace($id)
    {
        $secretary = $this->dashboardService->getSecretaryById($id);
        return view('admin.home.secretary-edit', compact('secretary'));
    }

    public function secretary_update(UpdateSecretaryRequest $request)
    {
        try {
            $this->dashboardService->updateSecretary($request->validated());

            return redirect()
                ->route('admin.secretary')
                ->with('status', '✅ تم استبدال السكرتيرة بنجاح');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'users_email_unique')) {
                return back()
                    ->withErrors(['email' => '⚠️ البريد الإلكتروني مستخدم مسبقًا.'])
                    ->withInput();
            }

            return back()
                ->withErrors(['error' => 'حدث خطأ غير متوقع أثناء تحديث السكرتيرة.'])
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function doctor_add()
    {

        $availableRooms = $this->dashboardService->getAvailableRooms();
        // إذا لم يكن هناك أي غرف متاحة → رجوع برسالة خطأ

        if ($availableRooms->isEmpty()) {
            return redirect()->back()->with('status', 'لا توجد غرف متاحة حالياً. تم الوصول إلى الحد الأقصى لعدد الأطباء في جميع الغرف.');
        }


        return view('admin.doctor.doctor-add', ['rooms' => $availableRooms]);
    }

    public function doctor_store(StoreDoctorRequest $request)
    {
        try {
            $this->dashboardService->storeDoctor($request->validated());
            return redirect()->route('admin.doctor')->with('status', 'تمت إضافة الطبيب بنجاح!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function doctor()
    {
        $doctors = $this->dashboardService->getAllDoctors();
        $roomsAreFull = $this->dashboardService->checkIfRoomsAreFull();


        return view('admin.doctor.doctor', compact('doctors', 'roomsAreFull'));
    }
    public function doctor_details($id)
    {
        $doctor = Doctor::with(['user', 'doctorProfile', 'room'])->findOrFail($id);

        return view('admin.doctor.doctor-details', compact('doctor'));
    }
    public function doctor_edit($id)
    {
        $doctor = Doctor::with('user', 'doctorProfile', 'room')->findOrFail($id);
        $rooms = Room::all(); // لعرض الغرف المتاحة
        return view('admin.doctor.doctor-edit', compact('doctor', 'rooms'));
    }
    public function doctor_update(Request $request, $id)
    {
        $doctor = Doctor::with('user', 'doctorProfile')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $doctor->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $doctor->update([
            'room_id' => $validated['room_id'],
        ]);

        return redirect()->route('admin.doctor')->with('message', 'تم تحديث بيانات الطبيب بنجاح');
    }
    public function doctor_destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        // حذف المستخدم المرتبط
        $doctor->user()->delete();
        $doctor->doctorProfile()->delete();
        // سيتم حذف doctor_profile تلقائياً إذا مفعّل cascading في العلاقة

        return redirect()->route('admin.doctor')->with('message', 'تم حذف الطبيب بنجاح!');
    }

    public function reports()
    {
        return view('admin.home.reports');

    }
    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); // إذا كان يستخدم نفس الحارس العام

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login'); // أو أي Route مخصص لصفحة دخول الأدمن
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $doctors = Doctor::with('user')
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%');
            })
            ->get();

        $roomsAreFull = $this->dashboardService->checkIfRoomsAreFull();

        return view('admin.doctor.doctor', compact('doctors', 'roomsAreFull', 'query'));
    }




}

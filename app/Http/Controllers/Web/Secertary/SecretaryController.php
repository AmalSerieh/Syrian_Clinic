<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Patient_record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecretaryController extends Controller
{

    public function index()
    {
        return view('secretary.dashboard');
    }
    public function patient_add()
    {
        return view('secretary.patient-add');
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
                'created_by_user_id' => auth()->id(),
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'photo' => 'avatars/6681221.png',
            ]);
            Patient_record::create([
                'patient_id' => $patient->id,
            ]);

            DB::commit();

            return redirect()->route('secretary.patient')->with('message', 'تمت إضافة المريض بنجاح!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'حدث خطأ أثناء إضافة المريض'])->withInput();
        }
    }
    public function patient()
    {
        return view('secretary.patient');
    }
}

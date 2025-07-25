<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
class SecertaryPatientController extends Controller
{
    public function patients()
    {
        $patients = Patient::with(['user', 'patient_record', 'appointments'])
            ->get()
            ->map(function ($patient) {
                $completedRecord = $patient->patient_record
                    ? collect([
                        $patient->patient_record->profile_submitted,
                        $patient->patient_record->diseases_submitted,
                        $patient->patient_record->operations_submitted,
                        $patient->patient_record->medicalAttachments_submitted,
                        $patient->patient_record->allergies_submitted,
                        $patient->patient_record->family_history_submitted,
                        $patient->patient_record->medications_submitted,
                        $patient->patient_record->medicalfiles_submitted,
                    ])->every(fn($val) => $val === 1)
                    : false;

                return [
                    'name' => $patient->user->name,
                    'email' => $patient->user->email,
                    'phone' => $patient->user->phone,
                    'photo' => $patient->photo,
                    'record_completed' => $completedRecord,
                    'appointments_count' => $patient->appointments->whereIn('status', ['completed', 'canceled_by_patient','canceled_by_doctor','canceled_by_secretary',])->count(),
                ];
            });

        return view('secretary.patient.patient', compact('patients'));
    }




}

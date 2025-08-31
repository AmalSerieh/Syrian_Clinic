<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Doctor, Visit, DoctorMaterial, Secretary, Nurse, ClinicBill};
use Carbon\Carbon;


class FinanceController extends Controller
{
    public function finance(Request $request)
    {
        $clinicBills = ClinicBill::orderBy('billed_at', 'desc')->get();

        $periods = [
            'today' => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        ];

        $doctors = Doctor::all();
        $doctorCount = max($doctors->count(), 1);

        // مجموع فواتير العيادة حسب الفترة
        $billsByPeriod = [];
        foreach ($periods as $label => [$from, $to]) {
            $billsByPeriod[$label] = ClinicBill::between($from, $to)->sum('amount');
        }

        $results = [];
        foreach ($doctors as $doctor) {
            foreach ($periods as $label => [$from, $to]) {
                $income = Visit::where('doctor_id', $doctor->id)
                    ->whereBetween('v_ended_at', [$from, $to])
                    ->sum('v_price');

                $consumption = DoctorMaterial::where('doctor_id', $doctor->id)
                    ->whereBetween('dm_used_at', [$from, $to])
                    ->selectRaw('COALESCE(SUM(dm_quantity * dm_price), 0) as total')
                    ->value('total');

                $billsShare = $billsByPeriod[$label] / $doctorCount;
                $secretaryShare = Secretary::sum('s_wage') / $doctorCount;
                $nurseSalary = Nurse::where('doctor_id', $doctor->id)->sum('salary');

                $deduction = 0;
                if ($doctor->type_wage === 'percent') {
                    $deduction = $income * ($doctor->wage / 100.0);
                } elseif ($doctor->type_wage === 'fixed') {
                    $deduction = $doctor->wage;
                }

                $final = $income - $consumption - $billsShare - $secretaryShare - $nurseSalary - $deduction;

                $results[$doctor->id][$label] = [
                    'doctor' => $doctor->only(['id', 'user_id', 'name']),
                    'income' => round($income, 2),
                    'consumption' => round($consumption, 2),
                    'bills_share' => round($billsShare, 2),
                    'secretary_share' => round($secretaryShare, 2),
                    'nurse_salary' => round($nurseSalary, 2),
                    'deduction' => round($deduction, 2),
                    'net_balance' => round($final, 2),
                ];
            }
        }

        // حساب المجاميع وربح العيادة
        $totals = [];
        foreach ($periods as $label => $_) {
            $totalIncome = 0;
            $totalConsumption = 0;
            $totalBills = $billsByPeriod[$label];
            $totalSecretary = Secretary::sum('s_wage');

            $totalDeduction = 0;

            foreach ($doctors as $doctor) {
                $totalIncome += $results[$doctor->id][$label]['income'];
                $totalConsumption += $results[$doctor->id][$label]['consumption'];

                $totalDeduction += $results[$doctor->id][$label]['deduction'];
            }

            $totals[$label] = [
                'income' => round($totalIncome, 2),
                'consumption' => round($totalConsumption, 2),
                'profit' => round(
                    $totalIncome - $totalConsumption - $totalBills - $totalSecretary  - $totalDeduction,
                    2
                ),
            ];
        }

        // الفترة المختارة (من الريكوست أو الافتراضي "month")
        $selectedPeriod = $request->get('period', 'month');

        return view('secretary.home.financial', compact(
            'clinicBills',
            'results',
            'periods',
            'doctors',
            'selectedPeriod',
            'totals'
        ));
    }



    public function doctorReport(Doctor $doctor)
    {
        $periods = [
            'today' => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        ];

        $results = [];

        foreach ($periods as $label => [$from, $to]) {

            // ✅ 1. الدخل (زيارات الطبيب خلال الفترة)
            $income = Visit::where('doctor_id', $doctor->id)
                ->whereBetween('v_paid', [$from, $to])
                ->sum('v_price');

            // ✅ 2. الاستهلاك (مواد استعملها الطبيب)
            $consumption = DoctorMaterial::where('doctor_id', $doctor->id)
                ->whereBetween('dm_used_at', [$from, $to])
                ->selectRaw('COALESCE(SUM(dm_quantity * dm_price), 0) as total')
                ->value('total');

            // ✅ 3. الفواتير المشتركة (تقسيم على عدد الأطباء)
            $doctorCount = max(Doctor::count(), 1);
            $billsTotal = ClinicBill::between($from, $to)->sum('amount');
            $billsShare = $billsTotal / $doctorCount;

            // ✅ 4. الرواتب
            $secretaryShare = Secretary::sum('s_wage') / $doctorCount;   // سكرتيرات مقسومة
            $nurseSalary = Nurse::where('doctor_id', $doctor->id)->sum('salary'); // ممرضات فقط لطبيب محدد

            // ✅ 5. الحسم / النسبة
            $deduction = 0;
            if ($doctor->type_wage === 'percent') {
                $deduction = $income * ($doctor->wage / 100.0);
            } elseif ($doctor->type_wage === 'fixed') {
                $deduction = $doctor->wage;
            }

            // ✅ 6. الصافي
            $final = $income - $consumption - $billsShare - $secretaryShare - $nurseSalary - $deduction;

            // ✅ حفظ النتائج
            $results[$label] = [
                'period' => [$from->toDateString(), $to->toDateString()],
                'income' => round($income, 2),
                'consumption' => round($consumption, 2),
                'bills_share' => round($billsShare, 2),
                'secretary_share' => round($secretaryShare, 2),
                'nurse_salary' => round($nurseSalary, 2),
                'deduction' => round($deduction, 2),
                'net_balance' => round($final, 2),
            ];
        }

        return response()->json([
            'doctor' => $doctor->only(['id', 'user_id']),
            'report' => $results,
        ]);
    }

    public function allDoctorsReport()
    {
        $periods = [
            'today' => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        ];

        $doctors = Doctor::all();
        $doctorCount = max($doctors->count(), 1);

        // ✅ الفواتير خلال كل فترة (نحسبها مرة واحدة بس)
        $billsByPeriod = [];
        foreach ($periods as $label => [$from, $to]) {
            $billsByPeriod[$label] = ClinicBill::between($from, $to)->sum('amount');
        }

        $results = [];

        foreach ($doctors as $doctor) {
            foreach ($periods as $label => [$from, $to]) {

                // ✅ 1. الدخل (زيارات الطبيب)
                $income = Visit::where('doctor_id', $doctor->id)
                    ->whereBetween('v_paid', [$from, $to])
                    ->sum('v_price');

                // ✅ 2. الاستهلاك (مواد استعملها الطبيب)
                $consumption = DoctorMaterial::where('doctor_id', $doctor->id)
                    ->whereBetween('dm_used_at', [$from, $to])
                    ->selectRaw('COALESCE(SUM(dm_quantity * dm_price), 0) as total')
                    ->value('total');

                // ✅ 3. الفواتير المشتركة (تقسيم على عدد الأطباء)
                $billsShare = $billsByPeriod[$label] / $doctorCount;

                // ✅ 4. الرواتب
                $secretaryShare = Secretary::sum('s_wage') / $doctorCount;
                $nurseSalary = Nurse::where('doctor_id', $doctor->id)->sum('salary');

                // ✅ 5. الحسم / النسبة
                $deduction = 0;
                if ($doctor->type_wage === 'percent') {
                    $deduction = $income * ($doctor->wage / 100.0);
                } elseif ($doctor->type_wage === 'fixed') {
                    $deduction = $doctor->wage;
                }

                // ✅ 6. الصافي
                $final = $income - $consumption - $billsShare - $secretaryShare - $nurseSalary - $deduction;

                // ✅ نحفظ النتيجة للطبيب والفترة
                $results[$doctor->id][$label] = [
                    'doctor' => $doctor->only(['id', 'user_id']),
                    'period' => [$from->toDateString(), $to->toDateString()],
                    'income' => round($income, 2),
                    'consumption' => round($consumption, 2),
                    'bills_share' => round($billsShare, 2),
                    'secretary_share' => round($secretaryShare, 2),
                    'nurse_salary' => round($nurseSalary, 2),
                    'deduction' => round($deduction, 2),
                    'net_balance' => round($final, 2),
                ];
            }
        }

        return response()->json([
            'report' => $results,
        ]);
    }
    public function storeClinicBill(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'billed_at' => 'nullable|date',
        ]);

        ClinicBill::create($data);

        return redirect()->back()->with('status', 'Invoice added successfully!');
    }



}





<?php

namespace App\Http\Controllers\Web\Doctor\Appointment;

use App\Http\Controllers\Controller;
use App\Models\DoctorMaterial;
use App\Models\Material;
use App\Models\SupplierMaterial;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorMaterialController extends Controller
{
    public function create()
    {
        $doctor = auth()->user()->doctor;
        $visit = Visit::where('doctor_id', $doctor->id)->where('status', 'active')->latest()->first();

        if (!$visit) {
            return back()->with('error', 'لا توجد زيارة نشطة حالياً.');
        }

        $materials = Material::all();
        return view('doctor.materials.create', compact('materials', 'visit'));
    }

    public function index()
    {
        $doctor = auth()->user()->doctor;
        $visit = Visit::where('doctor_id', $doctor->id)->where('status', 'active')->latest()->first();

        if (!$visit) {
            return back()->with('error', 'لا توجد زيارة نشطة حالياً.');
        }

        $usedMaterials = DoctorMaterial::with(['material', 'supplier'])
            ->where('doctor_id', $doctor->id)
            ->where('visit_id', $visit->id)
            ->latest()
            ->get();

        return view('doctor.materials.index', compact('usedMaterials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required',
            'material_id' => 'required',
            'visit_id' => 'required',
            'dm_quantity' => 'required|integer|min:1',
            'dm_quality' => 'nullable|integer|min:1|max:5',
        ]);

        DB::beginTransaction();

        try {
            $supplies = SupplierMaterial::where('material_id', $request->material_id)
                ->where('sup_material_quantity', '>', 0)
                ->orderBy('sup_material_delivered_at')
                ->get();

            $totalAvailable = $supplies->sum('sup_material_quantity');

            if ($totalAvailable < $request->dm_quantity) {
                return back()->with(['message' => 'الكمية المطلوبة غير متوفرة حالياً في المخزون.'], 422);
            }

            $remaining = $request->dm_quantity;

            foreach ($supplies as $supply) {
                if ($remaining <= 0)
                    break;

                $usedQuantity = min($remaining, $supply->sup_material_quantity);

                DoctorMaterial::create([
                    'doctor_id' => $request->doctor_id,
                    'material_id' => $request->material_id,
                    'supplier_id' => $supply->supplier_id,
                    'visit_id' => $request->visit_id,
                    'dm_quantity' => $usedQuantity,
                    'dm_quality' => $request->dm_quality,
                    'dm_used_at' => now(),
                ]);

                $supply->decrement('sup_material_quantity', $usedQuantity);

                $remaining -= $usedQuantity;
            }

            $material = Material::findOrFail($request->material_id);
            $material->decrement('material_quantity', $request->dm_quantity);

            DB::commit();

            return back()->with(['message' => 'تم الحفظ بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'خطأ: ' . $e->getMessage()], 500);
        }
    }

}

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
            'doctor_id' => 'required|exists:doctors,id',
            'visit_id' => 'required|exists:visits,id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.quality' => 'nullable|integer|min:1|max:5',
        ]);

        DB::beginTransaction();

        try {
            $material = Material::findOrFail($request->material_id);

            // ✅ تحقق من صلاحية المادة العامة
            if ($material->material_expiration_date <= now()) {
                return response()->json(['message' => 'هذه المادة منتهية الصلاحية.'], 400);
            }

            // ✅ جلب التوريدات من الموردين
            $supplies = SupplierMaterial::where('material_id', $request->material_id)
                ->where('sup_material_quantity', '>', 0)
                ->where('sup_material_is_damaged', false)
                ->orderBy('sup_material_delivered_at') // FIFO
                ->get();

            $totalAvailable = $supplies->sum('sup_material_quantity');

            if ($totalAvailable < $request->quantity) {
                return response()->json(['message' => 'لا توجد كمية كافية من هذه المادة.'], 400);
            }

            $remaining = $request->quantity;
            $totalPrice = 0;

            foreach ($supplies as $supply) {
                if ($remaining <= 0)
                    break;

                $usedQuantity = min($remaining, $supply->sup_material_quantity);

                // السعر يعتمد على المورد (مش السعر العام للمادة)
                $unitPrice = $supply->sup_material_price;
                $lineTotal = $usedQuantity * $unitPrice;
                $totalPrice += $lineTotal;

                // تسجيل الاستهلاك عند الطبيب
                DoctorMaterial::create([
                    'doctor_id' => $request->doctor_id,
                    'material_id' => $request->material_id,
                    'supplier_id' => $supply->supplier_id,
                    'visit_id' => $request->visit_id,
                    'dm_quantity' => $usedQuantity,
                    'dm_quality' => $request->quality,
                    'dm_used_at' => now(),
                    'dm_price' => $unitPrice,
                    'dm_total_price' => $lineTotal,
                ]);

                // خصم من المورد
                $supply->sup_material_quantity -= $usedQuantity;
                $supply->save();

                $remaining -= $usedQuantity;
            }

            // خصم من المخزون العام للمادة
            $material->material_quantity -= $request->quantity;
            $material->save();

            DB::commit();

            return redirect()->back()->with('status', 'تم تسجيل الاستهلاك بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }


    private function consumeMaterial($doctorId, $visitId, $materialId, $quantity, $quality = null)
    {
        $material = Material::findOrFail($materialId);

        // تحقق من الصلاحية العامة
        if ($material->material_expiration_date <= now()) {
            throw new \Exception('المادة ' . $material->material_name . ' منتهية الصلاحية.');
        }

        // جلب التوريدات المتاحة (الصحيحة وغير التالفة)
        $supplies = SupplierMaterial::where('material_id', $materialId)
            ->where('sup_material_quantity', '>', 0)
            ->where('sup_material_is_damaged', false)
            ->orderBy('sup_material_delivered_at') // FIFO
            ->get();

        $totalAvailable = $supplies->sum('sup_material_quantity');

        if ($totalAvailable < $quantity) {
            throw new \Exception('لا توجد كمية كافية من المادة ' . $material->material_name);
        }

        $remaining = $quantity;

        foreach ($supplies as $supply) {
            if ($remaining <= 0)
                break;

            $usedQuantity = min($remaining, $supply->sup_material_quantity);

            // السعر من المورد
            $unitPrice = $supply->sup_material_price;
            $lineTotal = $usedQuantity * $unitPrice;

            // تسجيل استهلاك الطبيب
            DoctorMaterial::create([
                'doctor_id' => $doctorId,
                'material_id' => $materialId,
                'supplier_id' => $supply->supplier_id,
                'visit_id' => $visitId,
                'dm_quantity' => $usedQuantity,
                'dm_quality' => $quality, // 👈 الجودة تُسجّل هنا
                'dm_used_at' => now(),
                'dm_price' => $unitPrice,
                'dm_total_price' => $lineTotal,
            ]);

            // تحديث مخزون المورد
            $supply->sup_material_quantity -= $usedQuantity;
            $supply->save();

            $remaining -= $usedQuantity;
        }

        // تحديث المخزون العام
        $material->material_quantity -= $quantity;
        $material->save();
    }
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'visit_id' => 'required|exists:visits,id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.quality' => 'nullable|integer|min:1|max:5',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->materials as $item) {
                $this->consumeMaterial(
                    $request->doctor_id,
                    $request->visit_id,
                    $item['material_id'],
                    $item['quantity'],
                    $item['quality'] ?? null
                );
            }

            DB::commit();
            return redirect()->back()->with('status', 'تم تسجيل المواد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }


}

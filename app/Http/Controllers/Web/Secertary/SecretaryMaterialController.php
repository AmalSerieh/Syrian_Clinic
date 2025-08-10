<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\SupplierMaterial;
use Illuminate\Support\Facades\DB;

class SecretaryMaterialController extends Controller
{

    // ✅ عرض كل المواد
    public function index()
    {
        $materials = Material::orderBy('material_name')->get();
        return view('secretary.material.material-show', compact('materials'));
    }
    public function create()
    {
        return view('secretary.material.material-create');
    }


    // ✅ إضافة مادة جديدة مع تسجيل المورد والكمية
    public function store(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string',
            'material_quantity' => 'required|integer|min:1',
            'material_location' => 'nullable|string',
            'material_expiration_date' => 'nullable|date',
            'material_price' => 'required|numeric|min:0',
            'material_threshold' => 'nullable|integer',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::beginTransaction();

        try {
            // محاولة الحصول على المادة أو إنشائها
            $material = Material::where('material_name', $request->material_name)->first();

            if ($material) {
                // تحديث القيم للمادة الموجودة
                $material->material_quantity += $request->material_quantity;
                $material->material_price = $request->material_price;
                $material->material_location = $request->material_location;
                $material->material_expiration_date = $request->material_expiration_date;
                $material->material_threshold = $request->material_threshold;
            } else {
                // إنشاء مادة جديدة
                $material = Material::create([
                    'material_name' => $request->material_name,
                    'material_quantity' => $request->material_quantity,
                    'material_location' => $request->material_location,
                    'material_expiration_date' => $request->material_expiration_date,
                    'material_price' => $request->material_price,
                    'material_threshold' => $request->material_threshold,
                ]);
            }

            $material->save();

            // إضافة سجل المورد
            SupplierMaterial::create([
                'material_id' => $material->id,
                'supplier_id' => $request->supplier_id,
                'sup_material_quantity' => $request->material_quantity,
                'sup_material_price' => $request->material_price,
                'sup_material_delivered_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('secretary.material')->with('success', '✅ تم إضافة المادة والمورد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    public function edit($materialId)
    {
        $material = Material::findOrFail($materialId);
        return view('secretary.material.material-edit', compact('material'));
    }

    // ✅ تعديل مادة (مثلاً تحديث الكمية والسعر مع مورد جديد)
    public function update(Request $request, $materialId)
    {
        $material = Material::findOrFail($materialId);

        if ($material->material_expiration_date && \Carbon\Carbon::parse($material->material_expiration_date)->isPast()) {
            return redirect()->back()->with('error', 'لا يمكن تعديل مادة منتهية الصلاحية.');
        }

        $request->validate([
            'material_name' => 'required|string|max:255',
            'material_quantity' => 'required|integer|min:1',
            'material_location' => 'nullable|string|max:255',
            'material_expiration_date' => 'nullable|date|after:today',
            'material_price' => 'required|numeric|min:0',
            'material_threshold' => 'nullable|integer|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::beginTransaction();

        try {
            // حذف علاقة المورد السابق إن تم تغييره
            if ($material->supplier_id && $material->supplier_id != $request->supplier_id) {
                SupplierMaterial::where('material_id', $material->id)
                    ->where('supplier_id', $material->supplier_id)
                    ->delete();
            }
            // تحديث بيانات المادة
            $material->update([
                'material_name' => $request->material_name,
                'material_quantity' => $request->material_quantity,
                'material_price' => $request->material_price,
                'material_location' => $request->material_location,
                'material_expiration_date' => $request->material_expiration_date,
                'material_threshold' => $request->material_threshold,
                'supplier_id' => $request->supplier_id, // إذا كان موجودًا داخل الجدول
            ]);

            // إنشاء أو تحديث بيانات المورد المرتبط بالمادة
            SupplierMaterial::updateOrCreate(
                [
                    'material_id' => $material->id,
                    'supplier_id' => $request->supplier_id,
                ],
                [
                    'sup_material_quantity' => $request->material_quantity,
                    'sup_material_price' => $request->material_price,
                    'sup_material_delivered_at' => now(),
                ]
            );

            DB::commit();
            return redirect()->route('secretary.material')->with('success', '✅ تم تحديث المادة والمورد بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '❌ فشل التحديث: ' . $e->getMessage());
        }
    }


    // ✅ حذف مادة
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
    public function delete($materialId)
    {
        $material = Material::findOrFail($materialId);
        $material->delete();
        return redirect()->back()->with('success', 'تم حذف المادة بنجاح');
    }

    public function deleteAll()
    {
        Material::truncate();
        return redirect()->back()->with('success', 'تم حذف جميع المواد بنجاح');
    }

    // ✅ ترشيح أفضل الموردين حسب الجودة والسعر
    /* public function recommendedSuppliers($material_id)
    {
        $suppliers = SupplierMaterial::where('material_id', $material_id)
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($records, $supplier_id) use ($material_id) {
                $supplier = $records->first()->supplier;

                $avg_quality = DoctorMaterial::where('material_id', $material_id)
                    ->where('supplier_id', $supplier_id)
                    ->avg('quality') ?? 0;

                $latest_price = $records->sortByDesc('delivered_at')->first()->price;

                return [
                    'supplier_id' => $supplier_id,
                    'name' => $supplier->name,
                    'avg_quality' => round($avg_quality, 2),
                    'latest_price' => $latest_price,
                ];
            })
            ->sortByDesc('avg_quality')
            ->values();

        return response()->json($suppliers);
    } */

    public function recommendedSuppliers($material_id)
    {
        $suppliers = SupplierMaterial::where('material_id', $material_id)
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($records, $supplier_id) use ($material_id) {
                $supplier = $records->first()->supplier;

                $avg_quality = DoctorMaterial::where('material_id', $material_id)
                    ->where('supplier_id', $supplier_id)
                    ->where('quantity', '>', 0) // فقط المستخدمة فعليًا
                    ->avg('quality') ?? 0;

                $lowest_price = $records->min('sup_material_price') ?? 0;

                return [
                    'supplier_id' => $supplier_id,
                    'sup_name' => $supplier->sup_name,
                    'avg_quality' => round($avg_quality, 2),
                    'lowest_price' => $lowest_price,
                    'score' => $avg_quality > 0 && $lowest_price > 0
                        ? round($avg_quality / $lowest_price, 4)
                        : 0
                ];
            })->values();

        return response()->json([
            'sorted_by_quality' => $suppliers->sortByDesc('avg_quality')->values(),
            'sorted_by_price' => $suppliers->sortBy('lowest_price')->values(),
            'sorted_by_both' => $suppliers->sortByDesc('score')->values(),
        ]);
    }

}

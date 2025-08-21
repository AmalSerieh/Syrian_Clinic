<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\DoctorMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\SupplierMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SecretaryMaterialController extends Controller
{

    // ✅ عرض كل المواد
    public function index()
    {
        $materials = Material::with(['supplierMaterials.supplier'])->orderBy('material_name')->get();
        $suppliers = Supplier::all(); // إضافة هذا السطر
        return view('secretary.material.material-show', compact('materials', 'suppliers'));
    }
    public function create()
    {
        return view('secretary.material.material-create');
    }


    // ✅ إضافة مادة جديدة مع تسجيل المورد والكمية
    public function store(Request $request)
    {
        // تحقق من وجود secretary للمستخدم الحالي
        if (!auth()->user()->secretary) {
            return redirect()
                ->route('secretary.material')
                ->with('error', 'لا يوجد سكرتير مرتبط بحسابك');
        }
        $request->validate([
            'material_name' => 'required|string',
            'material_quantity' => 'required|integer|min:1',
            'material_location' => 'nullable|string',
            'material_expiration_date' => 'nullable|date|after:today',
            'material_price' => 'required|numeric|min:0',
            'material_threshold' => 'nullable|integer',
            'supplier_id' => 'required|exists:suppliers,id',
            'material_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        ]);


        try {
            $photoPath = null;
            // محاولة الحصول على المادة أو إنشائها
            $material = Material::where('material_name', $request->material_name)->first();

            if ($material) {
                // تحديث المادة
                $material->material_quantity += $request->material_quantity;
                $material->material_price = $request->material_price;
                $material->material_location = $request->material_location;
                $material->material_expiration_date = $request->material_expiration_date;
                $material->material_threshold = $request->material_threshold;

                if ($request->hasFile('material_image')) {
                    $material->material_image = $request->file('material_image')->store('materials', 'public');
                } elseif (!$material->material_image) {
                    // تعيين صورة افتراضية إذا لم يكن هناك صورة مسبقة
                    $material->material_image = 'materials/ma1.jpg';
                }

                $material->save();
            } else {
                // إنشاء مادة جديدة
                $material = Material::create([
                    'secretary_id' => auth()->user()->secretary->id,
                    'material_name' => $request->material_name,
                    'material_quantity' => $request->material_quantity,
                    'material_location' => $request->material_location,
                    'material_expiration_date' => $request->material_expiration_date,
                    'material_price' => $request->material_price,
                    'material_threshold' => $request->material_threshold,
                    'material_image' => $request->hasFile('material_image')
                        ? $request->file('material_image')->store('materials', 'public')
                        : 'materials/ma1.jpg',
                ]);
            }


            /*  // في حالة وجود صورة مرفوعة
             if ($request->hasFile('material_image')) {
                 $photoPath = $request->file('material_image')->store('materials', 'public');
             } else {
                 // مسار صورة افتراضية في حالة عدم رفع صورة
                 $photoPath = 'materials/ma1.jpg';
             }

             $material->save(); */

            // إضافة سجل المورد
            SupplierMaterial::create([

                'material_id' => $material->id,
                'supplier_id' => $request->supplier_id,
                'sup_material_quantity' => $request->material_quantity,
                'sup_material_price' => $request->material_price,
                'sup_material_delivered_at' => now(),
            ]);

            return redirect()->route('secretary.material')->with('status', '✅ تم إضافة المادة والمورد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    public function store2(Request $request)
    {
        // تحقق من وجود سكرتير للمستخدم الحالي
        if (!auth()->user()->secretary) {
            return redirect()
                ->route('secretary.material')
                ->with('error', 'لا يوجد سكرتير مرتبط بحسابك');
        }

        $request->validate([
            'material_name' => 'required|string',
            'material_quantity' => 'required|integer|min:1',
            'material_location' => 'nullable|string',
            'material_expiration_date' => 'nullable|date|after:today',
            'material_price' => 'required|numeric|min:0',
            'material_threshold' => 'nullable|integer',
            'supplier_id' => 'required|exists:suppliers,id',
            'material_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        ]);

        try {
            // الحصول على المادة أو إنشائها
            $material = Material::firstOrCreate(
                ['material_name' => $request->material_name],
                [
                    'secretary_id' => auth()->user()->secretary->id,
                    'material_price' => $request->material_price,
                    'material_location' => $request->material_location,
                    'material_expiration_date' => $request->material_expiration_date,
                    'material_threshold' => $request->material_threshold,
                    'material_quantity' => 0, // سيُزاد لاحقًا

                ]
            );

            // زيادة الكمية إذا المادة موجودة
            $material->material_quantity += $request->material_quantity;
            $material->material_price = $request->material_price;
            $material->material_location = $request->material_location;
            $material->material_expiration_date = $request->material_expiration_date;
            $material->material_threshold = $request->material_threshold;

            // حفظ الصورة Base64 إذا موجودة
            if ($request->material_image) {
                $imageData = $request->material_image;
                // إزالة البداية data:image/jpeg;base64,
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                        return redirect()->back()->with('error', 'نوع الصورة غير مدعوم');
                    }
                    $imageData = base64_decode($imageData);
                    $fileName = 'materials/' . uniqid() . '.' . $type;
                    \Storage::disk('public')->put($fileName, $imageData);
                    $material->material_image = $fileName;
                }
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

            return redirect()->route('secretary.material')
                ->with('status', '✅ تم إضافة المادة والمورد بنجاح');
        } catch (\Exception $e) {
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
            'material_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        ]);

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
            // 2. معالجة الصورة
            if ($request->hasFile('material_image')) {
                // 2.1 حذف الصورة القديمة
                if ($material->material_image) {
                    Storage::disk('public')->delete($material->material_image);
                }

                // 2.2 رفع الصورة الجديدة
                $data['material_image'] = $request->file('material_image')->store(
                    'materials',
                    'public'
                );
            } else {
                // 2.3 الاحتفاظ بالصورة الحالية
                $data['material_image'] = $material->material_image;
            }

            $material->update($data);

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

    public function recommendedSuppliers1($material_id)
    {
        $suppliers = SupplierMaterial::where('material_id', $material_id)
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($records, $supplier_id) use ($material_id) {
                $supplier = $records->first()->supplier;

                $avg_quality = DoctorMaterial::where('material_id', $material_id)
                    ->where('supplier_id', $supplier_id)
                    ->where('dm_quantity', '>', 0)
                    ->avg('dm_quality') ?? 0;

                $lowest_price = $records->min('sup_material_price') ?? 0;

                return [
                    'supplier_id' => $supplier_id,
                    'name' => $supplier->name,
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
    public function recommendedSuppliers3($material_id, $sort_by = 'score')
    {
        // جلب كل SupplierMaterial مع المورد المرتبط
        $supplierGroups = SupplierMaterial::where('material_id', $material_id)
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id');

        $suppliers = $supplierGroups->map(function ($records, $supplier_id) use ($material_id) {
            $supplier = $records->first()->supplier;

            // حساب متوسط الجودة من DoctorMaterial
            $avg_quality = DoctorMaterial::where('material_id', $material_id)
                ->where('supplier_id', $supplier_id)
                ->where('dm_quantity', '>', 0)
                ->avg('dm_quality') ?? 0;

            // حساب أقل سعر من جميع سجلات المورد
            $lowest_price = $records->pluck('sup_material_price')->map(fn($p) => (float) $p)->min() ?? 0;

            return [
                'supplier_id' => $supplier_id,
                'name' => $supplier->sup_name,
                'avg_quality' => round($avg_quality, 2),
                'lowest_price' => round($lowest_price, 2),
                'score' => ($avg_quality > 0 && $lowest_price > 0)
                    ? round($avg_quality / $lowest_price, 4)
                    : 0
            ];
        })->values();

        // فرز حسب الفلتر المطلوب
        switch ($sort_by) {
            case 'quality':
                $suppliers = $suppliers->sortByDesc('avg_quality')->values();
                break;
            case 'price':
                $suppliers = $suppliers->sortBy('lowest_price')->values();
                break;
            case 'score':
            default:
                $suppliers = $suppliers->sortByDesc('score')->values();
                break;
        }

        return response()->json([
            'material_id' => $material_id,
            'suppliers' => $suppliers->map(fn($s) => [
                'supplier_id' => $s['supplier_id'],
                'name' => $s['name'],
                'quantity' => $s['sup_material_quantity'] ?? 0,      // أو أي قيمة مناسبة
                'delivered_at' => $s['sup_material_delivered_at'] ?? null,
                'lowest_price' => $s['lowest_price'],
                'avg_quality' => $s['avg_quality'],
                'score' => $s['score'],
            ])
        ]);
    }

    public function recommendedSuppliers5($material_id)
    {
        // جلب كل SupplierMaterial مع المورد المرتبط
        $supplierGroups = SupplierMaterial::where('material_id', $material_id)
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id');

        $suppliers = $supplierGroups->map(function ($records, $supplier_id) use ($material_id) {
            $supplier = $records->first()->supplier;

            // حساب متوسط الجودة من DoctorMaterial
            $avg_quality = DoctorMaterial::where('material_id', $material_id)
                ->where('supplier_id', $supplier_id)
                ->where('dm_quantity', '>', 0)
                ->avg('dm_quality') ?? 0;

            // تحويل الأسعار إلى float قبل الحساب للحصول على الأقل
            $lowest_price = $records->pluck('sup_material_price')->map(fn($p) => (float) $p)->min() ?? 0;

            return [
                'supplier_id' => $supplier_id,
                'name' => $supplier->sup_name,
                'avg_quality' => round($avg_quality, 2),
                'lowest_price' => round($lowest_price, 2),
                'score' => ($avg_quality > 0 && $lowest_price > 0)
                    ? round($avg_quality / $lowest_price, 4)
                    : 0
            ];
        })->values();

        return response()->json([
            'material_id' => $material_id,
            'sorted_by_quality' => $suppliers->sortByDesc('avg_quality')->values(),
            'sorted_by_price' => $suppliers->sortBy('lowest_price')->values(),
            'sorted_by_score' => $suppliers->sortByDesc('score')->values(),
        ]);
    }
public function recommendedSuppliers($material_id)
{
    $supplierGroups = SupplierMaterial::where('material_id', $material_id)
        ->with('supplier')
        ->get()
        ->groupBy('supplier_id');

    $suppliers = $supplierGroups->map(function ($records, $supplier_id) use ($material_id) {
        $supplier = $records->first()->supplier;

        $avg_quality = DoctorMaterial::where('material_id', $material_id)
            ->where('supplier_id', $supplier_id)
            ->where('dm_quantity', '>', 0)
            ->avg('dm_quality') ?? 0;

        $lowest_price = $records->pluck('sup_material_price')->map(fn($p) => (float) $p)->min() ?? 0;

        // هنا أضف الكمية وأي بيانات أخرى تحتاجها
        $total_quantity = $records->sum('sup_material_quantity'); // مثال للكمية
        $delivered_at = $records->first()->sup_material_delivered_at ?? null;  // مثال لتاريخ التسليم إذا موجود

        return [
            'supplier_id' => $supplier_id,
            'name' => $supplier->sup_name,
            'avg_quality' => round($avg_quality, 2),
            'lowest_price' => round($lowest_price, 2),
            'score' => ($avg_quality > 0 && $lowest_price > 0) ? round($avg_quality / $lowest_price, 4) : 0,
            'quantity' => $total_quantity,
            'delivered_at' => $delivered_at,
        ];
    })->values();

    return response()->json([
        'material_id' => $material_id,
        'sorted_by_quality' => $suppliers->sortByDesc('avg_quality')->values(),
        'sorted_by_price' => $suppliers->sortBy('lowest_price')->values(),
        'sorted_by_score' => $suppliers->sortByDesc('score')->values(),
    ]);
}



}

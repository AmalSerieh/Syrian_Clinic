<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SecretarySupplierController extends Controller
{
    public function create()
    {
        return view('secretary.material.supplier-create');
    }


    public function store(Request $request)
    {
        // تحقق من وجود secretary للمستخدم الحالي
        if (!auth()->user()->secretary) {
            return redirect()
                ->route('secretary.supplier')
                ->with('error', 'لا يوجد سكرتير مرتبط بحسابك');
        }
        // تحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'sup_name' => 'required|string|max:255',
            'sup_phone' => 'required|string|max:10',
            'sup_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $photoPath = null;

        // في حالة وجود صورة مرفوعة
        if ($request->hasFile('sup_photo')) {
            $photoPath = $request->file('sup_photo')->store('supplier', 'public');
        } else {
            // مسار صورة افتراضية في حالة عدم رفع صورة
            $photoPath = 'supplier/supplierdefault.webp';
        }

        // إنشاء مورد جديد
        $supplier = Supplier::create([
            'secretary_id' => auth()->user()->secretary->id,
            'sup_name' => $validated['sup_name'],
            'sup_phone' => $validated['sup_phone'],
            'sup_photo' => $photoPath,
        ]);

        return redirect()
            ->route('secretary.supplier')
            ->with('status', 'تم إضافة المورد بنجاح');
        // إرجاع JSON لعرض ديلوج بنجاح الإضافة (يمكن التحكم به في الواجهة)

    }

    public function index()
    {
        $suppliers = Supplier::withCount('supplierMaterials')->get();
        return view('secretary.material.supplier-show', compact('suppliers'));
    }
    public function edit($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        return view('secretary.material.supplier-edit', compact('supplier'));

    }

    public function update(Request $request, $supplierId)
    {
        $request->validate([
            'sup_name' => 'required|string|max:255',
            'sup_phone' => 'required|string|max:10',
            'sup_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $supplier = Supplier::findOrFail($supplierId);

        $supplier->update([
            'sup_name' => $request->sup_name,
            'sup_phone' => $request->sup_phone,
        ]);
        // 2. معالجة الصورة
        if ($request->hasFile('sup_photo')) {
            // 2.1 حذف الصورة القديمة
            if ($supplier->sup_photo) {
                Storage::disk('public')->delete($supplier->sup_photo);
            }

            // 2.2 رفع الصورة الجديدة
            $data['sup_photo'] = $request->file('sup_photo')->store(
                'supplier',
                'public'
            );
        } else {
            // 2.3 الاحتفاظ بالصورة الحالية
            $data['sup_photo'] = $supplier->sup_photo;
        }

        $supplier->update($data);

        return redirect()
            ->route('secretary.supplier')
            ->with('status', 'تم تعديل معلومات المورد بنجاح.');
    }
    public function delete($supplierId)
    {
        $supplier = Supplier::with('supplierMaterials')->findOrFail($supplierId);

        // التحقق إذا كان المورد مرتبطًا بمواد قيد الاستخدام (مثلاً الكمية > 0)
        if ($supplier->supplierMaterials()->exists()) {
            // حذف المواد المرتبطة به فقط من الجدول الوسيط
            $supplier->supplierMaterials()->delete();
        }

        $supplier->delete();

        return redirect()
            ->route('secretary.supplier')
            ->with('status', 'تم حذف المورد وكل مواده المرتبطة به بنجاح.');
    }

    public function deleteAll()
    {
        // تحقق من وجود موردين مرتبطين بمواد
        $suppliersWithMaterials = Supplier::has('supplierMaterials')->count();

        if ($suppliersWithMaterials > 0) {
            return redirect()->route('secretary.supplier')
                ->with('error', '❌ لا يمكن حذف جميع الموردين لأن بعضهم مرتبط بمواد.');
        }

        // حذف العلاقات أولًا ثم حذف الموردين
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            $supplier->supplierMaterials()->delete();
            $supplier->delete(); // إذا كنت تستخدم soft deletes، أو استبدله بـ forceDelete()
        }

        return redirect()->route('secretary.supplier')
            ->with('success', '✅ تم حذف جميع الموردين بنجاح.');
    }




}

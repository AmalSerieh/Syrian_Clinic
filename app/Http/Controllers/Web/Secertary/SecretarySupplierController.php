<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecretarySupplierController extends Controller
{
    public function create()
    {
        return view('secretary.material.supplier-create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'sup_name' => 'required|string|max:255',
            'sup_phone' => 'nullable|string|max:10',
        ]);
        $supp = Supplier::create([
            'secretary_id' => Auth::user()->secretary->id,
            'sup_name' => $validated['sup_name'],
            'sup_phone' => $validated['sup_phone'],
        ]);


        return redirect()->route('secretary.supplier')->with('success', 'تم إضافة المورد بنجاح');
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
            'sup_phone' => 'nullable|string|max:20',
        ]);

        $supplier = Supplier::findOrFail($supplierId);

        $supplier->update([
            'sup_name' => $request->sup_name,
            'sup_phone' => $request->sup_phone,
        ]);

        return redirect()
            ->route('secretary.supplier')
            ->with('success', 'تم تعديل معلومات المورد بنجاح.');
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
            ->with('success', 'تم حذف المورد وكل مواده المرتبطة به بنجاح.');
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

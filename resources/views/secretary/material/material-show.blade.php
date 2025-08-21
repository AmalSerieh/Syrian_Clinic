@extends('layouts.secretary.header')
@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="flex min-h-screen bg-[#1e293b] p-4 gap-10 rounded-3xl -mt-4" x-data="materialDetails()">
        <!-- Left Side (Materials List) -->
        <div class="w-3/4 flex flex-col">
            <div class="grid grid-cols-3 gap-6 auto-rows-min">
                @if ($materials->count())
                    @foreach ($materials as $material)
                        @php
                            $isExpired =
                                $material->material_expiration_date &&
                                \Carbon\Carbon::parse($material->material_expiration_date)->isPast();
                            $isLowStock =
                                $material->material_threshold &&
                                $material->material_quantity <= $material->material_threshold;

                            // تحديد لون البطاقة بناءً على حالة المادة
                            if ($isExpired) {
                                $cardColor = 'bg-red-900/50';
                                $borderColor = 'border-red-700';
                            } elseif ($isLowStock) {
                                $cardColor = 'bg-yellow-900/50';
                                $borderColor = 'border-yellow-700';
                            } else {
                                $cardColor = 'bg-[#0e1625]';
                                $borderColor = 'border-gray-700';
                            }
                        @endphp

                        <div
                            class="{{ $cardColor }} p-4 rounded-3xl flex flex-col items-center h-[350px] border {{ $borderColor }}">
                            <div class="flex flex-col items-center justify-center flex-grow w-full">
                                <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                                    @php
                                        $colors = [
                                            ['bg' => 'bg-[#A9DFD8]', 'border' => 'border-[#A9DFD8]'],
                                            ['bg' => 'bg-red-500', 'border' => 'border-red-500'],
                                            ['bg' => 'bg-yellow-500', 'border' => 'border-yellow-500'],
                                            ['bg' => 'bg-green-500', 'border' => 'border-green-500'],
                                            ['bg' => 'bg-purple-500', 'border' => 'border-purple-500'],
                                            ['bg' => 'bg-[#F65606]', 'border' => 'border-[#F65606]'],
                                        ];
                                        $colorSet = $colors[array_rand($colors)];
                                    @endphp
                                    <div class="absolute w-[120px] h-[120px] rounded-full {{ $colorSet['bg'] }} z-[1]"></div>
                                    <div
                                        class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] {{ $colorSet['border'] }} border-b-transparent rotate-[-125deg] z-0">
                                    </div>

                                    <!-- أيقونة المادة -->

                                    <div
                                        class="w-[100px] h-[100px] flex items-center justify-center bg-white rounded-full z-[2] relative">
                                        @if ($material->material_image)
                                            <img src="{{ asset('storage/' . $material->material_image) }}" alt="material"
                                                class="w-[100px] h-[100px] object-cover object-center rounded-full border-1 border-black bg-white z-[2] relative">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-700"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @endif

                                    </div>
                                </div>
                                <div class="text-center text-white">
                                    <h3 class="font-semibold">{{ $material->material_name }}</h3>
                                    <p class="text-gray-400">الكمية: {{ $material->material_quantity }}</p>
                                    {{-- <p class="text-gray-400">السعر: {{ $material->material_price }} ل.س</p> --}}
                                    <p class="text-sm mt-2">
                                        @if ($isExpired)
                                            <span class="text-red-400">⛔ منتهية الصلاحية</span>
                                        @elseif($isLowStock)
                                            <span class="text-yellow-400">⚠ الكمية منخفضة</span>
                                        @else
                                            <span class="text-green-400">✅ آمنة</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 w-full justify-end mt-4">
                                <button
                                    @click="viewMaterial(
        '{{ $material->material_name }}',
        '{{ $material->material_quantity }}',
        '{{ $material->material_price }}',
        '{{ $material->material_location ?? 'غير محدد' }}',
        '{{ $material->material_expiration_date ? \Carbon\Carbon::parse($material->material_expiration_date)->format('Y-m-d') : 'غير محدد' }}',
        '{{ $isExpired ? 'منتهية الصلاحية' : ($isLowStock ? 'كمية منخفضة' : 'آمنة') }}',
        {{ $material->id }},
        '{{ asset('storage/' . $material->material_image) }}'
    )"
                                    class="bg-black bg-opacity-60 text-white py-3 rounded-full text-sm">
                                    عرض التفاصيل
                                </button>
                                </button>
                                <div class="flex gap-2 w-full items-center">
                                    <div x-data="{
                                        showEdit: false,
                                        editMaterialId: null,
                                        openEdit(id) {
                                            this.editMaterialId = id;
                                            this.showEdit = true;
                                        },
                                        closeEdit() {
                                            this.showEdit = false;
                                            this.editMaterialId = null;
                                        }
                                    }" class="flex gap-2 w-full items-center">

                                        <!-- زر تعديل -->
                                        <button @click="openEdit({{ $material->id }})"
                                            class="flex-1 bg-blue-500 text-white py-3 rounded-full text-sm hover:bg-blue-700 transition">
                                            تعديل
                                        </button>

                                        <!-- مودال التعديل -->
                                        <div x-show="showEdit" x-transition
                                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                            style="display: none;" @click.self="closeEdit()">
                                            <div @click.stop class="bg-[#34589b] p-6 rounded shadow-lg w-96 text-black">
                                                <h4 class="mb-4 font-bold text-lg">تعديل المادة #<span
                                                        x-text="editMaterialId"></span></h4>

                                                <!-- نموذج التعديل -->
                                                <form method="POST"
                                                    action="{{ route('secretary.material.update', $material->id) }}"
                                                    enctype="multipart/form-data">
                                                    {{-- <input type="hidden" name="_method" value="PUT"> --}}
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <div class="mb-4">
                                                        <label for="material_name" class="block font-bold">اسم
                                                            المادة</label>
                                                        <input type="text" name="material_name" id="material_name"
                                                            class="form-input w-full"
                                                            value="{{ old('material_name', $material->material_name) }}"
                                                            required>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="material_quantity"
                                                            class="block font-bold">الكمية</label>
                                                        <input type="number" name="material_quantity"
                                                            id="material_quantity" class="form-input w-full"
                                                            value="{{ old('material_quantity', $material->material_quantity) }}"
                                                            required>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="material_price" class="block font-bold">السعر</label>
                                                        <input type="number" step="0.01" name="material_price"
                                                            id="material_price" class="form-input w-full"
                                                            value="{{ old('material_price', $material->material_price) }}"
                                                            required>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="material_location"
                                                            class="block font-bold">الموقع</label>
                                                        <input type="text" name="material_location"
                                                            id="material_location" class="form-input w-full"
                                                            value="{{ old('material_location', $material->material_location) }}">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="material_expiration_date" class="block font-bold">تاريخ
                                                            الانتهاء</label>
                                                        <input type="date" name="material_expiration_date"
                                                            id="material_expiration_date" class="form-input w-full"
                                                            value="{{ old('material_expiration_date', $material->material_expiration_date) }}">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="material_threshold" class="block font-bold">حد
                                                            التنبيه</label>
                                                        <input type="number" name="material_threshold"
                                                            id="material_threshold" class="form-input w-full"
                                                            value="{{ old('material_threshold', $material->material_threshold) }}">
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="material_image" class="block font-bold">Image</label>
                                                        <input type="file" name="material_image" id="material_image"
                                                            class="form-input w-full">
                                                        @if ($material->material_image)
                                                            <img src="{{ asset('storage/' . $material->material_image) }}"
                                                                alt="Current Photo" class="w-24 h-24 mt-2 rounded">
                                                        @endif
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="supplier_id" class="block font-bold ">المورد</label>
                                                        <select name="supplier_id" class="text-black " required>
                                                            <option disabled selected class=" text-white bg-black">اختر
                                                                المورد</option>
                                                            @foreach ($suppliers as $supplier)
                                                                <option value="{{ $supplier->id }}"
                                                                    class=" text-white bg-black"
                                                                    @if (old('supplier_id', $material->supplier_id ?? '') == $supplier->id) selected @endif>
                                                                    {{ $supplier->sup_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="text-right">
                                                        <button type="submit"
                                                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                            حفظ التغييرات
                                                        </button>
                                                        <button type="button" @click="closeEdit()"
                                                            class="ml-2 bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                                                            إلغاء
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- زر حذف -->
                                        <form action="{{ route('secretary.material.delete', $material->id) }}"
                                            method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-900 bg-opacity-60 text-white py-3 rounded-full text-sm hover:bg-red-700 transition"
                                                onclick="return confirm('هل أنت متأكد من حذف هذه المادة؟')">
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- إضافة مادة جديدة -->
                    <div x-data="{ showCreate: false }" class="">
                        <button @click="showCreate = true" class="w-full h-full">
                            <div
                                class="p-4 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition h-[350px] border-2 border-dashed border-blue-500 bg-transparent">
                                <div class="text-blue-500 text-6xl mb-2 font-bold select-none">+</div>
                                <div
                                    class="w-full block text-center text-xl text-blue-500 py-2 px-4 rounded-md font-semibold">
                                    إضافة مادة جديدة
                                </div>
                            </div>
                        </button>

                        <!-- مودال الإنشاء -->
                        <div x-show="showCreate" x-transition
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 text-black"
                            style="display: none;" @click.self="showCreate = false">
                            <div @click.stop class="bg-[#34589b] p-6 rounded shadow-lg w-96 text-black">
                                <h2 class="mb-4 font-bold text-lg">➕ إضافة مادة جديدة</h2>

                                <!-- نموذج الإنشاء -->
                                <form method="POST" action="{{ route('secretary.material.store') }}"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <div class="mb-4">
                                        <label for="material_name" class="block font-bold">اسم المادة</label>
                                        <input type="text" name="material_name" id="material_name"
                                            class="form-input w-full text-cyan-950" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="supplier_id" class="block font-bold ">المورد</label>
                                        <select name="supplier_id" class="text-black " required>
                                            <option disabled selected class=" text-white bg-black">اختر المورد</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" class=" text-white bg-black">
                                                    {{ $supplier->sup_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="mb-4">
                                        <label for="material_quantity" class="block font-bold">الكمية</label>
                                        <input type="number" name="material_quantity" id="material_quantity"
                                            class="form-input w-full" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="material_price" class="block font-bold">السعر</label>
                                        <input type="number" step="0.01" name="material_price" id="material_price"
                                            class="form-input w-full" required>
                                    </div>

                                    <div class="mb-4">
                                        <label for="material_location" class="block font-bold">الموقع</label>
                                        <input type="text" name="material_location" id="material_location"
                                            class="form-input w-full">
                                    </div>

                                    <div class="mb-4">
                                        <label for="material_expiration_date" class="block font-bold">تاريخ
                                            الانتهاء</label>
                                        <input type="date" name="material_expiration_date"
                                            id="material_expiration_date" class="form-input w-full">
                                    </div>

                                    <div class="mb-4">
                                        <label for="material_threshold" class="block font-bold">حد التنبيه</label>
                                        <input type="number" name="material_threshold" id="material_threshold"
                                            class="form-input w-full" placeholder="الحد الأدنى للتنبيه">
                                    </div>
                                    <div class="mb-4">
                                        <label for="material_image" class="block font-bold">الصورة </label>
                                        <input type="file" name="material_image" id="material_image"
                                            class="form-input w-full">
                                    </div>

                                    <div class="text-right">
                                        <button type="submit"
                                            class="bg-[#303c52] text-white px-4 py-2 rounded hover:bg-blue-700">
                                            إضافة
                                        </button>
                                        <button type="button" @click="showCreate = false"
                                            class="ml-2 bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                                            إلغاء
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- حالة عدم وجود مواد -->
                    <div class="col-span-3 flex items-center justify-center">
                        <div x-data="{ showCreate: false }">
                            <button @click="showCreate = true">
                                <div
                                    class="p-10 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition border-2 border-dashed border-blue-500 bg-transparent">
                                    <div class="text-blue-500 text-8xl mb-4 font-bold select-none">+</div>
                                    <div class="text-2xl text-blue-500 font-semibold">
                                        إضافة أول مادة
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side (Info Panel) -->
        @if ($materials->count())
            <div class="w-1/4 flex-shrink-0 flex flex-col min-h-[900px]">
                <div
                    class="flex-grow bg-[#0e1625] p-6 rounded-3xl flex flex-col items-center justify-start text-center relative">

                    <template x-if="selectedMaterial">

                        <div class="mt-8 flex flex-col items-center w-full space-y-4">
                            <!-- أيقونة المادة -->
                            <div>
                                <img :src="selectedMaterial.image" alt="Doctor"
                                    class="w-[150px] h-[150px] object-cover object-center rounded-full border-4  bg-[#031D2E] z-[2] relative">
                            </div>

                            <h3 class="text-white font-semibold text-xl" x-text="selectedMaterial.name"></h3>
                            <p class="text-gray-400 text-sm" x-text="'الكمية: ' + selectedMaterial.quantity"></p>
                            <p class="text-gray-400 text-sm" x-text="'السعر: ' + selectedMaterial.price + ' ل.س'"></p>
                            <p class="text-gray-400 text-sm" x-text="'الموقع: ' + selectedMaterial.location"></p>
                            <p class="text-gray-400 text-sm" x-text="'تاريخ الانتهاء: ' + selectedMaterial.expiration">
                            </p>
                            <div class="flex gap-2 mb-2 justify-center">
                                <button @click="loadSuppliers('quality')"
                                    class="px-2 py-1 bg-blue-600 text-white rounded">حسب
                                    الجودة</button>
                                <button @click="loadSuppliers('price')"
                                    class="px-2 py-1 bg-blue-600 text-white rounded">حسب
                                    السعر</button>
                                <button @click="loadSuppliers('score')"
                                    class="px-2 py-1 bg-blue-600 text-white rounded">حسب
                                    النقاط</button>
                            </div>

                            <!-- قسم الموردين -->
                            <div class="w-full mt-6">
                                <h4 class="text-white font-semibold mb-3 border-b border-gray-600 pb-2">الموردين</h4>
                                <div class="space-y-3 max-h-60 overflow-y-auto">
                                    <template x-for="supplier in selectedMaterial.suppliers" :key="supplier.id">
                                        <div class="bg-blue-900/30 border border-blue-700 p-3 rounded-lg text-right">
                                            <p class="text-blue-200 font-semibold" x-text="'المورد: ' + supplier.name">
                                            </p>
                                            <p class="text-blue-300 text-sm" x-text="'الكمية: ' + supplier.quantity"></p>
                                            <p class="text-blue-300 text-sm" x-text="'السعر: ' + supplier.price + ' ل.س'">
                                            </p>
                                            <p class="text-blue-300 text-sm" x-text="'التسليم: ' + supplier.delivered_at">
                                            </p>
                                            <p class="text-blue-300 text-sm" x-text="'الجودة: ' + supplier.quality"></p>
                                        </div>
                                    </template>

                                    <template
                                        x-if="!selectedMaterial.suppliers || selectedMaterial.suppliers.length === 0">
                                        <p class="text-gray-500 text-sm">لا يوجد موردين لهذه المادة</p>
                                    </template>
                                </div>
                            </div>

                            <div class="w-full flex flex-col gap-4 pt-10">
                                <!-- بطاقة حالة المادة -->
                                <div
                                    class="bg-blue-900/30 border border-blue-700 p-4 rounded-lg shadow flex items-center justify-between gap-3 w-full">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-700 p-2 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="font-semibold text-blue-300 text-sm">حالة المادة</h2>
                                            <p class="text-xl font-bold text-blue-200" x-text="selectedMaterial.status">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!selectedMaterial">
                        <p class="text-gray-500 mt-20">اختر مادة لعرض التفاصيل</p>
                    </template>
                </div>
            </div>
        @endif
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function materialDetails() {
            return {
                selectedMaterial: null,
                async viewMaterial(name, quantity, price, location, expiration, status, materialId, image) {
                    try {
                        const response = await fetch(`/materials/${materialId}/suppliers`);
                        const suppliersData = await response.json();

                        this.selectedMaterial = {
                            id: materialId,
                            name,
                            quantity,
                            price,
                            location,
                            expiration,
                            status,
                            suppliers: suppliersData,
                            image
                        };
                    } catch (error) {
                        console.error('Error fetching suppliers:', error);
                        this.selectedMaterial = {
                            name,
                            quantity,
                            price,
                            location,
                            expiration,
                            status,
                            suppliers: [],
                            error: 'تعذر تحميل بيانات الموردين'
                        };
                    }
                },
                async loadSuppliers(sortBy = 'score') {
                    if (!this.selectedMaterial) return;

                    const materialId = this.selectedMaterial.id;
                    const response = await fetch(
                        `/secretary/material/${materialId}/recommended-suppliers`
                    );
                    const suppliersData = await response.json();

                    // تحديد المفتاح المناسب حسب sortBy
                    let key;
                    switch (sortBy) {
                        case 'quality':
                            key = 'sorted_by_quality';
                            break;
                        case 'price':
                            key = 'sorted_by_price';
                            break;
                        case 'score':
                        default:
                            key = 'sorted_by_score';
                            break;
                    }

                    this.selectedMaterial = {
                        ...this.selectedMaterial,
                        suppliers: suppliersData[key].map(s => ({
                            id: s.supplier_id,
                            name: s.name,
                            quantity: s.quantity || '-', // قيمة افتراضية
                            price: s.lowest_price,
                            delivered_at: s.delivered_at || '-', // قيمة افتراضية
                            quality: s.avg_quality,
                            score: s.score
                        }))
                    };
                }

            }
        }
    </script>
@endsection

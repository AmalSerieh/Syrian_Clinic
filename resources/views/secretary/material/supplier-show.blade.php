@extends('layouts.secretary.header')
@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="flex min-h-screen bg-[#1e293b] p-4 gap-10 rounded-3xl -mt-4" x-data="supplierDetails()">
        <!-- Left Side (Suppliers List) -->
        <div class="w-3/4 flex flex-col">
            <div class="grid grid-cols-3 gap-6 min-h-[350px]">
                @if ($suppliers->count())
                    @foreach ($suppliers as $supplier)
                        <div class="bg-[#0e1625] p-4 rounded-3xl flex flex-col items-center h-[350px]">
                            <div class="flex flex-col items-center justify-center flex-grow w-full">
                                <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                                    @php
                                        $colors = [
                                            ['bg' => 'bg-blue-500', 'border' => 'border-blue-500'],
                                            ['bg' => 'bg-red-500', 'border' => 'border-red-500'],
                                            ['bg' => 'bg-yellow-500', 'border' => 'border-yellow-500'],
                                            ['bg' => 'bg-green-500', 'border' => 'border-green-500'],
                                            ['bg' => 'bg-purple-500', 'border' => 'border-purple-500'],
                                            ['bg' => 'bg-pink-500', 'border' => 'border-pink-500'],
                                        ];
                                        $colorSet = $colors[array_rand($colors)];
                                    @endphp
                                    <div class="absolute w-[120px] h-[120px] rounded-full {{ $colorSet['bg'] }} z-[1]"></div>
                                    <div
                                        class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] {{ $colorSet['border'] }} border-b-transparent rotate-[-125deg] z-0">
                                    </div>

                                    <!-- أيقونة المورد بدلاً من الصورة -->
                                    <div
                                        class="w-[100px] h-[100px] flex items-center justify-center bg-white rounded-full z-[2] relative">
                                        @if ($supplier->sup_photo)
                                            <img src="{{ asset('storage/' . $supplier->sup_photo) }}" alt="supplier"
                                                class="w-[100px] h-[100px] object-cover object-center rounded-full border-1 border-black bg-white z-[2] relative">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-700"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-center text-white">
                                    <h3 class="font-semibold">{{ $supplier->sup_name }}</h3>
                                    <p class="text-gray-400">{{ $supplier->sup_phone ?? 'No phone' }}</p>
                                    <p class="text-sm text-gray-300 mt-2">
                                        {{ $supplier->supplier_materials_count }} Available materials
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 w-full justify-end mt-4">
                                <button
                                    @click="viewSupplier(
                                        '{{ $supplier->sup_name }}',
                                        '{{ $supplier->sup_phone }}',
                                        '{{ $supplier->supplier_materials_count }} materials',
                                        '{{ $supplier->created_at->format('Y-m-d') }}',
                                        '{{ asset('storage/' . $supplier->sup_photo) }}'
                                    )"
                                    class="bg-black bg-opacity-60 text-white py-3 rounded-full text-sm">
                                    Show Details
                                </button>
                                <div class="flex gap-2 w-full items-center">

                                    <div x-data="{
                                        showEdit: false,
                                        editSupplierId: null,

                                        openEdit(id) {
                                            this.editSupplierId = id;
                                            this.showEdit = true;
                                        },

                                        closeEdit() {
                                            this.showEdit = false;
                                            this.editSupplierId = null;
                                        }
                                    }" class="flex gap-2 w-full items-center">

                                        <!-- زر تعديل -->
                                        <button @click="openEdit({{ $supplier->id }})"
                                            class="flex-1 bg-blue-500 text-white py-3 rounded-full text-sm hover:bg-blue-700 transition">
                                            Edit
                                        </button>

                                        <!-- مودال التعديل -->
                                        <div x-show="showEdit" x-transition
                                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                            style="display: none;" @click.self="closeEdit()">
                                            <div @click.stop class="bg-[#34589b] p-6 rounded shadow-lg w-96 text-black">
                                                <h4 class="mb-4 font-bold text-lg">Edit Supplier #<span
                                                        x-text="editSupplierId"></span></h4>

                                                <!-- نموذج التعديل -->
                                                <form method="POST"
                                                    action="{{ route('secretary.supplier.update', $supplier->id) }}"
                                                    enctype="multipart/form-data">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                                    <div class="mb-4">
                                                        <label for="sup_name" class="block font-bold"> Name</label>
                                                        <input type="text" name="sup_name" id="sup_name"
                                                            class="form-input w-full"
                                                            value="{{ old('sup_name', $supplier->sup_name) }}" required>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="sup_phone" class="block font-bold"> Phone</label>
                                                        <input type="text" name="sup_phone" id="sup_phone"
                                                            class="form-input w-full"
                                                            value="{{ old('sup_phone', $supplier->sup_phone) }}">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="sup_photo" class="block font-bold">Image</label>
                                                        <input type="file" name="sup_photo" id="sup_photo"
                                                            class="form-input w-full">
                                                        @if ($supplier->sup_photo)
                                                            <img src="{{ asset('storage/' . $supplier->sup_photo) }}"
                                                                alt="Current Photo" class="w-24 h-24 mt-2 rounded">
                                                        @endif
                                                    </div>

                                                    <div class="text-right">
                                                        <button type="submit"
                                                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                            Save changes
                                                        </button>
                                                        <button type="button" @click="closeEdit()"
                                                            class="ml-2 bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- زر حذف -->
                                        <form action="{{ route('secretary.supplier.delete', $supplier->id) }}"
                                            method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-900 bg-opacity-60 text-white py-3 rounded-full text-sm hover:bg-red-700 transition"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>


                                </div>

                            </div>
                        </div>
                    @endforeach
                    <!-- إضافة مورد جديد -->
                    <div x-data="{
                        showCreate: false,
                        createSupplierId: null,

                        openCreate() {
                            this.showCreate = true;
                        },

                        closeCreate() {
                            this.showCreate = false;
                            this.createSupplierId = null;
                        }
                    }" class="">

                        <div class="">
                            <!-- إضافة مورد جديد -->
                            <button @click="openCreate()">
                                <div
                                    class="p-4 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition h-[350px] border-2 border-dashed border-blue-500 bg-transparent">
                                    <div class="text-blue-500 text-6xl mb-2 font-bold select-none">+</div>
                                    <div
                                        class="w-full block text-center text-xl text-blue-500 py-2 px-4 rounded-md font-semibold">
                                        Add new Supplier
                                    </div>
                                </div>
                            </button>

                            <div x-show="showCreate" x-transition
                                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                style="display: none;" @click.self="closeCreate()">
                                <div @click.stop class="bg-[#34589b] p-6 rounded shadow-lg w-96 text-black">
                                    <h2 class="mb-4 font-bold text-lg">➕ Add Supplier
                                    </h2>

                                    <!-- نموذج الإنشاء -->
                                    <form method="POST" action="{{ route('secretary.supplier.store') }}"
                                        enctype="multipart/form-data">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                        <div class="mb-4">
                                            <label for="sup_name" class="block font-bold"> Name Supplier</label>
                                            <input type="text" name="sup_name" id="sup_name"
                                                class="form-input w-full text-cyan-950"required>
                                        </div>

                                        <div class="mb-4">
                                            <label for="sup_phone" class="block font-bold"> Phone Supplier</label>
                                            <input type="text" name="sup_phone" id="sup_phone"
                                                class="form-input w-full">
                                        </div>

                                        <div class="mb-4">
                                            <label for="sup_photo" class="block font-bold">Photo Supplier</label>
                                            <input type="file" name="sup_photo" id="sup_photo"
                                                class="form-input w-full">
                                        </div>

                                        <div class="text-right">
                                            <button type="submit"
                                                class="bg-[#303c52] text-white px-4 py-2 rounded hover:bg-blue-700">
                                                Add
                                            </button>
                                            <button type="button" @click="closeCreate()"
                                                class="ml-2 bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- حالة عدم وجود موردين -->
                    <div class="col-span-3 flex items-center justify-center">
                        <a href="{{ route('secretary.supplier.create') }}">
                            <div
                                class="p-10 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition border-2 border-dashed border-blue-500 bg-transparent">
                                <div class="text-blue-500 text-8xl mb-4 font-bold select-none">+</div>
                                <div class="text-2xl text-blue-500 font-semibold">
                                    Add first Supplier
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side (Info Panel) -->
        @if ($suppliers->count())
            <div class="w-1/4 flex-shrink-0 flex flex-col min-h-[900px]">
                <div
                    class="flex-grow bg-[#0e1625] p-6 rounded-3xl flex flex-col items-center justify-start text-center relative">
                    <template x-if="selectedSupplier">
                        <div class="mt-8 flex flex-col items-center w-full space-y-4">
                            <!-- أيقونة المورد -->
                            <div>
                                <img :src="selectedSupplier.photo" alt="Doctor"
                                    class="w-[150px] h-[150px] object-cover object-center rounded-full border-4  bg-[#031D2E] z-[2] relative">
                            </div>

                            <h3 class="text-white font-semibold text-xl" x-text="selectedSupplier.name"></h3>
                            <p class="text-gray-400 text-sm" x-text="'phone: ' + selectedSupplier.phone"></p>
                            <p class="text-gray-400 text-sm" x-text="'Registration date: ' + selectedSupplier.joinDate">
                            </p>

                            <div class="w-full flex flex-col gap-4 pt-10">
                                <!-- بطاقة المواد المتوفرة -->
                                <div
                                    class="bg-blue-900/30 border border-blue-700 p-4 rounded-lg shadow flex items-center justify-between gap-3 w-full">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-700 p-2 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="font-semibold text-blue-300 text-sm">Available materials</h2>
                                            <p class="text-xl font-bold text-blue-200"
                                                x-text="selectedSupplier.materials">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!selectedSupplier">
                        <p class="text-gray-500 mt-20">Select a supplier to view details.</p>
                    </template>
                </div>
            </div>
        @endif
    </div>



    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function supplierDetails() {
            return {
                selectedSupplier: null,
                viewSupplier(name, phone, materials, joinDate, photo) {
                    this.selectedSupplier = {
                        name,
                        photo,
                        phone: phone || 'غير متوفر',
                        materials,
                        joinDate
                    };
                }
            }
        }
    </script>
@endsection

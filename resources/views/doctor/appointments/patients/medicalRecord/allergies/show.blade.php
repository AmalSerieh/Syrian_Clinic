@section('content')
    <div class="container mx-auto px-4"x-data="{ edit: false, allergy: {}, create: false }">
        <h2 class="text-2xl font-bold m b-6">
            allergies - {{ $patient->user->name }}
        </h2>

        <button @click="create = true; " class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
            <span>➕</span>إضافة حساسية
        </button>
        @if ($allergies->isEmpty())
            <div class="p-4 bg-yellow-100 text-yellow-700 rounded-lg">
                No allergies found.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($allergies as $allergy)
                    <div class="bg-red-400 rounded-2xl shadow p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $allergy->aller_name }}</h3>

                        <p class="text-sm text-gray-600 mb-1">
                            القوة:: <span class="font-medium">{{ $allergy->aller_power }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            النوع:: <span class="font-medium">{{ $allergy->aller_type }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            السبب:: <span class="font-medium">{{ $allergy->aller_cause }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            العلاج:: <span class="font-medium">{{ $allergy->aller_treatment }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            الوقاية:: <span class="font-medium">{{ $allergy->aller_pervention }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            ملاحظات:: <span class="font-medium">{{ $allergy->aller_reasons }}</span>
                        </p>

                        <div class="flex justify-between items-center mt-3">

                            <button @click="edit = true;"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                ✏️ تعديل
                            </button>

                            <form action="{{ route('doctor.medical-record.allergies.delete', $allergy->id) }}"
                                method="POST" onsubmit="return confirm('Are you sure you want to delete this allergy?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>

        @endif
        {{-- المودال --}}
        <div x-show="edit" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white text-black rounded-2xl p-6 w-full max-w-lg relative">
                <button @click="edit = false" class="absolute top-3 right-3 text-gray-700 text-xl">&times;</button>
                <h3 class="text-xl font-bold mb-4">✏️ تعديل الحساسية</h3>

                <form action="{{ route('doctor.medical-record.allergies.update', $allergy->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label>اسم الحساسية</label>
                        <input type="text" name="aller_name" value="{{$allergy->aller_name}}"
                            class="w-full border rounded p-2" required>
                    </div>
                    <div>
                        <label>القوة</label>
                        <select name="aller_power" value="{{$allergy->aller_power}}" class="w-full border rounded p-2" required>
                            <option value="strong">قوية</option>
                            <option value="medium">متوسطة</option>
                            <option value="weak">ضعيفة</option>
                        </select>
                    </div>
                    <div>
                        <label>النوع</label>
                        <select name="aller_type" value="{{$allergy->aller_type}}" class="w-full border rounded p-2" required>
                            <option value="animal">حيوانات</option>
                            <option value="pollen">غبار الطلع</option>
                            <option value="Food">طعام</option>
                            <option value="dust">غبار</option>
                            <option value="mold">عفن</option>
                            <option value="medicine">دواء</option>
                            <option value="seasons">مواسم</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label>السبب</label>
                        <input type="text" name="aller_cause" value="{{$allergy->aller_cause}}"
                            class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label>العلاج</label>
                        <input type="text" name="aller_treatment" value="{{$allergy->aller_treatment}}"
                            class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label>الوقاية</label>
                        <input type="text" name="aller_pervention" value="{{$allergy->aller_pervention}}"
                            class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label>ملاحظات</label>
                        <textarea name="aller_reasons" value="{{$allergy->aller_reasons}}" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            💾 حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- المودال --}}
        <div x-show="create" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="  bg-slate-200 text-black rounded-2xl p-6 w-full max-w-lg relative">
                <button @click="create = false" class="absolute top-3 right-3 text-gray-700 text-xl">&times;</button>
                <h3 class="text-xl font-bold mb-4">✏️ add allergy</h3>

                <form action="{{ route('doctor.medical-record.allergies.store', $patient->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label>📄 اسم الحساية</label>
                        <input type="text" name="aller_name" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label>القوة</label>
                        <select name="aller_power" value="" class="w-full border rounded p-2" required>
                            <option value="strong">قوية</option>
                            <option value="medium">متوسطة</option>
                            <option value="weak">ضعيفة</option>
                        </select>
                    </div>
                    <div>
                        <label>النوع</label>
                        <select name="aller_type" value="" class="w-full border rounded p-2" required>
                            <option value="animal">حيوانات</option>
                            <option value="pollen">غبار الطلع</option>
                            <option value="Food">طعام</option>
                            <option value="dust">غبار</option>
                            <option value="mold">عفن</option>
                            <option value="medicine">دواء</option>
                            <option value="seasons">مواسم</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label>السبب</label>
                        <input type="text" name="aller_cause" value=""
                            class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label>العلاج</label>
                        <input type="text" name="aller_treatment" value=""
                            class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label>الوقاية</label>
                        <input type="text" name="aller_pervention" value=""
                            class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label>ملاحظات</label>
                        <textarea name="aller_reasons" value="allergy.aller_reasons" class="w-full border rounded p-2"></textarea>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            💾 حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

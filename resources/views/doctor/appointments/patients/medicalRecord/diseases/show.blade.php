<!-- resources/views/patient/diseases/index.blade.php -->

<!-- العنوان -->
<div class="mb-8 text-center">
    <h1 class="text-2xl font-bold mb-2">📋 الملف الطبي للمريض: {{ $patient->user->name }}</h1>
    <p class="text-gray-400">إدارة المعلومات الطبية الأساسية للمريض</p>
</div>

<!-- رسائل التنبيه -->
@if (session('error'))
    <div class="bg-red-900/30 border border-red-700 rounded-2xl p-4 mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
            <div>
                <p class="text-red-300 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

@if (session('status'))
    <div class="bg-green-900/30 border border-green-700 rounded-2xl p-4 mb-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400 text-xl"></i>
            <p class="text-green-300">{{ session('success') }}</p>
        </div>
    </div>
@endif

<!-- x-data يضم الزر والـ Modal -->
<div x-data="{ open: false, edit: false, disease: {} }" class="bg-[#0f2538] rounded-2xl p-6 shadow-lg">
    <div>
        <!-- زر إنشاء المرض -->
        <button @click="open = true"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center gap-2 mx-auto">
            <i class="fas fa-plus-circle"></i>
            إنشاء مرض جديد
        </button>
    </div>


    @if ($diseases->isEmpty())
        <!-- حالة عدم وجود مرض -->
        <div class="text-center py-5">
            <h3 class="text-xl font-bold text-yellow-300 mb-2">❌ لم يتم إدخال أي مرض بعد</h3>
            <p class="text-gray-400 mb-6">لا يوجد أمراض للمريض {{ $patient->user->name }} في النظام</p>

            <!-- زر إنشاء المرض -->
            <button @click="open = true"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center gap-2 mx-auto">
                <i class="fas fa-plus-circle"></i>
                إنشاء مرض جديد
            </button>
        </div>
    @else
        <!-- عرض الأمراض الحالية والمزمنة -->
        <h3 class="text-lg font-bold mb-4">📌 الأمراض الحالية</h3>
        @foreach ($current ?? [] as $disease)
            <div class="disease-box p-4 mb-4 bg-[#1a2d42] rounded-xl text-white">
                <strong>النوع:</strong> {{ $disease->d_type }}<br>
                <strong>الاسم:</strong> {{ $disease->d_name }}<br>
                <strong>تاريخ التشخيص:</strong> {{ $disease->d_diagnosis_date }}<br>
                <strong>الطبيب المشخّص:</strong> {{ $disease->d_doctor }}<br>
                <strong>نصائح:</strong> {{ $disease->d_advice }}<br>
                <strong>محظورات:</strong> {{ $disease->d_prohibitions }}<br>

                <div class="mt-2 flex gap-2">

                    <!-- زر إنشاء المرض -->
                    <button @click="edit = true"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center gap-2 mx-auto">
                        <i class="fas fa-plus-circle"></i>
                        edit
                    </button>

                    <form action="{{ route('doctor.medical-record.diseases.delete', $disease->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded"
                            onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
                    </form>
                </div>
            </div>
        @endforeach

        <h3 class="text-lg font-bold mt-6 mb-4">📌 الأمراض المزمنة</h3>
        @foreach ($chronic ?? [] as $disease)
            <div class="disease-box p-4 mb-4 bg-[#1a2d42] rounded-xl text-white">
                <strong>النوع:</strong> {{ $disease->d_type }}<br>
                <strong>الاسم:</strong> {{ $disease->d_name }}<br>
                <strong>تاريخ التشخيص:</strong> {{ $disease->d_diagnosis_date }}<br>
                <strong>الطبيب المشخّص:</strong> {{ $disease->d_doctor }}<br>
                <strong>نصائح:</strong> {{ $disease->d_advice }}<br>
                <strong>محظورات:</strong> {{ $disease->d_prohibitions }}<br>

                <div class="mt-2 flex gap-2">
                    <button @click="edit = true"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center gap-2 mx-auto">
                        <i class="fas fa-plus-circle"></i>
                        edit
                    </button>
                    <form action="{{ route('doctor.medical-record.diseases.delete', $disease->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded"
                            onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
                    </form>
                </div>
            </div>
            <!-- Modal لتعديل مرض -->
            <div x-show="edit" x-transition
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-[#0f2538] rounded-2xl p-6 w-full max-w-lg relative text-white">
                    <button @click="edit = false" class="absolute top-3 right-3 text-white text-xl">&times;</button>
                    <h3 class="text-xl font-bold mb-4">✏️ تعديل مرض</h3>

                    <form action="{{ route('doctor.medical-record.diseases.update', $disease->id) }}" method="POST"
                        class="space-y-4 text-white">
                        @csrf


                        <div>
                            <label>النوع</label>
                            <select name="d_type" class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                                <option value="current" @selected($disease->d_type === 'current')>حالية</option>
                                <option value="chronic" @selected($disease->d_type === 'chronic')>مزمنة</option>
                            </select>
                        </div>

                        <div>
                            <label>اسم المرض</label>
                            <input type="text" name="d_name" required
                                class="w-full rounded border-gray-300 mt-1 p-2 text-black"
                                value="{{ $disease->d_name }}">
                        </div>

                        <div>
                            <label>تاريخ التشخيص</label>
                            <input type="date" name="d_diagnosis_date" required
                                class="w-full rounded border-gray-300 mt-1 p-2 text-black"
                                value="{{ $disease->d_diagnosis_date }}">
                        </div>

                        <div>
                            <label>الطبيب المشخّص</label>
                            <input type="hidden" name="d_doctor"
                                value="{{ Auth::user()->doctor?->name ?? Auth::user()->name }}">
                            <p class="text-sm text-gray-300">{{ Auth::user()->doctor?->name ?? Auth::user()->name }}
                            </p>
                        </div>

                        <div>
                            <label>نصائح</label>
                            <textarea name="d_advice" class="w-full rounded border-gray-300 mt-1 p-2 text-black">{{ $disease->d_advice }}</textarea>
                        </div>

                        <div>
                            <label>محظورات</label>
                            <textarea name="d_prohibitions" class="w-full rounded border-gray-300 mt-1 p-2 text-black">{{ $disease->d_prohibitions }}</textarea>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl">💾
                                حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Modal لإنشاء مرض جديد -->
    <div x-show="open" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-[#0f2538] rounded-2xl p-6 w-full max-w-lg relative text-white">
            <button @click="open = false" class="absolute top-3 right-3 text-white text-xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">إنشاء مرض جديد</h3>

            <form action="{{ route('doctor.medical-record.diseases.store', $patient->id) }}" method="POST"
                class="space-y-4 text-white">
                @csrf
                <div>
                    <label>النوع</label>
                    <select name="d_type" class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                        <option value="current">حالية</option>
                        <option value="chronic">مزمنة</option>
                    </select>
                </div>
                <div>
                    <label>اسم المرض</label>
                    <input type="text" name="d_name" required
                        class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                </div>
                <div>
                    <label>تاريخ التشخيص</label>
                    <input type="date" name="d_diagnosis_date" required
                        class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                </div>
                {{--  <div>
                    <label>الطبيب المشخّص</label> --}}<input type="hidden" name="d_doctor"
                    value="{{ Auth::user()->doctor?->name ?? Auth::user()->name }}">

                {{--
                <input type="hidden" name="d_doctor" class="w-full rounded border-gray-300 mt-1 p-2 text-black"
                    value="{{ Auth::user()->doctor->name }}"> --}}
                {{-- </div> --}}
                <div>
                    <label>نصائح</label>
                    <textarea name="d_advice" class="w-full rounded border-gray-300 mt-1 p-2 text-black"></textarea>
                </div>
                <div>
                    <label>محظورات</label>
                    <textarea name="d_prohibitions" class="w-full rounded border-gray-300 mt-1 p-2 text-black"></textarea>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl">💾 حفظ</button>
                </div>
            </form>
        </div>
    </div>



</div>

<!-- Alpine.js -->
<script src="//unpkg.com/alpinejs" defer></script>

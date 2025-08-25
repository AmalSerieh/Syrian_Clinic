<!-- resources/views/doctor/appointments/patients/medicalRecord/operations/show.blade.php -->


<div class="max-w-5xl mx-auto p-6" x-data="{ open: false, edit: false }">

    <!-- العنوان -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold mb-2">🛠️ العمليات الجراحية للمريض: {{ $patient->user->name }}</h1>
        <p class="text-gray-400">إدارة وعرض السجل الجراحي للمريض</p>
    </div>

    <!-- رسائل التنبيه -->
    @if (session('error'))
        <div class="bg-red-900/30 border border-red-700 rounded-2xl p-4 mb-6">
            <p class="text-red-300 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-900/30 border border-green-700 rounded-2xl p-4 mb-6">
            <p class="text-green-300">{{ session('success') }}</p>
        </div>
    @endif

    <!-- زر إنشاء عملية جديدة -->
  {{--   <div class="mb-6 text-center">
        <a href="{{ route('doctor.medical-record.operations.create', $patient->id) }}"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition inline-flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> إضافة عملية جديدة
        </a>
    </div> --}}
    <!-- زر إنشاء المرض -->
    <button @click="open = true"
        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center gap-2 mx-auto">
        <i class="fas fa-plus-circle"></i>
        إضافة عملية جديدة
    </button>

    <!-- قائمة العمليات -->
    @if ($operations->isEmpty())
        <div class="text-center py-6">
            <h3 class="text-xl font-bold text-yellow-300 mb-2">❌ لا توجد عمليات مسجّلة</h3>
            <p class="text-gray-400">لم يتم إضافة أي عملية جراحية للمريض {{ $patient->user->name }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($operations as $operation)
                <div class="p-5 bg-[#1a2d42] rounded-xl text-white shadow">
                    <strong>اسم العملية:</strong> {{ $operation->op_name }} <br>
                    <strong>تاريخ العملية:</strong> {{ $operation->op_date }} <br>
                    <strong>الطبيب المنفذ:</strong> {{ $operation->op_doctor_name }} <br>
                    <strong>المشفى:</strong> {{ $operation->op_hospital_name }} <br>

                    <div class="mt-3 flex gap-2">
                        <!-- زر تعديل -->

                        <button @click="edit = true"
                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            <i class="fas fa-plus-circle"></i>
                            edit
                        </button>
                        <!-- زر حذف -->
                        <form action="{{ route('doctor.medical-record.operations.delete', $operation->id) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
                        </form>
                    </div>
                </div>
                <div x-show="edit" x-transition
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-[#0f2538] rounded-2xl p-6 w-full max-w-lg relative text-white">
                        <button @click="edit = false" class="absolute top-3 right-3 text-white text-xl">&times;</button>
                        <h3 class="text-xl font-bold mb-4">✏️ تعديل مرض</h3>

                        <form action="{{ route('doctor.medical-record.operations.update', $operation->id) }}"
                            method="POST" class="space-y-4 text-white">
                            @csrf


                            <div>
                                <label>اسم العملية</label>
                                <input type="text" name="op_name" required
                                    class="w-full rounded border-gray-300 mt-1 p-2 text-black"
                                    value="{{ $operation->op_name }}">
                            </div>

                            <div>

                                {{-- <label>اسم الطبيب</label> --}}
                                <input type="hidden" name="op_doctor_name" required
                                    value="{{ $operation->op_doctor_name ?? Auth::user()->doctor->name }}"
                                    class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                            </div>
                            <div>
                                <label> اسم المشفى</label>
                                <input type="text" name="op_hospital_name" required
                                    class="w-full rounded border-gray-300 mt-1 p-2 text-black"
                                    value="{{ $operation->op_hospital_name }}">
                            </div>
                            <div>
                                <label> تاريخ العملية </label>
                                <input type="text" name="op_date" required
                                    class="w-full rounded border-gray-300 mt-1 p-2 text-black"
                                    value="{{ $operation->op_date }}">
                            </div>


                            <div class="text-center mt-4">
                                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl">💾
                                    حفظ</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <!-- Modal لإنشاء مرض جديد -->
    <div x-show="open" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-[#0f2538] rounded-2xl p-6 w-full max-w-lg relative text-white">
            <button @click="open = false" class="absolute top-3 right-3 text-white text-xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">إنشاء عملية جديد</h3>

            <form action="{{ route('doctor.medical-record.operations.store', $patient->id) }}" method="POST"
                class="space-y-4 text-white">
                @csrf
                <div>
                    <label> اسم العملية</label>
                    <input type="text" name="op_name" required
                        class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                </div>
                <input type="hidden" name="op_doctor_name"
                    value="{{ Auth::user()->doctor?->name ?? Auth::user()->name }}">

                <div>
                    <label>اسم المشفى</label>
                    <input type="text" name="op_hospital_name" required
                        class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                </div>
                <div>
                    <label>تاريخ العملية </label>
                    <input type="date" name="op_date" required
                        class="w-full rounded border-gray-300 mt-1 p-2 text-black">
                </div>


                <div class="text-center mt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-xl">💾 حفظ</button>
                </div>
            </form>
        </div>
    </div>

</div>

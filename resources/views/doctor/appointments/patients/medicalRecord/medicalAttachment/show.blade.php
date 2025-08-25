@section('content')
    <div class="container mx-auto px-4"x-data="{ edit: false, file: {}, create: false }">
        <h2 class="text-2xl font-bold m b-6">
            Medical Attachments - {{ $patient->user->name }}
        </h2>


        <button @click="create = true; " class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
            <span>➕</span> إضافة تحليل
        </button>
        @if ($files->isEmpty())
            <div class="p-4 bg-yellow-100 text-yellow-700 rounded-lg">
                No medical attachments found.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($files as $file)
                    <div class="bg-red-400 rounded-2xl shadow p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $file->ray_name }}</h3>

                        <p class="text-sm text-gray-600 mb-1">
                            Laboratory: <span class="font-medium">{{ $file->ray_laboratory }}</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-3">
                            Date: <span
                                class="font-medium">{{ \Carbon\Carbon::parse($file->ray_date)->format('d M Y') }}</span>
                        </p>


                        <img src="{{ asset('storage/' . $file->ray_image) }}" target="_blank"
                            class="w-16 h-16 rounded-full object-cover border-2 border-slate-600" alt="صورة المريض">
                        {{-- أزرار التحكم --}}
                        <div class="flex justify-between items-center mt-3">

                            <button @click="edit = true; file = @js($file)"
                                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                ✏️ تعديل
                            </button>

                            <form action="{{ route('doctor.medical-record.medicalAttachments.delete', $file->id) }}"
                                method="POST" onsubmit="return confirm('Are you sure you want to delete this file?');">
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
                <h3 class="text-xl font-bold mb-4">✏️ تعديل المرفق</h3>

                <form :action="'/doctor/medical-record/' + file.id + '/medicalAttachments/update'" method="POST"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label>📄 اسم الفحص</label>
                        <input type="text" name="ray_name" x-model="file.ray_name" class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label>🏥 المختبر</label>
                        <input type="text" name="ray_laboratory" x-model="file.ray_laboratory"
                            class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label>📅 التاريخ</label>
                        <input type="date" name="ray_date" x-model="file.ray_date" class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label>📎 الملف (اختياري)</label>
                        <input type="file" name="ray_image" class="w-full border rounded p-2" x-model="file.ray_date">
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
            <div class="bg-white text-black rounded-2xl p-6 w-full max-w-lg relative">
                <button @click="create = false" class="absolute top-3 right-3 text-gray-700 text-xl">&times;</button>
                <h3 class="text-xl font-bold mb-4">✏️ add المرفق</h3>

                <form action="{{ route('doctor.medical-record.medicalAttachments.store', $patient->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label>📄 اسم الفحص</label>
                        <input type="text" name="ray_name" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label>🏥 المختبر</label>
                        <input type="text" name="ray_laboratory" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label>📅 التاريخ</label>
                        <input type="date" name="ray_date" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label>📎 الملف (اختياري)</label>
                        <input type="file" name="ray_image" class="w-full border rounded p-2">
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            💾 حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

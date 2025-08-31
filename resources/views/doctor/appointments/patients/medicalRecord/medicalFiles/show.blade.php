<div class="container mx-auto px-4" x-data="{ create: false, edit: false, file: {} }">
    <h2 class="text-2xl font-bold mb-6">
        🧾 الملفات الطبية - {{ $patient->user->name }}
    </h2>

    <!-- زر الإضافة -->
    <button @click="create = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        ➕ إضافة ملف جديد
    </button>

    <!-- عرض الملفات -->
    @if ($files->isEmpty())
        <div class="mt-6 p-4 bg-yellow-100 text-yellow-700 rounded-lg">
            لا يوجد ملفات طبية بعد.
        </div>
    @else
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full bg-blue-500 border border-gray-200 rounded-lg shadow">
                <thead>
                    <tr class="bg-gray-500 text-left">
                        <th class="p-3 border-b">📄 اسم الفحص</th>
                        <th class="p-3 border-b">🏥 المختبر</th>
                        <th class="p-3 border-b">📅 التاريخ</th>
                        <th class="p-3 border-b">📎 الملف</th>
                        <th class="p-3 border-b">⚙️ الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                        <tr class="border-b">
                            <td class="p-3">{{ $file->test_name }}</td>
                            <td class="p-3">{{ $file->test_laboratory }}</td>
                            <td class="p-3">{{ $file->test_date }}</td>
                            <td class="p-3">
                                @if ($file->test_image_pdf)
                                    <a href="{{ asset('storage/' . $file->test_image_pdf) }}" target="_blank"
                                        class="text-blue-600 underline">
                                        📂 عرض
                                    </a>
                                @else
                                    <span class="text-gray-500">لا يوجد</span>
                                @endif
                            </td>
                            <td class="p-3 flex gap-2">
                                <!-- زر الحذف -->
                                <form action="{{ route('doctor.medical-record.medicalFiles.delete', $file->id) }}"
                                    method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الملف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        🗑️ حذف
                                    </button>
                                </form>

                                <!-- زر التعديل -->
                                <button @click="edit = true; file = @js($file)"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    ✏️ تعديل
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- مودال الإضافة -->
    <div x-show="create" x-transition
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white text-black rounded-2xl p-6 w-full max-w-lg relative">
            <button @click="create = false" class="absolute top-3 right-3 text-gray-700 text-xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">➕ إضافة ملف طبي</h3>

            <form action="{{ route('doctor.medical-record.medicalFiles.store', $patient->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block">📄 اسم الفحص</label>
                    <input type="text" name="test_name" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block">🏥 المختبر</label>
                    <input type="text" name="test_laboratory" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block">📅 التاريخ</label>
                    <input type="date" name="test_date" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label class="block">📎 ملف الفحص (صورة أو PDF)</label>
                    <input type="file" name="test_image_pdf" accept="image/*,.pdf" class="w-full border rounded p-2">
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        💾 حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- مودال التعديل -->
    <div x-show="edit" x-transition
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white text-black rounded-2xl p-6 w-full max-w-lg relative">
            <button @click="edit = false" class="absolute top-3 right-3 text-gray-700 text-xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">✏️ تعديل الملف الطبي</h3>

            <form :action="'/doctor/medical-record/' + file.id + '/medicalFiles/update'" method="POST"
                enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block">📄 اسم الفحص</label>
                    <input type="text" name="test_name" class="w-full border rounded p-2" x-model="file.test_name"
                        required>
                </div>

                <div>
                    <label class="block">🏥 المختبر</label>
                    <input type="text" name="test_laboratory" class="w-full border rounded p-2"
                        x-model="file.test_laboratory" required>
                </div>

                <div>
                    <label class="block">📅 التاريخ</label>
                    <input type="date" name="test_date" class="w-full border rounded p-2" x-model="file.test_date"
                        required>
                </div>

                <div>
                    <label class="block">📎 ملف الفحص (صورة أو PDF)</label>
                    <input type="file" name="test_image_pdf" accept="image/*,.pdf" class="w-full border rounded p-2">
                    <small class="text-gray-500">رفع ملف جديد لاستبدال القديم (اختياري)</small>
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

<script src="//unpkg.com/alpinejs" defer></script>

<!-- Sidebar -->
<div class="w-60 min-h-screen bg-[#062E47] text-white flex flex-col p-4">
    <div class="flex flex-col items-center mb-6 relative">
        <!-- صورة الطبيب -->
        <div class="relative">
            <img src="{{ asset('storage/' . Auth::user()->doctor->photo) }}" alt="Avatar"
                class="w-20 h-20 rounded-full mb-3 border-2 border-gray-400">


            <!-- أيقونة القلم للتعديل -->
            <button onclick="openEditModal()"
                class="absolute bottom-2 right-2 bg-blue-600 p-1 rounded-full shadow-md hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                </svg>
            </button>
        </div>
          <div class="text-center">
            <div class="text-sm text-gray-300">DOCTOR CLINIC</div>
            <div class="font-semibold">{{ Auth::user()->name }}</div>
        </div>
    </div>

    <!-- خط رايق بعد الصورة -->
    <div class="border-t border-[#1f2a40] mb-4"></div>

    <!-- عنوان Main -->
    <div class="text-xs text-gray-400 mb-4 pl-1">Main</div>

    <!-- Navigation -->
  <!-- Navigation -->
    <nav class="flex flex-col gap-4 ">
        <a href="{{ route('doctor.dashboard') }}"
            class="flex items-center gap-3 p-3 rounded  hover:bg-blue-500/15 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layout-grid-icon lucide-layout-grid">
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="14" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
            </svg>
            Home
        </a>

        <a href="{{ route('doctor-profile.show') }}" class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user-round-pen-icon lucide-user-round-pen">
                <path d="M2 21a8 8 0 0 1 10.821-7.487" />
                <path
                    d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                <circle cx="10" cy="8" r="5" />
            </svg>
            Doctor Profile
        </a>

        <a href="{{ route('doctor-schedule.index') }}"
            class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50">

            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-activity-icon lucide-activity">
                <path
                    d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2" />
            </svg>
            Schedule
        </a>

        <a href="#" class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chart-spline-icon lucide-chart-spline">
                <path d="M3 3v16a2 2 0 0 0 2 2h16" />
                <path d="M7 16c.5-2 1.5-7 4-7 2 0 2 3 4 3 2.5 0 4.5-5 5-7" />
            </svg>
            Appointments
        </a>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50 w-full text-left">
                <!-- أيقونة تسجيل الخروج -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-log-out">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" x2="9" y1="12" y2="12" />
                </svg>
                logout
            </button>
        </form>

    </nav>
</div>
<!-- المودال (تعديل البيانات) -->
<!-- المودال (تعديل البيانات) -->
<div id="editModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6 text-black">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">تعديل الحساب</h2>
            <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('doctor.profile.update') }}" enctype="multipart/form-data"
            id="profileForm">
            @csrf
             @method('PUT')
            <!-- معاينة الصورة -->
            <div class="mb-4 text-center">
                <div id="imagePreview"
                    class="w-24 h-24 mx-auto mb-2 rounded-full overflow-hidden border-2 border-gray-300">
                    @if (Auth::user()->doctor && Auth::user()->doctor->photo)
                        <img src="{{ asset('storage/' . Auth::user()->doctor->photo) }}" alt="الصورة الحالية"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">لا توجد صورة</span>
                        </div>
                    @endif
                </div>

                <label for="photo"
                    class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    تغيير الصورة
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden"
                        onchange="previewImage(this)">
                </label>
                @error('photo')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- تعديل الاسم -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">الاسم</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- تعديل الإيميل -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">الإيميل</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- تعديل كلمة المرور -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الجديدة</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="اتركه فارغاً إذا لم ترد التغيير">
                @error('password')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- تأكيد كلمة المرور -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="تأكيد كلمة المرور الجديدة">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    إلغاء
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // دالة لمعاينة الصورة قبل الرفع
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML =
                    `<img src="${e.target.result}" class="w-full h-full object-cover" alt="معاينة الصورة">`;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // دالة لفتح المودال
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // دالة لإغلاق المودال
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        // إعادة تعيين الفورم
        document.getElementById('profileForm').reset();
    }

    // إغلاق المودال عند الضغط على ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });

    // إغلاق المودال عند النقر خارج المحتوى
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target.id === 'editModal') {
            closeEditModal();
        }
    });
</script>

<style>
    /* إضافة تأثيرات انتقالية للمودال */
    #editModal {
        transition: opacity 0.3s ease;
    }

    #editModal>div {
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    #editModal:not(.hidden)>div {
        transform: scale(1);
    }
</style>

<script>
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>

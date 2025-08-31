@extends('layouts.doctor.header')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-800 py-8 px-4 rounded-2xl" x-data="{ showEditModal: false }">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white bg-[#060E0E] rounded-2xl shadow-lg px-8 py-4 inline-block">
                    📅 جداول مواعيد الطبيب
                </h1>
                @if (session('success'))
                    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-xl shadow-md">
                        ✅ {{ session('success') }}
                    </div>
                @endif
            </div>

            <!-- Schedule Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($schedules as $schedule)
                    <div
                        class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-blue-800 to-indigo-900 p-6 text-white text-center">
                            <h3 class="text-xl font-bold mb-2">{{ __($schedule->day) }}</h3>
                            <div class="flex justify-center items-center gap-2">
                                <span class="bg-blue-400 px-3 py-1 rounded-full text-sm text-black">
                                    🕒 {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                </span>
                                <span class="text-white">-</span>
                                <span class="bg-blue-400 px-3 py-1 rounded-full text-sm text-black">
                                    🕒 {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center bg-gray-800 rounded-xl p-3">
                                    <div class="text-2xl font-bold text-blue-600">{{ $schedule->patients_per_hour }}</div>
                                    <div class="text-sm text-blue-600">مريض/ساعة</div>
                                </div>
                                <div class="text-center bg-gray-800 rounded-xl p-3">
                                    <div class="text-2xl font-bold text-green-600">{{ $schedule->appointment_duration }}
                                    </div>
                                    <div class="text-sm text-green-600">دقيقة/موعد</div>
                                </div>
                                <div class="text-center bg-gray-800 rounded-xl p-3 col-span-2">
                                    <div class="text-2xl font-bold text-purple-600">{{ $schedule->max_patients }}</div>
                                    <div class="text-sm text-purple-600">الحد الأقصى</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3">
                                <button @click="showEditModal = true"
                                    class="flex-1 bg-blue-700 text-white py-2 px-4 rounded-xl text-center hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    تعديل
                                </button>
                                <form action="{{ route('doctor-schedule.destroy', $schedule->id) }}" method="POST"
                                    class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الجدول؟')"
                                        class="w-full bg-red-700 text-white py-2 px-4 rounded-xl hover:bg-red-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Add New Schedule Card -->
                <div
                    class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-dashed border-blue-600">
                    <div class="p-6 h-full flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">إضافة جدول جديد</h3>
                        <p class="text-white mb-4">أضف جدول مواعيد جديد لأيام الأسبوع</p>
                        <a href="{{ route('doctor-schedule.create') }}"
                            class="bg-gradient-to-r from-blue-700 to-blue-800 text-white py-2 px-6 rounded-xl hover:from-blue-800 hover:to-blue-900 transition-all duration-200">
                            + إضافة جدول
                        </a>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <!-- Edit Modal -->
            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                style="display: none;">
                <div @click.outside="showEditModal = false"
                    class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white rounded-t-2xl">
                        <div class="flex justify-between items-center">
                            <h2 class="text-2xl font-bold">تعديل الملف المهني</h2>
                            <button @click="showEditModal = false" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        <form action="{{ route('doctor-schedule.update', $schedule->id) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6 text-gray-700">
                            @csrf
                            @method('PUT')

                            <!-- اسم الطبيب (مقروء فقط) -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">اليوم</label>
                                <select name="day" required
                                    class="w-full bg-gray-100 border-gray-300 rounded-xl shadow-sm py-2 px-4 mt-1 text-gray-600">
                                    @foreach ([
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $schedule->day == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- وقت البدء -->
                            <div>
                                <label class="block font-semibold text-gray-700 mb-2">وقت البدء:</label>
                                <input type="time" name="start_time"
                                    value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required
                                    step="900"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

                                @error('date_birth')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>


                            <!-- وقت النهاية -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وقت النهاية:</label>
                                <input type="time" name="end_time"
                                    value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required
                                    step="900"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- عدد المرضى في الساعة -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">عدد المرضى في الساعة:</label>
                                <input type="number" name="patients_per_hour"
                                    value="{{ $schedule->appointment_duration ? round(60 / $schedule->appointment_duration) : 4 }}"
                                    min="1" max="60" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">سيتم حساب مدة الموعد تلقائياً (60 / عدد المرضى)</p>
                            </div>

                            <!-- معاينة الحسابات -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-medium text-gray-700 mb-2">معاينة الحسابات:</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <p class="text-sm">مدة الموعد:</p>
                                    <p class="text-sm font-semibold">
                                        @if ($schedule->appointment_duration)
                                            {{ round($schedule->appointment_duration) }} دقيقة
                                        @else
                                            سيتم الحساب بعد الحفظ
                                        @endif
                                    </p>

                                    <p class="text-sm">الحد الأقصى للمرضى:</p>
                                    <p class="text-sm font-semibold">
                                        @php
                                            $start = \Carbon\Carbon::parse($schedule->start_time);
                                            $end = \Carbon\Carbon::parse($schedule->end_time);
                                            $hours = $end->diffInMinutes($start) / 60;
                                            $max_patients = round(
                                                $hours *
                                                    ($schedule->appointment_duration
                                                        ? 60 / $schedule->appointment_duration
                                                        : 4),
                                            );
                                        @endphp
                                        {{ $max_patients }} مريض
                                    </p>
                                </div>
                            </div>


                            <!-- أزرار -->
                            <div class="flex gap-4 pt-4">
                                <button type="button" @click="showEditModal = false"
                                    class="flex-1 py-3 bg-gray-500 text-white text-lg font-semibold rounded-xl shadow hover:bg-gray-600 transition">
                                    إلغاء
                                </button>
                                <button type="submit"
                                    class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-lg font-semibold rounded-xl shadow hover:from-blue-700 hover:to-indigo-800 transition">
                                    💾 حفظ التعديلات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            @if ($schedules->isEmpty())
                <div class="text-center py-12">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md mx-auto">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">لا توجد مواعيد حاليًا</h3>
                        <p class="text-gray-600 mb-4">ابدأ بإضافة جدول المواعيد الأول</p>
                        <a href="{{ route('doctor-schedule.create') }}"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 px-6 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-200">
                            + إضافة أول جدول
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
@endsection

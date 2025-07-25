<x-app-layout>
    <div class="max-w-6xl mx-auto mt-10 bg-white shadow rounded p-6">
        <h2 class="text-2xl font-bold mb-4 text-center text-blue-700">جداول مواعيد الطبيب</h2>

        @if (session('success'))
            <div class="mb-4 text-green-600 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-right">
                    <th class="border px-4 py-2">اليوم</th>
                    <th class="border px-4 py-2">من</th>
                    <th class="border px-4 py-2">إلى</th>
                    <th class="border px-4 py-2">عدد المرضى / ساعة</th>
                    <th class="border px-4 py-2">مدة الموعد (دقيقة)</th>
                    <th class="border px-4 py-2">الحد الأقصى للمرضى</th>
                    <th class="border px-4 py-2">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedules as $schedule)
                    
                    <tr class="border-b hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ __($schedule->day) }}</td>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                        </td>
                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                        </td>
                        <td class="border px-4 py-2">{{ $schedule->patients_per_hour }}</td>
                        <td class="border px-4 py-2">{{ $schedule->appointment_duration }}</td>
                        <td class="border px-4 py-2">{{ $schedule->max_patients }}</td>
                        <td class="border px-4 py-2 space-x-2">
                            <a href="{{ route('doctor-schedule.edit', $schedule->id) }}"
                                class="text-blue-600 hover:underline">تعديل</a>

                            <form action="{{ route('doctor-schedule.destroy', $schedule->id) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if ($schedules->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">لا توجد مواعيد حاليًا.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-app-layout>

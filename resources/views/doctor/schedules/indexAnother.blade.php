<x-app-layout>
    <div class="max-w-5xl mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4 text-center">جدول المواعيد</h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

    {{--     <div class="mb-4 text-right">
            <a href="{{ route('doctor-schedule.create') }}"
               class="bg-red-600 hover:bg-blue-700 text-black px-4 py-2 rounded shadow">
                + إضافة موعد جديد
            </a>
        </div> --}}

      @if($timeRanges)
    <h3 class="text-xl font-semibold my-6 text-center">عرض الفترات الزمنية (كل ساعة)</h3>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white border border-gray-300 rounded shadow">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border">اليوم</th>
                    <th class="px-4 py-2 border">من</th>
                    <th class="px-4 py-2 border">إلى</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($timeRanges as $range)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $range['day'] }}</td>
                        <td class="px-4 py-2">{{ $range['from'] }}</td>
                        <td class="px-4 py-2">{{ $range['to'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

    </div>
</x-app-layout>

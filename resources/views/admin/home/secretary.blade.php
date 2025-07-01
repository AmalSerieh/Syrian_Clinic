<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">

        {{-- رسالة نجاح --}}
        @if (session('message'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded shadow">
                {{ session('message') }}
            </div>
        @endif

        @if ($secretary)
            <div class="bg-indigo-50 border border-indigo-300 rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-4">معلومات السكرتيرة</h2>

                <div class="grid grid-cols-2 gap-6 text-gray-800">
                    <div>
                        <p class="text-sm text-gray-500">الاسم:</p>
                        <p class="text-lg font-semibold">{{ $secretary->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">البريد الإلكتروني:</p>
                        <p class="text-lg font-semibold">{{ $secretary->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">رقم الهاتف:</p>
                        <p class="text-lg font-semibold">{{ $secretary->phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الدور:</p>
                        <p class="text-lg font-semibold capitalize">{{ $secretary->role }}</p>
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex justify-end mt-6 space-x-4">
                    <a href="{{ route('admin.secretary.replace', $secretary->id) }}"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                        ✏️ Replace
                    </a>

                   {{--  <form action="{{ route('admin.secretary.replace', $secretary->id) }}" method="POST"
                        onsubmit="return confirm('هل أنت متأكد من استبدال السكرتيرة؟');">
                        @csrf
                        @method('POST')
                        <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            ✏️ Replace
                        </button>
                    </form> --}}
                </div>
            </div>
        @else
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded shadow text-center">
                ⚠️ لم يتم إضافة سكرتيرة بعد.
                <a href="{{ route('admin.secretary.add') }}"
                    class="bg-blue-600 text-black px-4 py-2 rounded hover:bg-blue-700 transition">
                     Add secretary
                </a>
            </div>
        @endif
    </div>
</x-app-layout>

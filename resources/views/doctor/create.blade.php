<x-app-layout>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="max-w-4xl mx-auto py-10">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">إدخال الملف المهني للطبيب</h2>

        @if (session('info'))
            <div class="mb-4 p-4 bg-blue-100 text-blue-800 rounded">{{ session('info') }}</div>
        @endif

        <form action="{{ route('doctor-profile.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf


            <!-- السيرة الذاتية -->
            <div>
                <x-input-label for="biography" :value="'السيرة الذاتية'" />
                <textarea id="biography" name="biography" rows="4"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('biography') }}</textarea>
                <x-input-error :messages="$errors->get('biography')" class="mt-2" />
            </div>

            <!-- تاريخ الميلاد -->
            <div>
                <x-input-label for="date_birth" :value="'تاريخ الميلاد'" />
                <x-text-input id="date_birth" name="date_birth" type="date" class="mt-1 block w-full" :value="old('date_birth')" required />
                <x-input-error :messages="$errors->get('date_birth')" class="mt-2" />
            </div>

            <hr class="my-6">

            <h3 class="text-xl font-semibold text-gray-700">معلومات الشهادة</h3>

            <!-- اسم الشهادة -->
            <div>
                <x-input-label for="cer_name" :value="'اسم الشهادة'" />
                <x-text-input id="cer_name" name="cer_name" type="text" class="mt-1 block w-full" :value="old('cer_name')" />
                <x-input-error :messages="$errors->get('cer_name')" class="mt-2" />
            </div>

            <!-- جهة الإصدار -->
            <div>
                <x-input-label for="cer_place" :value="'جهة الإصدار'" />
                <x-text-input id="cer_place" name="cer_place" type="text" class="mt-1 block w-full" :value="old('cer_place')" />
                <x-input-error :messages="$errors->get('cer_place')" class="mt-2" />
            </div>

            <!-- تاريخ الشهادة -->
            <div>
                <x-input-label for="cer_date" :value="'تاريخ الشهادة'" />
                <x-text-input id="cer_date" name="cer_date" type="date" class="mt-1 block w-full" :value="old('cer_date')" />
                <x-input-error :messages="$errors->get('cer_date')" class="mt-2" />
            </div>

            <!-- صورة الشهادة -->
            <div>
                <x-input-label for="cer_images" :value="'صورة الشهادة'" />
                <input id="cer_images" name="cer_images" type="file"
                    class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md cursor-pointer focus:outline-none" />
                <x-input-error :messages="$errors->get('cer_images')" class="mt-2" />
            </div>

            <hr class="my-6">

            <h3 class="text-xl font-semibold text-gray-700">معلومات الخبرة</h3>

            <!-- مكان الخبرة -->
            <div>
                <x-input-label for="exp_place" :value="'مكان الخبرة'" />
                <x-text-input id="exp_place" name="exp_place" type="text" class="mt-1 block w-full" :value="old('exp_place')" />
                <x-input-error :messages="$errors->get('exp_place')" class="mt-2" />
            </div>

            <!-- عدد سنوات الخبرة -->
            <div>
                <x-input-label for="exp_years" :value="'عدد سنوات الخبرة'" />
                <x-text-input id="exp_years" name="exp_years" type="number" min=0 class="mt-1 block w-full" :value="old('exp_years')" />
                <x-input-error :messages="$errors->get('exp_years')" class="mt-2" />
            </div>

            <!-- زر الحفظ -->
            <div class="flex justify-end">
                <button type="submit"
                            class="mt-4 bg-blue-600 hover:bg-blue-700 text-black border-b-blue-900 font-bold py-2 px-4 rounded-2xl w-full flex items-center justify-center gap-2">
                        ➕ حفظ الملف المهني
                    </button>
                {{-- <x-primary-button>{{ __('حفظ الملف') }}</x-primary-button> --}}
            </div>
        </form>
    </div>
</x-app-layout>

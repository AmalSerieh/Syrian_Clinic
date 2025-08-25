<form action="{{ route('doctor.medical-record.patient_profile.store', $patient->id) }}" method="POST">
    @csrf

    <!-- عرض جميع الأخطاء فوق الفورم -->
    @if ($errors->any())
        <div class="bg-red-600 text-white rounded-lg p-4 mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-black">
        <!-- الجنس -->
        <div>
            <x-input-label for="gender" value="الجنس" />
            <select name="gender" id="gender" class="form-select w-full rounded border-gray-300 mt-1" required>
                <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>ذكر</option>
                <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>أنثى</option>
            </select>
            @error('gender')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- تاريخ الميلاد -->
        <div>
            <x-input-label for="date_birth" value="تاريخ الميلاد" />
            <x-text-input type="date" name="date_birth" id="date_birth"
                class="form-input w-full rounded border-gray-300 mt-1" required
                value="{{ old('date_birth') }}" max="{{ date('Y-m-d') }}"/>
            @error('date_birth')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- الطول -->
        <div>
            <x-input-label for="height" value="الطول (سم)" />
            <x-text-input type="number" name="height" id="height" min="1"
                class="form-input w-full rounded border-gray-300 mt-1" required
                value="{{ old('height') }}"/>
            @error('height')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- الوزن -->
        <div>
            <x-input-label for="weight" value="الوزن (كغ)" />
            <x-text-input type="number" name="weight" id="weight" min="1"
                class="form-input w-full rounded border-gray-300 mt-1" required
                value="{{ old('weight') }}"/>
            @error('weight')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- باقي الحقول بنفس الطريقة -->
        <!-- الدم، الحالة الاجتماعية، المدخن، الكحول، المخدرات -->
        <!-- مثال على المدخن -->
        <div class="flex items-center space-x-2 mt-2">
            <input type="hidden" name="smoker" value="0">
            <input type="checkbox" id="smoker" name="smoker" value="1" {{ old('smoker') ? 'checked' : '' }}
                class="h-5 w-5 text-blue-600 border-gray-300 rounded">
            <x-input-label for="smoker" value="🚬 مدخن؟" class="ml-2"/>
            @error('smoker')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- نفس الشيء للكحول و المخدرات -->
    </div>

    <div class="mt-6 text-center">
        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl">
            💾 حفظ الملف الطبي
        </button>
    </div>
</form>

<x-app-layout>
    @php
        $existingsecretary = \App\Models\User::where('role', 'secretary')->exists();
    @endphp
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if (!$existingsecretary)
        <form method="POST" action="{{ route('admin.secretary.store') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <!-- Phone Address -->
            <div class="mt-4">


                <x-input-label for="phone" :value="__('Phone')" />
                <input id="phone" type="tel" name="phone" class="form-control" required>
               {{--  <x-text-input id="phone" class="block mt-1 w-full" type="phone" name="phone" :value="old('phone')"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" /> --}}
            </div>


            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>


            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
             <!-- Date of appointment -->
            <div class="mt-4">
                <x-input-label for="date_of_appointment" :value="__('Date of appointment')" />

                <x-text-input id="date_of_appointment" class="block mt-1 w-full" type="date" name="date_of_appointment" required
                    autocomplete="new-date" />

                <x-input-error :messages="$errors->get('date_of_appointment')" class="mt-2" />
            </div>

            <div class="mt-4">
                <!-- العنوان فوق المربع -->
                <x-input-label for="gender" :value="__('Select Gender:')" class="mb-2 text-lg font-semibold text-gray-800" />

                <!-- المربع الأبيض -->
                <div class="bg-white border border-gray-300 rounded-xl shadow-sm p-4">
                    <div class="flex space-x-6 ">
                            <div class="flex items-center ">
                                <input type="radio" id="gender_secretary" name="gender" value="male" required
                                    class="mr-2 text-indigo-600 focus:ring-indigo-500 " checked>
                                <label for="gender_secretary" class="text-gray-700">male</label>
                            </div>
                            <div class="flex items-center ">
                                <input type="radio" id="gender_secretary" name="gender" value="female" required
                                    class="mr-2 text-indigo-600 focus:ring-indigo-500 ">
                                <label for="gender_secretary" class="text-gray-700">female

                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />

                </div>
            </div>




            <div class="flex items-center justify-end mt-4">


                <x-primary-button class="ms-4">
                    {{ __('Add Secretary') }}
                </x-primary-button>
            </div>
        </form>
    @else
        <!-- إذا يوجد Admin -->
        <div class="p-6 bg-red-100 border border-red-300 rounded-lg text-red-700 text-center">
            ⚠️ تم إنشاء حساب السكرتيرة مسبقًا. اضغط ل رؤية السكرتيرة .
            <br>
            <a class="underline text-sm text-gray-600 hover:text-blue-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('admin.secretary') }}">
                {{ __('Already Added Secretary ?Show Secretary') }}
            </a>
        </div>
    @endif
    <!-- CSS الخاص بالمكتبة -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css"/>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
    const phoneInput = document.querySelector("#phone");

    const iti = window.intlTelInput(phoneInput, {
        preferredCountries: ["sy", "sa", "eg", "jo"],
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch("https://ipinfo.io/json?token=YOUR_TOKEN_HERE") // يمكنك حذف هذا السطر لجعلها default
                .then(resp => resp.json())
                .then(resp => callback(resp.country))
                .catch(() => callback("sy"));
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    // عند إرسال النموذج، نحصل على الرقم الدولي ونرسله
    document.querySelector("form").addEventListener("submit", function(e) {
        const phoneInputField = document.querySelector("#phone");
        const fullNumber = iti.getNumber();
        phoneInputField.value = fullNumber;
    });
</script>

</x-app-layout>

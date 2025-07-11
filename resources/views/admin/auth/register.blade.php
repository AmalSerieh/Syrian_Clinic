<x-guest-layout>
    @php
        $existingAdmin = \App\Models\User::where('role', 'admin')->exists();
    @endphp
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if (!$existingAdmin)
        <form method="POST" action="{{ route('admin.register') }}">
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
                <x-text-input id="phone" class="block mt-1 w-full" type="phone" name="phone" :value="old('phone')"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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

           {{--  <div class="mt-4">
                <!-- العنوان فوق المربع -->
                <x-input-label for="role" :value="__('Select role:')" class="mb-2 text-lg font-semibold text-gray-800" />

                <!-- المربع الأبيض -->
                <div class="bg-white border border-gray-300 rounded-xl shadow-sm p-4">
                    <div class="flex space-x-6 ">

                        @php
                            $existingadmin = \App\Models\User::where('role', 'admin')->exists();
                        @endphp

                        @if (!$existingadmin)
                            <div class="flex items-center ">
                                <input type="radio" id="role_admin" name="role" value="admin" required
                                    class="mr-2 text-indigo-600 focus:ring-indigo-500 " checked>
                                <label for="role_admin" class="text-gray-700">admin</label>
                            </div>
                        @endif
                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />

                </div>
            </div> --}}




            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('admin.login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    @else
        <!-- إذا يوجد Admin -->
        <div class="p-6 bg-red-100 border border-red-300 rounded-lg text-red-700 text-center">
            ⚠️ تم إنشاء حساب الآدمن مسبقًا. لا يمكن إنشاء حساب جديد.
            <br>
             <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('admin.login') }}">
                    {{ __('Already registered?') }}
                </a>
        </div>
    @endif
</x-guest-layout>

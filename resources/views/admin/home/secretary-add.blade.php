@php
    $existingsecretary = \App\Models\User::where('role', 'secretary')->exists();
@endphp
@extends('layouts.admin.header')
@section('content')
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if (!$existingsecretary)
        <div class="h-screen flex items-center justify-end"
            style="background-image: url('{{ asset('images/admin/secretary/secretary.png') }}'); background-size: cover; background-position: center;">
            <form method="POST" action="{{ route('admin.secretary.store') }}"
                  onsubmit="return validatePasswords();"
                  class="bg-gray-700 bg-opacity-70 shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3 rounded-[50px] ml-16">
                @csrf

                <h1 class="text-2xl font-bold mb-6 text-center flex items-center justify-center text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="mr-3 text-blue-500">
                        <path d="M2 21a8 8 0 0 1 13.292-6" />
                        <circle cx="10" cy="8" r="5" />
                        <path d="M19 16v6" />
                        <path d="M22 19h-6" />
                    </svg>
                    Add Secretary
                </h1>

                <div class="space-y-4">

                    <!-- Secretary Name -->
                    <div>
                        <label for="name" class="flex items-center mb-1 text-sm font-medium">Secretary Name</label>
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus
                               class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="flex items-center mb-1 text-sm font-medium">Secretary Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required
                               class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="flex items-center mb-1 text-sm font-medium">Phone Number</label>
                        <input id="phone" type="tel" name="phone" required
                               class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Password + Confirm -->
                    <div class="flex gap-4">
                        <div class="w-1/2 relative">
                            <label for="password" class="flex items-center mb-1 text-sm font-medium">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                            <button type="button" onclick="togglePassword('password')"
                                    class="absolute right-2 top-8 text-white">
                                    <!-- Eye Icon -->
                              {{--   <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    class="text-blue-500" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg> --}}
                                👁️
                            </button>
                        </div>

                        <div class="w-1/2 relative">
                            <label for="password_confirmation" class="flex items-center mb-1 text-sm font-medium">Confirm</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                    class="absolute right-2 top-8 text-white">
                                👁️
                            </button>
                        </div>
                    </div>
                    <p id="passwordError" class="text-red-400 mt-2 text-sm"></p>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="flex items-center mb-1 text-sm font-medium">Gender</label>
                        <select id="gender" name="gender" required
                                class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="date_of_appointment" class="flex items-center mb-1 text-sm font-medium">Date of Appointment</label>
                        <input id="date_of_appointment" type="date" name="date_of_appointment" required
                               class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                        <x-input-error :messages="$errors->get('date_of_appointment')" class="mt-2" />
                    </div>

                    <button type="submit"
                            class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-2xl w-full flex items-center justify-center gap-2">
                        ➕ Add Secretary
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-6 bg-red-100 border border-red-300 rounded-lg text-red-700 text-center">
            ⚠️ تم إنشاء حساب السكرتيرة مسبقًا. اضغط ل رؤية السكرتيرة .
            <br>
            <a class="underline text-sm text-gray-600 hover:text-blue-800"
               href="{{ route('admin.secretary') }}">
                Already Added Secretary? Show Secretary
            </a>
        </div>
    @endif

    <!-- Scripts -->
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirm = document.getElementById("password_confirmation").value;
            const errorText = document.getElementById("passwordError");

            if (password !== confirm) {
                errorText.textContent = "Passwords do not match!";
                return false;
            } else if (password.length < 6) {
                errorText.textContent = "Password must be at least 6 characters.";
                return false;
            } else {
                errorText.textContent = "";
                return true;
            }
        }
    </script>
@endsection

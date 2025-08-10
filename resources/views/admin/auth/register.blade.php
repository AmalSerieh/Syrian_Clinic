<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{-- Clinic Dashboard --}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="overflow-auto">
    <div class="h-screen flex items-center justify-center"
        style="background-image: url('{{ asset('images/admin/auth/logreg.png') }}'); background-size: cover; background-position: center;">
        @php
            $existingAdmin = \App\Models\User::where('role', 'admin')->exists();
        @endphp
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        @if (!$existingAdmin)
            <form method="POST" action="{{ route('admin.register') }}
            class="bg-gray-700 bg-opacity-70
                shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3 rounded-[50px] ml-16">

                @csrf
                <!-- أيقونة -->
                <h1 class="text-2xl font-bold mb-6 text-center flex items-center justify-center text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="mr-3 text-blue-500">
                        <path d="M2 21a8 8 0 0 1 13.292-6" />
                        <circle cx="10" cy="8" r="5" />
                        <path d="M19 16v6" />
                        <path d="M22 19h-6" />
                    </svg>
                    Register
                </h1>

                <div class="space-y-4">

                    <!-- Full Name -->
                    <div>
                        <label class="flex items-center mb-1 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Full Name
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Email -->
                    <div>
                        <label class="flex items-center mb-1 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a3 3 0 003.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Your Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="flex items-center mb-1 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M22 16.92v3a2 2 0 01-2.18 2 19.86 19.86 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.86 19.86 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13 1.21.44 2.39.91 3.5a2 2 0 01-.45 2.11L9 10.75a16 16 0 006 6l1.42-1.42a2 2 0 012.11-.45c1.11.47 2.29.78 3.5.91a2 2 0 012 2z" />
                            </svg>
                            Phone Number
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        @error('phone')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password + Confirm -->

                    <!-- Password -->
                    <div class="w-full relative">
                        <label class="flex items-center mb-1 text-sm font-medium text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect width="16" height="11" x="4" y="11" rx="2" ry="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 018 0v4" />
                            </svg>
                            Password
                        </label>
                        <input id="password" type="password" name="password"
                            class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-2 top-8 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                        @error('password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="w-full relative">
                        <label class="flex items-center mb-1 text-sm font-medium text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect width="16" height="11" x="4" y="11" rx="2" ry="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 018 0v4" />
                            </svg>
                            Confirm
                        </label>
                        <input id="confirmPassword" type="password" name="password_confirmation"
                            class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <button type="button" onclick="togglePassword('confirmPassword')"
                            class="absolute right-2 top-8 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                        @error('password_confirmation')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <h5>you already have an account <a href="{{ route('admin.login') }}"
                            class="text-blue-500 underline">Login</a> </h5>


                    <!-- زر الإرسال -->
                    <button type="submit"
                        class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-2xl w-full flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="text-white">
                            <path d="M2 21a8 8 0 0 1 13.292-6" />
                            <circle cx="10" cy="8" r="5" />
                            <path d="M19 16v6" />
                            <path d="M22 19h-6" />
                        </svg>
                        Register
                    </button>

                    <p id="passwordError" class="text-red-400 mt-2 text-sm"></p>

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
    </div>

    <!-- Scripts -->
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirm = document.getElementById("confirmPassword").value;
            const errorText = document.getElementById("passwordError");

            if (password !== confirm) {
                errorText.textContent = "Passwords do not match!";
            } else if (password.length < 6) {
                errorText.textContent = "Password must be at least 6 characters.";
            } else {
                errorText.textContent = "";
                alert("Password is valid! Submitting...");
                // document.getElementById("form").submit(); // إذا أضفت ID للفورم
            }
        }
    </script>

</body>

</html>

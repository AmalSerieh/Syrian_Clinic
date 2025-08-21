@extends('layouts.admin.header')
@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="h-screen flex items-center justify-start"
        style="background-image: url('{{ asset('images/admin/doctor/doctor.png') }}'); background-size: cover; background-position: center;">
        <!-- فورم إضافة الطبيب -->
        {{--  <div class="bg-gray-700 bg-opacity-70 shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3"
                style="border-radius: 50px; margin-left: 60px;"> --}}
        <form method="POST" action="{{ route('admin.doctor.store') }}" id="doctorForm"
            class="bg-gray-700 bg-opacity-70 shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3 rounded-[50px] ml-16">
            @method('POST')
            @csrf
            <!-- فورم إضافة الطبيب -->


            <!-- العنوان -->
            <h1 class="text-2xl font-bold mb-6 text-center flex items-center justify-center text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="mr-3 text-blue-500">
                    <path d="M2 21a8 8 0 0 1 13.292-6" />
                    <circle cx="10" cy="8" r="5" />
                    <path d="M19 16v6" />
                    <path d="M22 19h-6" />
                </svg>
                Add Doctor
            </h1>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                <!-- Doctor Name -->
                <div>
                    <label for="name" class="flex block mb-1 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Doctor Name</label>
                    <input id="name" type="text" name="name" :value="old('name')" required autofocus
                        class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />

                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="flex block mb-1 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a3 3 0 003.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                        Doctor Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required
                        class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />

                </div>
                <!-- Phone Number -->
                <div>
                    <label for="phone" class="flex block mb-1 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M22 16.92v3a2 2 0 01-2.18 2 19.86 19.86 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.86 19.86 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13 1.21.44 2.39.91 3.5a2 2 0 01-.45 2.11L9 10.75a16 16 0 006 6l1.42-1.42a2 2 0 012.11-.45c1.11.47 2.29.78 3.5.91a2 2 0 012 2z" />
                        </svg>
                        Phone Number</label>
                    <input id="phone" type="tel" name="phone" required
                        class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />

                </div>
                {{--  <br> --}}

                <!-- Password and Confirm Password (Side by Side with Show/Hide) -->
                <div class="flex gap-4">
                    <!-- Password -->
                    <div class="w-1/2 relative">
                        <label for="password" class="flex block mb-1 text-sm font-medium text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect width="16" height="11" x="4" y="11" rx="2" ry="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 018 0v4" />
                            </svg>
                            Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-2 top-8 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>

                    <!-- Confirm Password -->
                    <div class="w-1/2 relative">
                        <label for="password_confirmation" class="flex block mb-1 text-sm font-medium text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect width="16" height="11" x="4" y="11" rx="2" ry="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 018 0v4" />
                            </svg>
                            Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password" type="password"
                            class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            class="absolute right-2 top-8 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>


                <div class="flex gap-4">


                    <!-- Room Number (enum) -->
                    <div class="w-1/2 relative">
                        <div class="mt-4">
                            <label for="room_id" class="flex block mb-1 text-sm font-medium text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7v10a2 2 0 002 2h3m10-12h3a2 2 0 012 2v10a2 2 0 01-2 2h-3m0 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14m10 0H7" />
                                </svg>
                                Room</label>
                            <select id="room_id" name="room_id"
                                class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                                <option value="" disabled selected>Select Room</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room['id'] }}">{{ $room['name'] }} - {{ $room['specialty'] }}"
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="w-1/2 relative">
                        <div class="mt-4">
                            <!-- Gender Enum -->

                            <label for="gender" class="flex block mb-1 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="7" r="4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 21h13a2 2 0 00-13 0z" />
                                </svg>
                                Gender</label>
                            <select id="gender" name="gender" required
                                class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />

                        </div>
                    </div>



                </div>


                <div class="flex gap-4">


                    <!-- type_wage Number (enum) -->
                    <div class="w-1/2 relative">
                        <div class="mt-4">
                            <label for="type_wage" class="flex block mb-1 text-sm font-medium text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7v10a2 2 0 002 2h3m10-12h3a2 2 0 012 2v10a2 2 0 01-2 2h-3m0 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14m10 0H7" />
                                </svg>
                                type_wage</label>
                            <select id="type_wage" name="type_wage"
                                class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                                <option value="" disabled selected>Select Room</option>
                                <option value="number" {{ old('gender') == 'number' ? 'selected' : '' }}>number</option>
                                <option value="percentage" {{ old('gender') == 'percentage' ? 'selected' : '' }}>percentage
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="w-1/2 relative">
                        <div class="mt-4">
                            <!-- wage Enum -->

                            <label for="wage" class="flex block mb-1 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="7" r="4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 21h13a2 2 0 00-13 0z" />
                                </svg>
                                wage</label>

                            <input id="wage" type="text" name="wage" :value="old('wage')" required autofocus
                                class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                            <x-input-error :messages="$errors->get('wage')" class="mt-2" />

                        </div>
                    </div>



                </div>

                <!-- Date of Appointment -->
                <div>
                    <label for="date_of_appointment" class=" flex block mb-1 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400  mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                        Date of Appointment</label>
                    <input id="date_of_appointment" type="date" name="date_of_appointment" required
                        class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                </div>


                <br>

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
                    Add Doctor
                </button>


                <p id="passwordError" class="text-red-400 mt-2 text-sm"></p>
            </div>
        </form>
    </div>


    </div>

    <!-- Toggle Password Script -->
    <script>
        // Toggle show/hide password
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
        // التحقق من الباسوورد عند الإرسال
        document.getElementById('doctorForm').addEventListener('submit', function(e) {
            const password = document.getElementById("password").value;
            const confirm = document.getElementById("password_confirmation").value;
            const errorText = document.getElementById("passwordError");

            if (password !== confirm) {
                errorText.textContent = "Passwords do not match!";
                e.preventDefault(); // يمنع الإرسال
            } else if (password.length < 6) {
                errorText.textContent = "Password must be at least 6 characters.";
                e.preventDefault(); // يمنع الإرسال
            } else {
                errorText.textContent = ""; // كل شيء تمام
                // الإرسال يتم تلقائيًا
            }
        });
    </script>
@endsection

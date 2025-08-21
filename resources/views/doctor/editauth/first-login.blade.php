@extends('layouts.doctor.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="h-screen flex items-center justify-center"
        style="background-image: url('{{ asset('images/admin/secretary/secretary.png') }}'); background-size: cover; background-position: center;">

        <form method="POST" action="{{ route('doctor.first-login.update') }}" onsubmit="return confirmSecretaryReplacement()"
            class="bg-gray-700 bg-opacity-70 shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3 rounded-[50px] ml-16"
            autocomplete="off">
            @csrf

            <input type="hidden" name="id" value="{{ Auth::user()->id }}" />

            <h1 class="text-2xl font-bold mb-6 text-center flex items-center justify-center text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="mr-3 text-blue-400">
                    <path d="m17 2 4 4-4 4" />
                    <path d="M3 11v-1a4 4 0 0 1 4-4h14" />
                    <path d="m7 22-4-4 4-4" />
                    <path d="M21 13v1a4 4 0 0 1-4 4H3" />
                </svg>
                Update Auth Doctor
            </h1>

            <div class="space-y-4">
                <!-- Current secretary info -->
                <div class="bg-gray-100 bg-opacity-20 p-3 rounded-xl text-blue-300 text-sm">
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                </div>

                <!-- New Name -->


                <!-- New Email -->
                <div>
                    <label for="email" class="flex items-center mb-1 text-sm font-medium">New Email</label>
                    <input id="email" type="email" name="email" required
                        class="w-full text-sm py-1.5 px-2 border border-blue-400 rounded-xl bg-gray-800 bg-opacity-50 text-blue-400">
                </div>





                <!-- Password + Confirm -->
                <div class="flex gap-4">
                    <div class="w-1/2 relative">
                        <label for="password" class="flex items-center mb-1 text-sm font-medium">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-400 rounded-xl bg-gray-800 bg-opacity-50 text-blue-400">
                        <button type="button" onclick="togglePassword('password')"
                            class="absolute right-2 top-8 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <div class="w-1/2 relative">
                        <label for="password_confirmation"
                            class="flex items-center mb-1 text-sm font-medium">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full text-sm py-1.5 px-2 pr-10 border border-blue-400 rounded-xl bg-gray-800 bg-opacity-50 text-blue-400">
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
                <p id="passwordError" class="text-blue-400 mt-2 text-sm"></p>

                <button type="submit" onclick="checkAndOpenModal()""
                    class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-2xl w-full flex items-center justify-center gap-2">
                    🔁 Update Auth Doctor
                </button>

            </div>
        </form>
          <!-- Confirmation Modal -->
        <div id="confirmationModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
            <div class="bg-gray-800 rounded-2xl p-6 w-full max-w-md text-center border-2 border-blue-500">
                <h2 class="text-xl font-bold text-blue-400 mb-4">⚠️ Confirm Replacement</h2>
                <p class="text-gray-300 mb-6">Are you sure you want to replace the doctor? The old data will be deleted.
                </p>
                <div class="flex justify-center gap-4">
                    <button onclick="submitReplacementForm()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl">Confirm</button>
                    <button onclick="closeConfirmationModal()"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl">Cancel</button>
                </div>
            </div>
        </div>

    </div>

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

        function confirmSecretaryReplacement() {
            return confirm("⚠️ Are you sure you want to replace the doctor? The old data will be deleted.");
        }

        function openConfirmationModal() {
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        function submitReplacementForm() {
            if (validatePasswords()) {
                document.querySelector('form').submit();
            }
        }

        // اغلاق عند الضغط خارج المودال
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('confirmationModal');
            if (e.target === modal) {
                closeConfirmationModal();
            }
        });

        function checkAndOpenModal() {
            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const date = document.getElementById("date_of_appointment").value.trim();
            const password = document.getElementById("password").value.trim();
            const confirm = document.getElementById("password_confirmation").value.trim();
            const errorText = document.getElementById("passwordError");

            if (!name || !email || !phone || !date || !password || !confirm) {
                errorText.textContent = "⚠️ All fields are required!";
                return false;
            }

            if (!validatePasswords()) {
                return false;
            }

            errorText.textContent = ""; // clear previous errors
            openConfirmationModal();
        }
    </script>
@endsection

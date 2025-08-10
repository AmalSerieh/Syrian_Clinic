<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Syrian_Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
@php

    $hasAdmin = App\Models\User::where('role', 'admin')->exists();
@endphp

<body class="h-screen w-screen">

    <div class="flex w-full h-full overflow-hidden rounded-xl shadow-lg bg-black">
        <!-- Left Section -->
        <div class="w-1/2 h-full bg-gray-900 text-white px-10 py-10 relative">

            <h2 class="absolute top-6 left-6 text-3xl font-bold text-blue-600">Log IN</h2>

            <div class="mt-[75px] space-y-8">
                <div class="text-center space-y-2">
                    <h3 class="text-4xl font-semibold">Welcome in our Clinic</h3>
                    <p class="text-gray-400 text-sm">Please enter your contact details to connect.</p>
                </div><br>

                <!-- Roles -->
                <div class="flex justify-center gap-6">
                    <div class="w-48 border border-blue-600 px-6 py-6 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-blue-600"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="10" cy="7" r="4" />
                            <path d="M10.3 15H7a4 4 0 0 0-4 4v2" />
                            <path d="M15 15.5V14a2 2 0 0 1 4 0v1.5" />
                            <rect width="8" height="5" x="13" y="16" rx=".899" />
                        </svg>
                        <p class="text-blue-600 text-lg font-medium">Admin</p>
                    </div>

                    <div class="w-48 border border-blue-600 px-6 py-6 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-blue-600"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="5" />
                            <path d="M20 21a8 8 0 0 0-16 0" />
                        </svg>
                        <p class="text-blue-600 text-lg font-medium">Doctor</p>
                    </div>

                    <div class="w-48 border border-blue-600 px-6 py-6 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-2 text-blue-600"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M11.5 15H7a4 4 0 0 0-4 4v2" />
                            <path
                                d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                            <circle cx="10" cy="7" r="4" />
                        </svg>
                        <p class="text-blue-600 text-lg font-medium">Secretary</p>
                    </div>
                </div>



                <!-- Form -->
                <form class="space-y-4" method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Email address</label>
                        <input type="email" placeholder="name@gmail.com" name="email" :value="old('email')"
                            required autofocus autocomplete="username"
                            class="w-[650px] h-[45px] bg-gray-800 text-white p-2 rounded-md focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Password</label>
                        <input type="password" placeholder="••••••••" name="password" required
                            autocomplete="current-password"
                            class="w-[650px] h-[45px] bg-gray-800 text-white p-2 rounded-md focus:outline-none">

                    </div> <br>

                    @if (!$hasAdmin)
                        your don't have an account?
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('admin.register') }}">
                            {{ __('Register') }}
                        </a>
                    @endif
                    {{--  <x-primary-button class="ms-3">
                        {{ __('Log in') }}
                    </x-primary-button> --}}
                    <button type="submit"
                        class="w-[500px] mx-auto  bg-blue-500 hover:bg-blue-600 transition text-white font-bold py-2 rounded-md block">
                        Log in
                    </button>


                </form>

                <!-- Footer text -->
                <p class="text-gray-500 text-xs text-center mt-4">Clinic management system</p>

            </div>
        </div>




        <!-- Right Section -->
        <div class="w-1/2 bg-blue-600 relative rounded-xl overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="{{ asset('images/admin/auth/medlink2.png') }}" alt="Background Image"
                    class="max-w-75 max-h-80 object-contain">
            </div>
        </div>



    </div>

</body>

</html>

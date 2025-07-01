<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة التحكم') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- رسالة الترحيب -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('أهلاً بك، تم تسجيل الدخول بنجاح!') }}
                </div>
                   <!-- Add Secretary  -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.secretary.add')" :active="request()->routeIs('admin.secretary.add')">
                        {{ __(' Add Secretary') }}
                    </x-nav-link>
                </div > <!-- Add Doctor  -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                   {{--  <x-nav-link :href="route('admin.doctor.add')" :active="request()->routeIs('admin.doctor.add')">
                        {{ __(' Add Doctor') }}
                    </x-nav-link> --}}
                </div >
            </div>
        </div>
    </div>
</x-app-layout>

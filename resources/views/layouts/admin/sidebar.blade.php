<!-- Sidebar -->
<div class="w-60 min-h-screen bg-[#162133] text-white flex flex-col p-4">
    <div class="flex flex-col items-center mb-6">
        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Avatar" class="w-20 h-20 rounded-full mb-3">
        <div class="text-center">
            <div class="text-sm text-gray-300">ADMIN CLINIC</div>
            <div class="font-semibold">{{ Auth::user()->name }}</div>
        </div>
    </div>

    <!-- خط رايق بعد الصورة -->
    <div class="border-t border-[#1f2a40] mb-4"></div>

    <!-- عنوان Main -->
    <div class="text-xs text-gray-400 mb-4 pl-1">Main</div>

    <!-- Navigation -->
    <nav class="flex flex-col gap-4 ">
        <a href="{{route('admin.index')}}"
            class="flex items-center gap-3 p-3 rounded  hover:bg-blue-500/15 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-layout-grid-icon lucide-layout-grid">
                <rect width="7" height="7" x="3" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="3" rx="1" />
                <rect width="7" height="7" x="14" y="14" rx="1" />
                <rect width="7" height="7" x="3" y="14" rx="1" />
            </svg>
            Home
        </a>

        <a href="{{route('admin.doctor')}}" class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-activity-icon lucide-activity">
                <path
                    d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2" />
            </svg>
            Doctors
        </a>

        <a href="{{route('admin.secretary')}}" class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user-round-pen-icon lucide-user-round-pen">
                <path d="M2 21a8 8 0 0 1 10.821-7.487" />
                <path
                    d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                <circle cx="10" cy="8" r="5" />
            </svg>
            Secretary
        </a>

        <a href="/Reports" class="flex items-center gap-3 p-3 rounded hover:bg-[#0f2f41]/50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chart-spline-icon lucide-chart-spline">
                <path d="M3 3v16a2 2 0 0 0 2 2h16" />
                <path d="M7 16c.5-2 1.5-7 4-7 2 0 2 3 4 3 2.5 0 4.5-5 5-7" />
            </svg>
            Reports
        </a>
    </nav>
</div>

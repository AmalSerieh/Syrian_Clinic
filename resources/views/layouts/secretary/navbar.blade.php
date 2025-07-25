<div
    class="bg-gradient-to-br from-[#0a0f1e] to-[#1b2236] p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow rounded-2xl">
    <h1 class="text-xl font-semibold text-white">Welcome Back, {{ Auth::user()->name }} 👋 </h1>
    <div class="relative w-full md:w-[500px]">
        <input type="text" placeholder="Search for Doctors"
            class="rounded-lg px-4 pr-12 py-2 w-full bg-[#12192b] border border-gray-600 text-sm text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-2.85z" />
            </svg>
        </div>
    </div>
</div>

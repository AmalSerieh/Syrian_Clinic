<div
    class=" bg-[#060E0E]  p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow rounded-2xl">
    <h1 class="text-xl font-semibold text-white">Welcome Back, {{ Auth::user()->name }} 👋 </h1>
    <div class="relative w-full md:w-[500px]">
        <form  id="searchForm"  method="GET" action="{{ route('admin.doctors.search') }}" class="relative w-full md:w-[500px]">
            <input type="text" name="query" id="searchQuery" placeholder="Search for Doctors"
            class="text-white p-2  rounded-xl w-full focus:outline-none bg-[#0094e7]/10 backdrop-blur-[160px] border border-[#0094e7]/100 shadow-[0_64px_64px_-32px_rgba(41,0,0,0.56)] " />
            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-2.85z" />
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const query = document.getElementById('searchQuery').value.trim();
        if (!query) {
            e.preventDefault(); // يمنع الإرسال
            alert('يرجى إدخال اسم الطبيب قبل البحث');
        }
    });
</script>

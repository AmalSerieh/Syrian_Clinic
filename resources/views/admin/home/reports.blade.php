@extends('layouts.admin.header')

@section('content')
    <div class="mt-20 space-y-6">

        {{-- All Projects --}}
        <div class="bg-[#1c2230] p-6 rounded-xl w-[60%]">
            <h2 class="text-lg font-semibold mb-4 text-white">All Projects</h2>
            <div class="flex items-center justify-around">
                {{-- Doughnut Chart --}}
                <div class="relative w-32 h-32">
                    <canvas id="projectsChart" class="w-full h-full"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-sm font-bold">
                        {{ $complete ?? 62 }}<br><span class="text-xs font-medium">Complete</span>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="space-y-3 text-white text-sm">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span><span>Complete</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-purple-500 rounded-full"></span><span>Pending</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-yellow-400 rounded-full"></span><span>Not Start</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-end gap-6 h-[200px]">
            {{-- Today's Sales --}}
            <div class="bg-[#1c2230] p-6 rounded-xl w-fit space-y-4">

                <h2 class="text-lg font-semibold text-white ">Today's Sales</h2>
                <div class="flex flex-wrap gap-4 text-sm">

                    <div class="bg-gray-900 p-4 rounded-xl text-white w-30">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-signal-icon lucide-signal text-orange-300">
                            <path d="M2 20h.01" />
                            <path d="M7 20v-4" />
                            <path d="M12 20v-8" />
                            <path d="M17 20V8" />
                            <path d="M22 4v16" />
                        </svg>
                        <p class="font-bold text-lg">$5k</p>
                        <p>Total Sales</p>
                        <span class="text-orange-300 text-xs ">+10% from yesterday</span>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-xl text-white w-30">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-clipboard-check-icon lucide-clipboard-check text-blue-300">
                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                            <path d="m9 14 2 2 4-4" />
                        </svg>
                        <p class="font-bold text-lg">500</p>
                        <p>Total Orders</p>
                        <span class="text-blue-300 text-xs">+6% from yesterday</span>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-xl text-white w-30">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-weight-icon lucide-weight text-pink-200">
                            <circle cx="12" cy="5" r="3" />
                            <path
                                d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                        </svg>
                        <p class="font-bold text-lg">9</p>
                        <p>Product Sold</p>
                        <span class="text-pink-200 text-xs">+2% from yesterday</span>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-xl text-white w-30">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-package-plus-icon lucide-package-plus text-blue-500">
                            <path d="M16 16h6" />
                            <path d="M19 13v6" />
                            <path
                                d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                            <path d="m7.5 4.27 9 5.15" />
                            <polyline points="3.29 7 12 12 20.71 7" />
                            <line x1="12" x2="12" y1="22" y2="12" />
                        </svg>
                        <p class="font-bold text-lg">12</p>
                        <p>New Customers</p>
                        <span class="text-blue-500 text-xs">+3% from yesterday</span>
                    </div>
                </div>
            </div>

            {{-- Level --}}
            <div class="bg-[#1c2230] p-2 rounded-xl w-full md:w-48">
                <h2 class="text-lg font-semibold mb-4 text-white">Level</h2>
                <div class="flex items-end space-x-2 h-28">
                    @foreach ([30, 50, 70, 45, 65, 25, 40] as $value)
                        <div class="w-4 bg-teal-400 rounded" style="height: {{ $value }}%"></div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-400">
                    <span>Volume</span>
                    <span>Service</span>
                </div>
            </div>
        </div>


        {{-- Chart Script --}}
        <script>
            const ctx = document.getElementById('projectsChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Complete', 'Pending', 'Not Start'],
                    datasets: [{
                        data: [{{ $complete ?? 62 }}, {{ $pending ?? 20 }}, {{ $notStart ?? 10 }}],
                        backgroundColor: ['#22c55e', '#8b5cf6', '#facc15'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true
                        },
                    }
                }
            });
        </script>
    @endsection

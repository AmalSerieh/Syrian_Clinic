@extends('layouts.admin.header')

@section('content')
    <x-auth-session-status class="mb-8" :status="session('status')" />

    <div class="p-6 space-y-6">

        <!-- Top Stats Cards -->
        <div class="grid grid-cols-3 gap-4 -mt-8">
            <!-- Red Card -->
            <div class="bg-red-900/30 border border-red-700 p-5 rounded-lg shadow flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-red-700 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-red-300 text-sm">Total Outcome</h2>
                        <p class="text-xl font-bold text-red-200">$632.000</p>
                    </div>
                </div>
                <p class="text-xs text-red-400 whitespace-nowrap">+1.26%</p>
            </div>

            <!-- Green Card -->
            <div
                class="bg-green-900/30 border border-green-700 p-3 rounded-lg shadow flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-green-700 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-green-300 text-sm">Total Income</h2>
                        <p class="text-xl font-bold text-green-200">$632.000</p>
                    </div>
                </div>
                <p class="text-xs text-green-400 whitespace-nowrap">+1.29%</p>
            </div>

            <!-- Yellow Card -->
            <div
                class="bg-yellow-900/30 border border-yellow-700 p-3 rounded-lg shadow flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-700 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-yellow-300 text-sm">Total Balance</h2>
                        <p class="text-xl font-bold text-yellow-200">$632.000</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-400 whitespace-nowrap">+1.29%</p>
            </div>
        </div>
        <!-- Table and Chart -->
        <div class="grid grid-cols-12 gap-6 mt-2">
            <!-- Top Products -->
            <div class="bg-[#162133] p-6 rounded-2xl shadow-lg col-span-9 overflow-x-auto">
                <h2 class="text-white text-lg font-semibold mb-0">Top Products</h2>
                <table class="w-full text-sm text-left text-gray-400 min-w-[600px]">
                    <thead class="text-xs uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">Name</th>
                            <th scope="col" class="px-4 py-3">Popularity</th>
                            <th scope="col" class="px-4 py-3">Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-700">
                            <td class="px-4 py-3">01</td>
                            <td class="px-4 py-3">Home Decore Range</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-700 rounded-full h-1">
                                    <div class="bg-yellow-200 h-1 rounded-full" style="width: 90%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-3 py-1 rounded-lg text-sm text-yellow-200 border border-yellow-200 bg-yellow-400 bg-opacity-5">
                                    46%
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-700">
                            <td class="px-4 py-3">02</td>
                            <td class="px-4 py-3">Disney Princess Dress</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-700 rounded-full h-1">
                                    <div class="bg-cyan-400 h-1 rounded-full" style="width: 70%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-3 py-1 rounded-lg text-sm text-cyan-400 border border-cyan-400 bg-cyan-400 bg-opacity-5">
                                    17%
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-700">
                            <td class="px-4 py-3">03</td>
                            <td class="px-4 py-3">Bathroom Essentials</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-700 rounded-full h-1">
                                    <div class="bg-blue-400 h-1 rounded-full" style="width: 80%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-3 py-1 rounded-lg text-sm text-blue-400 border border-blue-400 bg-blue-400 bg-opacity-5">
                                    19%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">04</td>
                            <td class="px-4 py-3">Apple Smartwatch</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-700 rounded-full h-1">
                                    <div class="bg-pink-200 h-1 rounded-full" style="width: 60%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-3 py-1 rounded-lg text-sm text-pink-200 border border-pink-200 bg-pink-400 bg-opacity-5">
                                    29%
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Customer Fulfilment -->
            <div class="bg-[#032427] rounded-2xl p-6 shadow-lg col-span-3 flex flex-col justify-between">
                <h2 class="text-white text-sm font-semibold mb-4 leading-snug">
                    Dates of Previous Month<br />and This Month
                </h2>
                <canvas id="lineChart" class="w-full h-40 mb-4"></canvas>

                <div class="flex justify-between items-center text-sm text-white">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-[#4cc9f0] rounded-full"></span>
                        <span>Last Month</span>
                    </div>
                    <div class="text-[#4cc9f0] font-semibold">320</div>
                </div>

                <div class="flex justify-between items-center text-sm text-white mt-2">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-[#f38ba0] rounded-full"></span>
                        <span>This Month</span>
                    </div>
                    <div class="text-[#f38ba0] font-semibold">413</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-12 gap-6 items-start w-full">

            <div class="col-span-2 flex flex-col items-start gap-6">
                @if ($roomsFull)
                    <div
                        class="border-2 border-dashed border-orange-400 px-6 py-5 rounded-lg w-full flex items-center gap-4 shadow-md bg-[#12192b] hover:scale-105 transition-transform duration-300">

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="w-6 h-6 text-orange-500">
                            <path d="M2 21a8 8 0 0 1 13.292-6" />
                            <circle cx="10" cy="8" r="5" />
                            <path d="M19 16v6" />
                            <path d="M22 19h-6" />
                        </svg>

                        <div class="text-base font-semibold">
                            يرجى حذف طبيب لإضافة طبيب جديد.
                        </div>
                    </div>

                    {{--
                    <div class="bg-red-600 text-white px-4 py-3 rounded-md shadow-md">
                        غرف العيادة ممتلئة
                    </div>
                    <div class="text-white text-sm mt-2">
                        يرجى حذف طبيب لإضافة طبيب جديد
                    </div> --}}
                @else
                    <a href="{{ route('admin.doctor.add') }}" class="block">
                        <div
                            class="border-2 border-dashed border-orange-400 text-white-300 px-6 py-5 rounded-lg w-full flex items-center gap-4 shadow-md bg-[#12192b] hover:scale-105 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-user-round-plus-icon text-orange-300">
                                <path d="M2 21a8 8 0 0 1 13.292-6" />
                                <circle cx="10" cy="8" r="5" />
                                <path d="M19 16v6" />
                                <path d="M22 19h-6" />
                            </svg>
                            <div class="text-base font-semibold">+ Add Doctor</div>
                        </div>
                    </a>
                @endif
                @if ($secretary)
                    <a href="{{ route('admin.secretary.replace', [$secretary->id]) }}">
                        <div
                            class="border-2 border-dashed border-red-500 text-white-300 px-6 py-5 rounded-lg w-full flex items-center gap-4 shadow-md bg-[#12192b] hover:scale-105 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-repeat-icon text-red-300">
                                <path d="m17 2 4 4-4 4" />
                                <path d="M3 11v-1a4 4 0 0 1 4-4h14" />
                                <path d="m7 22-4-4 4-4" />
                                <path d="M21 13v1a4 4 0 0 1-4 4H3" />
                            </svg>
                            <div class="text-base font-semibold">Replacing Secretary</div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('admin.secretary.add') }}" class="block">
                        <div
                            class="border-2 border-dashed border-red-500 text-white-300 px-6 py-5 rounded-lg w-full flex items-center gap-4 shadow-md bg-[#12192b] hover:scale-105 transition-transform duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-user-round-plus-icon text-orange-300">
                                <path d="M2 21a8 8 0 0 1 13.292-6" />
                                <circle cx="10" cy="8" r="5" />
                                <path d="M19 16v6" />
                                <path d="M22 19h-6" />
                            </svg>
                            <div class="text-base font-semibold">+ Add Secretary</div>
                        </div>
                    </a>
                @endif
            </div>


            <div class="col-span-10">
                <div class="bg-gradient-to-br from-[#0a0f1e] to-[#1b2236] p-3 rounded-2xl shadow-lg w-full h-full">
                    {{--   <h3 class="text-white text-lg font-semibold mb-4">Visitors</h3> --}}
                    <div class="relative h-48">
                        <canvas id="visitorChart" class="rounded-lg bg-gradient-to-t from-[#3a3f54] to-[#2c3246]"
                            style="height: 100%; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const canvas = document.getElementById('visitorChart');
            const ctx1 = canvas.getContext('2d');
            const gradient = ctx1.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0.4, 'rgba(88, 211, 255, 0.6)');
            gradient.addColorStop(0.4, 'rgba(75, 157, 187, 0.6)');
            gradient.addColorStop(1, 'rgba(18, 18, 26, 0.2)');

            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'New Visitors',
                        data: [120, 100, 110, 400, 250, 500, 430, 410, 420, 300, 240, 310],
                        backgroundColor: gradient,
                        borderColor: '#58d3ff',
                        borderWidth: 2,
                        fill: true,
                        pointBackgroundColor: function(ctx) {
                            return ctx.dataIndex === 5 ? '#fca311' : '#58d3ff';
                        },
                        pointRadius: function(ctx) {
                            return ctx.dataIndex === 5 ? 6 : 3;
                        },
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#2c2c3c'
                            },
                            ticks: {
                                color: '#ccc'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#ccc'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });


            const ctx = document.getElementById("lineChart").getContext("2d");

            const gradientLastMonth = ctx.createLinearGradient(0, 0, 0, 120);
            gradientLastMonth.addColorStop(0, "rgba(76, 201, 240, 0.3)");
            gradientLastMonth.addColorStop(1, "rgba(76, 201, 240, 0)");

            const gradientThisMonth = ctx.createLinearGradient(0, 0, 0, 120);
            gradientThisMonth.addColorStop(0, "rgba(243, 139, 160, 0.3)");
            gradientThisMonth.addColorStop(1, "rgba(243, 139, 160, 0)");

            new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["", "", "", "", "", "", ""],
                    datasets: [{
                            label: "Last Month",
                            data: [30, 50, 40, 60, 45, 65, 50],
                            fill: true,
                            backgroundColor: gradientLastMonth,
                            borderColor: "#4cc9f0",
                            tension: 0.4,
                            pointRadius: 0,
                        },
                        {
                            label: "This Month",
                            data: [40, 45, 35, 55, 50, 60, 70],
                            fill: true,
                            backgroundColor: gradientThisMonth,
                            borderColor: "#f38ba0",
                            tension: 0.4,
                            pointRadius: 0,
                        },
                    ],
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        },
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: true,
                            grid: {
                                color: "rgba(255, 255, 255, 0.05)",
                                drawTicks: false,
                                drawBorder: false,
                            },
                            ticks: {
                                display: false,
                            },
                        },
                    },
                },
            });
        </script>
    @endsection

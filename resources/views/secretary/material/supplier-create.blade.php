@extends('layouts.secretary.header')

@section('content')
    <div class="container">
        <h2>➕  Add Supplier</h2>
        <form method="POST" action="{{ route('secretary.supplier.store') }}">
            @csrf

            <div class="mb-3">
                <label for="sup_name" class="form-label">Name Supplier</label>
                <input type="text" name="sup_name" class="form-control text-cyan-950" required>
            </div>

            <div class="mb-3">
                <label for="sup_phone" class="form-label">Phone Supplier</label>
                <input type="text" name="sup_phone" class="form-control text-cyan-950">
            </div>
            <div class="mb-3">
                <label for="sup_photo" class="form-label"> Photo Supplier</label>
                <input type="file" name="sup_photo" class="form-control text-cyan-950">
            </div>

            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
    @endsection{{--
@extends('layouts.secretary.header')

@section('content')
    @php
        $materials = [
            [
                'name' => 'SCALPEL',
                'qty' => '24 Item',
                'min' => 5,
                'color' => 'bg-purple-500',
                'colors' => 'border-purple-500',
                'image' => '/images/scalpel.png',
                'suppliers' => [
                    ['price' => '$10', 'qty' => 5, 'exp' => '2025-12-31', 'status' => 'ok'],
                    ['price' => '$12', 'qty' => 3, 'exp' => '2025-11-30', 'status' => 'warn'],
                ],
            ],
            [
                'name' => 'ANTISEPTIC',
                'qty' => '24 Item',
                'min' => 10,
                'color' => 'bg-orange-500',
                'colors' => 'border-orange-500',
                'image' => '/images/anti1.webp',
                'suppliers' => [['price' => '$8', 'qty' => 4, 'exp' => '2025-10-31', 'status' => 'ok']],
            ],
            [
                'name' => 'MEDICAL ADHESIVE',
                'qty' => '24 Item',
                'min' => 7,
                'color' => 'bg-yellow-500',
                'colors' => 'border-yellow-500',
                'image' => '/images/medad.webp',
                'suppliers' => [['price' => '$5', 'qty' => 6, 'exp' => '2025-09-30', 'status' => 'ok']],
            ],
            [
                'name' => 'SCALPEL',
                'qty' => '24 Item',
                'min' => 5,
                'color' => 'bg-purple-500',
                'colors' => 'border-purple-500',
                'image' => '/images/scalpel.png',
                'suppliers' => [
                    ['price' => '$10', 'qty' => 5, 'exp' => '2025-12-31', 'status' => 'ok'],
                    ['price' => '$12', 'qty' => 3, 'exp' => '2025-11-30', 'status' => 'warn'],
                ],
            ],
            [
                'name' => 'ANTISEPTIC',
                'qty' => '24 Item',
                'min' => 10,
                'color' => 'bg-orange-500',
                'colors' => 'border-orange-500',
                'image' => '/images/anti1.webp',
                'suppliers' => [['price' => '$8', 'qty' => 4, 'exp' => '2025-10-31', 'status' => 'ok']],
            ],
            [
                'name' => 'MEDICAL ADHESIVE',
                'qty' => '24 Item',
                'min' => 7,
                'color' => 'bg-yellow-500',
                'colors' => 'border-yellow-500',
                'image' => '/images/medad.webp',
                'suppliers' => [['price' => '$5', 'qty' => 6, 'exp' => '2025-09-30', 'status' => 'ok']],
            ],
        ];
    @endphp

    <main class="flex-1 p-6" x-data="materialsData()">

        <!-- Top Buttons -->
        <div class="flex space-x-4 mb-6 -mt-4">
            <button
                class="w-1/3 h-14 rounded border-2 border-dashed border-yellow-500 px-10 text-yellow-500 hover:bg-yellow-500/20 hover:text-black transition-colors duration-300">
                Add Material
            </button>
            <button
                class="w-1/3 h-14 rounded border-2 border-dashed border-red-500 px-10 text-red-500 hover:bg-red-500/20 hover:text-white transition-colors duration-300">
                Add Material
            </button>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-4 gap-6">
            <!-- Left content: Material Cards -->
            <div class="col-span-3">
                <div class="grid grid-cols-3 gap-6">
                    @foreach ($materials as $index => $item)
                        <div
                            class="bg-[#062d3d] rounded-xl shadow-lg p-6 flex flex-col items-center text-center hover:scale-105 transition-transform duration-300">

                            <!-- Circle with Half Outer Background -->

                            <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                                <!-- الدائرة الخلفية -->
                                <div class="absolute w-[110px] h-[110px] rounded-full {{ $item['color'] }} z-[1]"></div>

                                <!-- الزينة المائلة الخارجية -->
                                <div
                                    class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[10px] {{ $item['colors'] }}
        border-r-transparent border-b-transparent rotate-[-30deg] z-0">
                                </div>

                                <!-- صورة المادة -->
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                    class="w-[90px] h-[90px] object-contain object-center rounded-full border-[3px] border-black bg-white z-[2] relative">
                            </div>



                            <!-- Name & Quantity -->
                            <p class="font-bold text-lg text-white">{{ $item['name'] }}</p>
                            <p class="text-sm text-gray-400">{{ $item['qty'] }}</p>

                            <!-- Buttons -->
                            <div class="mt-4 flex flex-col gap-2 w-full">
                                <button @click="selected = {{ $index }}"
                                    class="bg-black px-4 py-2 rounded-lg text-white hover:bg-gray-800 transition-colors">
                                    View Details
                                </button>
                                <button
                                    class="bg-green-600 px-4 py-2 rounded-lg text-white hover:bg-green-700 transition-colors">
                                    Add
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Right content: Material Details -->
            <div class="bg-[#062d3d] rounded-xl p-6 w-full shadow-xl text-white space-y-6" x-show="selected !== null"
                x-transition>
                <template x-if="selected !== null">
                    <div class="space-y-4">

                        <!-- Header: Circle image and name -->
                        <div class="flex flex-col items-center space-y-3">

                            <div class="flex flex-col items-center space-y-3">
                                <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                                    <!-- الدائرة الخلفية -->
                                    <div class="absolute w-[110px] h-[110px] rounded-full z-[1]"
                                        :class="materials[selected].color">
                                    </div>

                                    <!-- الزينة المائلة الخارجية -->
                                    <div class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[10px] rotate-[-30deg] z-0"
                                        :class="materials[selected].colors + ' border-r-transparent border-b-transparent'">
                                    </div>

                                    <!-- صورة المادة -->
                                    <img :src="materials[selected].image" :alt="materials[selected].name"
                                        class="w-[90px] h-[90px] object-contain object-center rounded-full border-[3px] border-black bg-white z-[2] relative">
                                </div>
                            </div>

                            <div class="text-center">
                                <br>
                                <p class="text-xl font-bold" x-text="materials[selected].name"></p>
                                <br>
                                <p class="text-sm text-gray-300">
                                    TOTAL QUANTITY:
                                    <span class="font-semibold text-white" x-text="materials[selected].qty"></span>
                                </p>
                                <p class="text-sm text-red-500">
                                    THE MINIMUM:
                                    <span x-text="materials[selected].min + ' Item'"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Supplier Info -->
                        <template x-for="(supplier, idx) in materials[selected].suppliers" :key="idx">
                            <div class="py-3 border-b border-gray-700 last:border-b-0">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-300">SUPPLIER OMAR</span>
                                    <span :class="supplier.status === 'warn' ? 'text-red-500' : 'text-green-400'"
                                        class="font-semibold">
                                        <span x-text="supplier.qty + ' Item'"></span>
                                        <span x-show="supplier.status === 'warn'">(Warning)</span>
                                    </span>
                                </div>

                                <div class="flex justify-between text-sm text-gray-300">
                                    <span>Price</span>
                                    <span x-text="supplier.price"></span>
                                </div>

                                <div class="flex justify-between text-sm text-gray-300">
                                    <span>Expiration Date</span>
                                    <span x-text="supplier.exp"></span>
                                </div>

                                <div class="flex justify-between text-sm text-gray-300">
                                    <span>Quality</span>
                                    <span>
                                        <template x-for="n in supplier.qty">
                                            <span class="text-yellow-400">★</span>
                                        </template>
                                    </span>
                                </div>
                            </div>
                        </template>



                    </div>
                </template>
            </div>



        </div>
    </main>

    <script>
        function materialsData() {
            return {
                selected: null,
                materials: @json($materials)
            }
        }
    </script>
@endsection

 --}}

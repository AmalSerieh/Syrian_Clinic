<!-- resources/views/secretary/material/material-show.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">📦 قائمة المواد</h2>
    </x-slot>

    <div class="p-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <a href="{{ route('secretary.material.create') }}" class="btn btn-primary mb-3">➕ إضافة مادة</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>اسم المادة</th>
                    <th>الكمية</th>
                    <th>الموقع</th>
                    <th>السعر</th>
                    <th>تاريخ الانتهاء</th>
                    <th>التحذيرات</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $material)
                    @php
                        $isExpired =
                            $material->material_expiration_date &&
                            \Carbon\Carbon::parse($material->material_expiration_date)->isPast();
                        $isLowStock =
                            $material->material_threshold &&
                            $material->material_quantity <= $material->material_threshold;
                    @endphp
                    <tr>
                        <td>{{ $material->material_name }}</td>
                        <td>{{ $material->material_quantity }}</td>
                        <td>{{ $material->material_location ?? '-' }}</td>
                        <td>{{ $material->material_price }} ل.س</td>
                        <td>
                            {{ $material->material_expiration_date ? \Carbon\Carbon::parse($material->material_expiration_date)->format('Y-m-d') : '-' }}
                        </td>
                        <td>
                            @if ($isExpired)
                                <span class="badge bg-danger">⛔ منتهية الصلاحية</span>
                            @elseif($isLowStock)
                                <span class="badge bg-warning text-dark">⚠ الكمية منخفضة</span>
                            @else
                                <span class="badge bg-success">✅ آمنة</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('secretary.material.edit', $material->id) }}"
                                class="btn btn-sm btn-warning">✏️ تعديل</a>
                            <form method="POST" action="{{ route('secretary.material.delete', $material->id) }}"
                                style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑️ حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">لا توجد مواد مسجلة.</td>
                    </tr>
                @endforelse
            </tbody>
            <button class="btn btn-info" onclick="fetchRecommendedSuppliers({{ $material->id }})">
                🔍 ترشيح أفضل الموردين
            </button>

            <!-- مكان عرض النتائج -->
            <div id="recommended-suppliers-result" class="mt-4"></div>


        </table>
    </div>
    <script>
        function fetchRecommendedSuppliers(materialId) {
            fetch(`/secretary/materials/${materialId}/recommended-suppliers`)
                .then(response => response.json())
                .then(data => {
                    let result = `
                <h4>📊 الموردين المقترحين:</h4>

                <h5>🔹 حسب الجودة:</h5>
                ${renderSupplierList(data.sorted_by_quality)}

                <h5>🔹 حسب السعر:</h5>
                ${renderSupplierList(data.sorted_by_price)}

                <h5>🔹 الأفضل توازنًا:</h5>
                ${renderSupplierList(data.sorted_by_both)}
            `;
                    document.getElementById('recommended-suppliers-result').innerHTML = result;
                })
                .catch(error => {
                    console.error("فشل في جلب البيانات:", error);
                    document.getElementById('recommended-suppliers-result').innerHTML =
                        '<div class="alert alert-danger">حدث خطأ أثناء جلب الموردين.</div>';
                });

        }

        function renderSupplierList(list) {
            if (!list.length) return '<p>لا يوجد موردين متاحين.</p>';
            return `
        <ul class="list-group mb-3">
            ${list.map(supplier => `
                            <li class="list-group-item">
                                <strong>الاسم:</strong> ${supplier.sup_name} |
                                <strong>الجودة:</strong> ${supplier.avg_quality} |
                                <strong>السعر الأدنى:</strong> ${supplier.lowest_price} |
                                <strong>النقاط:</strong> ${supplier.score}
                            </li>
                        `).join('')}
        </ul>
    `;
        }
    </script>

</x-app-layout>

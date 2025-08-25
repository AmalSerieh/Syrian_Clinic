@extends('layouts.doctor.header')
@section('content')
    <div class="p-6 bg-[#0B1622] min-h-screen text-white rounded-3xl">
        @if (session('status'))
            <div class="bg-green-900/30 border border-green-700 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    <p class="text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-600/70 border-l-4 border-red-500 text-white rounded-lg shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        @endif
        <!-- العنوان الرئيسي -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold mb-2">🩺 السجل الطبي للمريض: {{ $patient->user->name }}</h1>
            <p class="text-gray-400">إدارة ومتابعة السجل الطبي الشامل للمريض</p>
        </div>

        <!-- التخطيط الرئيسي - ثلاث أعمدة -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- العمود الأول: الإحصائيات -->
            <div class="lg:col-span-1 space-y-4">
                <!-- معلومات المريض -->
                <div class="bg-[#0f2538] rounded-2xl p-4">
                    <div class="flex items-center gap-4 mb-4">
                        @if ($patient->photo)
                            <img src="{{ asset('storage/' . $patient->photo) }}"
                                class="w-16 h-16 rounded-full object-cover border-2 border-slate-600" alt="صورة المريض">
                        @else
                            <div class="w-16 h-16 rounded-full bg-slate-700 flex items-center justify-center">
                                <i class="fas fa-user text-xl text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-bold">{{ $patient->user->name }}</h3>
                            <div class="text-sm text-gray-400 mt-1">
                                <div><i class="fas fa-envelope ml-1"></i> {{ $patient->user->email }}</div>
                                <div><i class="fas fa-phone ml-1"></i> {{ $patient->user->phone ?? 'غير متوفر' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات سريعة -->
                <div class="bg-[#0f2538] rounded-2xl p-4">
                    <h3 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2">📊 الإحصائيات</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">الأمراض</span>
                            <span class="text-blue-400 font-bold">{{ $patient->patient_record->diseases->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">الأدوية</span>
                            <span
                                class="text-green-400 font-bold">{{ $patient->patient_record->medications->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">العمليات</span>
                            <span
                                class="text-purple-400 font-bold">{{ $patient->patient_record->operations->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">الحساسيات</span>
                            <span
                                class="text-yellow-400 font-bold">{{ $patient->patient_record->allergies->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">الفحوصات</span>
                            <span
                                class="text-teal-400 font-bold">{{ $patient->patient_record->medicalAttachment->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">الملفات</span>
                            <span
                                class="text-orange-400 font-bold">{{ $patient->patient_record->medicalFiles->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- ملخص سريع -->
                <div class="bg-[#0f2538] rounded-2xl p-4">
                    <h3 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2">📝 ملخص سريع</h3>
                    <div class="text-sm text-gray-400 space-y-2">
                        <p>آخر تحديث: {{ now()->format('Y/m/d') }}</p>
                        <p>حالة السجل: <span class="text-green-400">مكتمل</span></p>
                        <p>عدد الزيارات:
                            <span class="text-blue-400 font-bold">
                                {{ $patient->visits()->where('doctor_id', Auth::user()->doctor->id)->count() }}
                            </span>
                            زيارة
                        </p>
                    </div>
                </div>
            </div>

            <!-- العمود الثاني: الأقسام -->
            <div class="lg:col-span-1">
                <div class="bg-[#0f2538] rounded-2xl p-4 h-full">
                    <h3 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2">📁 أقسام السجل الطبي</h3>

                    <div class="grid grid-cols-1 gap-3">
                        <!-- الملف الشخصي -->
                        <button onclick="loadSection('profile')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-user-circle text-blue-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>الملف الشخصي</h4>
                                <p>المعلومات الأساسية والتفاصيل الشخصية</p>
                            </div>
                        </button>

                        <!-- الأمراض -->
                        <button onclick="loadSection('diseases')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-disease text-red-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>الأمراض</h4>
                                <p>السجل المرضي والتشخيصات</p>
                            </div>
                        </button>

                        <!-- العمليات -->
                        <button onclick="loadSection('operations')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-procedures text-purple-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>العمليات</h4>
                                <p>العمليات الجراحية والإجراءات</p>
                            </div>
                        </button>

                        <!-- الأدوية -->
                        <button onclick="loadSection('medications')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-pills text-green-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>الأدوية</h4>
                                <p>الوصفات الطبية والعلاجات</p>
                            </div>
                        </button>

                        <!-- الفحوصات -->
                        <button onclick="loadSection('medicalAttachments')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-flask text-yellow-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>الفحوصات</h4>
                                <p>التحاليل والفحوصات المخبرية</p>
                            </div>
                        </button>

                        <!-- الحساسيات -->
                        <button onclick="loadSection('allergies')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-allergies text-orange-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>الحساسيات</h4>
                                <p>الحساسيات والتفاعلات الدوائية</p>
                            </div>
                        </button>

                        <!-- الملفات المرفقة -->
                        <button onclick="loadSection('medicalFiles')"
                            class="medical-record-btn bg-[#1a2d42] hover:bg-[#2a3d52] transition-all">
                            <div class="btn-icon">
                                <i class="fas fa-file-medical text-teal-400"></i>
                            </div>
                            <div class="btn-content">
                                <h4>الملفات المرفقة</h4>
                                <p>الوثائق والصور والتقارير</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- العمود الثالث: محتوى القسم -->
            <div class="lg:col-span-1">
                <div class="bg-[#0f2538] rounded-2xl p-4 h-full">
                    <div id="sectionContent">
                        <div class="text-center py-12">
                            <i class="fas fa-folder-open text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-bold text-gray-400">اختر قسمًا لعرض محتواه</h3>
                            <p class="text-sm text-gray-500 mt-2">انقر على أي قسم في القائمة لعرض محتواه هنا</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .medical-record-btn {
            padding: 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
            text-align: right;
            transition: all 0.3s ease;
            border: 1px solid #2d3748;
        }

        .medical-record-btn:hover {
            transform: translateX(-5px);
            border-color: #3b82f6;
        }

        .btn-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .btn-content {
            flex-grow: 1;
        }

        .btn-content h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .btn-content p {
            font-size: 0.875rem;
            color: #9ca3af;
        }

        /* تأثيرات للأزرار المختلفة */
        .medical-record-btn:nth-child(1):hover {
            border-color: #3b82f6;
        }

        .medical-record-btn:nth-child(2):hover {
            border-color: #ef4444;
        }

        .medical-record-btn:nth-child(3):hover {
            border-color: #8b5cf6;
        }

        .medical-record-btn:nth-child(4):hover {
            border-color: #10b981;
        }

        .medical-record-btn:nth-child(5):hover {
            border-color: #f59e0b;
        }

        .medical-record-btn:nth-child(6):hover {
            border-color: #f97316;
        }

        .medical-record-btn:nth-child(7):hover {
            border-color: #0d9488;
        }

        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .medical-record-btn {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    <script>
        // دالة لتحميل محتوى القسم
        async function loadSection(sectionType) {
            const sectionContent = document.getElementById('sectionContent');

            // عرض مؤشر التحميل
            sectionContent.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <p class="text-gray-400">جاري تحميل المحتوى...</p>
                </div>
            `;

            try {
                // بناء URL بناءً على نوع القسم
                let url = '';
                switch (sectionType) {
                    case 'profile':
                        url = '{{ route('doctor.medical-record.patient_profile', $patient->patient_record->id) }}';
                        break;
                    case 'diseases':
                        url = '{{ route('doctor.medical-record.diseases', $patient->patient_record->id) }}';
                        break;
                    case 'operations':
                        url = '{{ route('doctor.medical-record.operations', $patient->patient_record->id) }}';
                        break;
                    case 'medications':
                        url = '{{ route('doctor.medical-record.medications', $patient->patient_record->id) }}';
                        break;
                    case 'medicalAttachments':
                        url = '{{ route('doctor.medical-record.medicalAttachments', $patient->patient_record->id) }}';
                        break;
                    case 'allergies':
                        url = '{{ route('doctor.medical-record.allergies.index', $patient->patient_record->id) }}';
                        break;
                    case 'medicalFiles':
                        url = '{{ route('doctor.medical-record.medicalFiles', $patient->patient_record->id) }}';
                        break;
                }

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error('فشل في تحميل المحتوى');
                }

                const html = await response.text();
                sectionContent.innerHTML = html;

            } catch (error) {
                console.error('Error:', error);
                sectionContent.innerHTML = `
                    <div class="text-center py-8 text-red-400">
                        <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                        <p>حدث خطأ في تحميل المحتوى</p>
                        <button onclick="loadSection('${sectionType}')" class="mt-4 bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                            إعادة المحاولة
                        </button>
                    </div>
                `;
            }
        }

        // تحميل أول قسم تلقائياً (اختياري)
        document.addEventListener('DOMContentLoaded', function() {
            // يمكنك تحميل قسم معين تلقائياً عند فتح الصفحة
            loadSection('profile');
        });
    </script>
    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            loadCreateForm();
        }


        async function loadCreateForm() {
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <p>جاري تحميل النموذج...</p>
        </div>
    `;

            try {
                const response = await fetch(
                    'http://localhost:8000/doctor/medical-record/3/patient_profile/create', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                if (!response.ok) throw new Error("فشل تحميل النموذج");

                modalContent.innerHTML = await response.text();

            } catch (error) {
                modalContent.innerHTML = `
            <div class="text-center py-8 text-red-400">
                <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                <p>حدث خطأ أثناء تحميل النموذج</p>
                <button onclick="loadCreateForm()" class="mt-4 bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    إعادة المحاولة
                </button>
            </div>
        `;
            }
        }


        // إغلاق عند الضغط على الخلفية
        document.addEventListener('DOMContentLoaded', function() {
            const createModal = document.getElementById('createModal');
        });
    </script>
@endsection

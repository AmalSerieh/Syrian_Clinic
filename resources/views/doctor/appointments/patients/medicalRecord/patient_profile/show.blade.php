  <!-- حالة وجود ملف طبي -->

  <div class="p-6 bg-[#0B1622] min-h-screen text-white rounded-3xl">
        <div class="max-w-4xl mx-auto">
         <!-- العنوان -->
         <div class="mb-8 text-center">
             <h1 class="text-2xl font-bold mb-2">📋 الملف الطبي للمريض: {{ $patient->user->name }}</h1>
             <p class="text-gray-400">إدارة المعلومات الطبية الأساسية للمريض</p>
         </div>

         <!-- رسائل التنبيه -->
         @if (session('error'))
             <div class="bg-red-900/30 border border-red-700 rounded-2xl p-4 mb-6">
                 <div class="flex items-center gap-3">
                     <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                     <div>
                         <p class="text-red-300 font-medium">{{ session('error') }}</p>
                     </div>
                 </div>
             </div>
         @endif

         @if (session('status'))
             <div class="bg-green-900/30 border border-green-700 rounded-2xl p-4 mb-6">
                 <div class="flex items-center gap-3">
                     <i class="fas fa-check-circle text-green-400 text-xl"></i>
                     <p class="text-green-300">{{ session('success') }}</p>
                 </div>
             </div>
         @endif

         <!-- محتوى الصفحة -->
         <div class="bg-[#0f2538] rounded-2xl p-6 shadow-lg">
             @if (!$patientProfile)
                 <!-- حالة عدم وجود ملف طبي -->
                 <div class="text-center py-5">
                     <h3 class="text-xl font-bold text-yellow-300 mb-2">❌ لم يتم إدخال الملف الطبي بعد</h3>
                     <p class="text-gray-400 mb-6">لا يوجد ملف طبي للمريض {{ $patient->user->name }} في النظام</p>

                     <!-- شروط الإنشاء -->
                     <div class="bg-[#1a2d42] rounded-xl p-4 mb-6">
                         <h4 class="font-bold text-blue-400 mb-3">Conditions for creating a medical file:</h4>
                         <ul class="text-sm text-gray-300 space-y-2 text-right">
                             <li class="flex items-center gap-2 justify-end">
                                 <span>{{ $patient->user->name }}'s visit should be under examination, meaning he is in
                                     front of you.😊😊</span>
                                 <i class="fas fa-user-md text-green-400"></i>
                             </li>
                         </ul>
                     </div>

                     <!-- زر الإنشاء -->
                     <div class="mt-6">
                         <a href="{{ route('doctor.medical-record.patient_profile.create', $patient->id) }}"
                             class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center justify-center gap-2 mx-auto w-fit">
                             <i class="fas fa-plus-circle"></i>
                             إنشاء الملف الطبي
                             <a>
                     </div>
                 </div>
             @else
                 <!-- حالة وجود ملف طبي -->
                 @php
                     $birthDate = \Carbon\Carbon::parse($patientProfile->date_birth);
                     $age = $birthDate->age;
                     $heightMeters = $patientProfile->height / 100;
                     $bmi =
                         $heightMeters > 0 ? round($patientProfile->weight / ($heightMeters * $heightMeters), 1) : null;
                     $bloodTypes = [
                         'A+' => 'A+, AB+',
                         'A-' => 'A-, A+, AB-, AB+',
                         'B+' => 'B+, AB+',
                         'B-' => 'B-, B+, AB-, AB+',
                         'AB+' => 'AB+ فقط',
                         'AB-' => 'AB-, AB+',
                         'O+' => 'O+, A+, B+, AB+',
                         'O-' => 'الكل (O-, O+, A-, A+, B-, B+, AB-, AB+)',
                         'Gwada-' => 'Gwada-',
                     ];
                 @endphp

                 <div class="grid grid-cols-1 gap-6">
                     <!-- المعلومات الأساسية -->
                     <div class="space-y-4">
                         <h3 class="text-lg font-bold border-b border-gray-700 pb-2">المعلومات الأساسية</h3>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">الجنس:</span>
                             <span class="font-medium">{{ $patientProfile->gender == 'male' ? 'ذكر' : 'أنثى' }}</span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">تاريخ الميلاد:</span>
                             <span class="font-medium">{{ $birthDate->format('Y-m-d') }} ({{ $age }}
                                 سنة)</span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">الطول:</span>
                             <span class="font-medium">{{ $patientProfile->height }} سم</span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">الوزن:</span>
                             <span class="font-medium">{{ $patientProfile->weight }} كغ</span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">مؤشر كتلة الجسم:</span>
                             <span
                                 class="font-medium {{ $bmi ? ($bmi > 25 ? 'text-red-400' : ($bmi < 18.5 ? 'text-yellow-400' : 'text-green-400')) : 'text-gray-400' }}">
                                 {{ $bmi ?? 'غير محسوب' }}
                             </span>
                         </div>
                     </div>

                     <!-- المعلومات الطبية -->
                     <div class="space-y-4">
                         <h3 class="text-lg font-bold border-b border-gray-700 pb-2">المعلومات الطبية</h3>
                         <div class="p-3 bg-[#1a2d42] rounded-lg">
                             <div class="flex justify-between items-center mb-2">
                                 <span class="text-gray-400">فصيلة الدم:</span>
                                 <span class="font-medium">{{ $patientProfile->blood_type }}</span>
                             </div>
                             <div class="text-sm text-gray-400">
                                 يمكنه استقبل الدم من: {{ $bloodTypes[$patientProfile->blood_type] ?? 'غير معروف' }}
                             </div>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">مدخن:</span>
                             <span
                                 class="font-medium {{ $patientProfile->smoker ? 'text-red-400' : 'text-green-400' }}">
                                 {{ $patientProfile->smoker ? 'نعم' : 'لا' }}
                             </span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">يشرب الكحول:</span>
                             <span
                                 class="font-medium {{ $patientProfile->alcohol ? 'text-red-400' : 'text-green-400' }}">
                                 {{ $patientProfile->alcohol ? 'نعم' : 'لا' }}
                             </span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">يتعاطى مخدرات:</span>
                             <span class="font-medium {{ $patientProfile->drug ? 'text-red-400' : 'text-green-400' }}">
                                 {{ $patientProfile->drug ? 'نعم' : 'لا' }}
                             </span>
                         </div>
                         <div class="flex justify-between items-center p-3 bg-[#1a2d42] rounded-lg">
                             <span class="text-gray-400">الحالة الاجتماعية:</span>
                             <span class="font-medium">{{ $patientProfile->matital_status }}</span>
                         </div>
                     </div>
                 </div>

                 <!-- زر التعديل -->
                 <div class="mt-8 text-center">
                     <a href="{{ route('doctor.medical-record.patient_profile.edit', $patientProfile->id) }}"
                         class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition inline-flex items-center gap-2">
                         <i class="fas fa-edit"></i>
                         تعديل الملف الطبي
                     </a>
                 </div>
             @endif
         </div>
     </div>


      <style>
          .bg-[#0B1622] {
              background-color: #0B1622;
          }

          .bg-[#0f2538] {
              background-color: #0f2538;
          }

          .bg-[#1a2d42] {
              background-color: #1a2d42;
          }

          .rounded-2xl {
              border-radius: 1rem;
          }

          .rounded-xl {
              border-radius: 0.75rem;
          }

          .bg-blue-600:hover {
              background-color: #2563eb;
          }
      </style>

      {{-- Box 1: Basic Info --}}
      <div class="bg-gray-900 rounded-2xl p-4">
          <!-- حالة وجود ملف طبي -->
          @php
              $birthDate = \Carbon\Carbon::parse($patientProfile->date_birth);
              $age = $birthDate->age;
              $heightMeters = $patientProfile->height / 100;
              $bmi = $heightMeters > 0 ? round($patientProfile->weight / ($heightMeters * $heightMeters), 1) : null;
              $bloodTypes = [
                  'A+' => 'A+, AB+',
                  'A-' => 'A-, A+, AB-, AB+',
                  'B+' => 'B+, AB+',
                  'B-' => 'B-, B+, AB-, AB+',
                  'AB+' => 'AB+ فقط',
                  'AB-' => 'AB-, AB+',
                  'O+' => 'O+, A+, B+, AB+',
                  'O-' => 'الكل (O-, O+, A-, A+, B-, B+, AB-, AB+)',
                  'Gwada-' => 'Gwada-',
              ];
          @endphp
          <div class="flex justify-between items-center mb-4">
              <h2 class="text-white font-semibold">Public</h2>
              <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 5v14M5 12h14" />
                  </svg>
              </button>
          </div>

          {{-- Basic Data --}}
          <div class="grid grid-cols-4 gap-4 text-center">
              {{-- Weight --}}
              <div>
                  <p class="text-gray-400 text-xs flex items-center justify-center gap-1">Weight :</p>
                  <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                      {{ $patientProfile->weight ?? 'N/A' }} كغ
                  </p>
              </div>

              {{-- Height --}}
              <div>
                  <p class="text-gray-400 text-xs flex items-center justify-center gap-1">Height :</p>
                  <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                      {{ $patientProfile->height ?? 'N/A' }} سم
                  </p>
              </div>

              {{-- Gender --}}
              <div>
                  <p class="text-gray-400 text-xs flex items-center justify-center gap-1">Gender :</p>
                  <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                      {{ $patientProfile->gender ?? 'N/A' }}
                  </p>
              </div>

              {{-- Blood Group --}}
              <div>
                  <p class="text-gray-400 text-xs flex items-center justify-center gap-1">Blood :</p>
                  <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                      {{ $patientProfile->blood_type }}
                  </p>


              </div>
          </div>

          {{-- Addictions / Medical Info --}}
          <div class="mt-4">
              <h2 class="text-gray-400 font-semibold mb-2">Medical Info :</h2>
              <div class="flex flex-wrap gap-2 justify-center">
                  <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                      Smoking: {{ $patientProfile->smoker ? 'Yes' : 'No' }}
                  </span>
                  <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                      Alcohol: {{ $patientProfile->alcohol ? 'Yes' : 'No' }}
                  </span>
                  <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                      Drugs: {{ $patientProfile->drugs ? 'Yes' : 'No' }}
                  </span>
                  <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                      Marital Status:{{ $patientProfile->matital_status }}
                  </span>
                  <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md"
                      class="font-medium {{ $bmi ? ($bmi > 25 ? 'text-red-400' : ($bmi < 18.5 ? 'text-yellow-400' : 'text-green-400')) : 'text-gray-400' }}">
                      BMI: {{ $bmi ?? 'غير محسوب' }}
                  </span>
              </div>
          </div>
          <div class="text-sm text-gray-400">
              يمكنه استقبل الدم من: {{ $bloodTypes[$patientProfile->blood_type] ?? 'غير معروف' }}
          </div>
      </div>

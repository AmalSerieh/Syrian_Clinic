@foreach ($appointments as $appointment)
    <div class="card mb-3 p-3 border rounded shadow-sm"
        style="background-color:
        @switch($appointment->location_type)
            @case('in_Home') #f8d7da @break
            @case('on_Street') #fff3cd @break
            @case('in_Clinic') #d1ecf1 @break
            @case('at_Doctor') #d4edda @break
            @default #ffffff
        @endswitch
    ">
        <p><strong>الاسم:</strong> {{ $appointment->patient->user->name }}</p>
        <p><strong>الوقت:</strong> {{ $appointment->start_time }}-{{ $appointment->end_time }}</p>
        <p><strong>الحالة:</strong>
            @switch($appointment->location_type)
                @case('in_Home')
                    في المنزل
                @break

                @case('on_Street')
                    في الطريق
                @break

                @case('in_Clinic')
                    في العيادة
                @break

                @case('at_Doctor')
                    داخل المعاينة
                @break

                @default
                    غير معروف
            @endswitch
        </p>
        <p class="border px-4 py-2">
            <a href="{{ route('doctor.patients.medicalRecord.show', $appointment->patient_id) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1 rounded inline-block text-center">
                عرض سجل المريض
            </a>
        </p>

        @if ($appointment->location_type === 'in_Clinic')
            <form method="POST" action="{{ route('doctor.appointments.enterConsultation', $appointment->id) }}">
                @csrf
                <button type="submit" class="btn btn-primary">🔍 بدء المعاينة</button>
            </form>
        @endif
    </div>
@endforeach

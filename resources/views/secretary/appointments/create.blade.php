<!-- resources/views/secretary/appointments/create.blade.php -->
<div x-data="appointmentBooking()" class="p-6 bg-white rounded-lg shadow">
    <!-- اختيار المريض والطبيب -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">المريض</label>
            <select x-model="patientId" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="">اختر المريض</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">الطبيب</label>
            <select x-model="doctorId" @change="loadDays()" class="w-full p-2 border border-gray-300 rounded-md">
                <option value="">اختر الطبيب</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->user->name }} ({{ $doctor->specialization->name }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- عرض الأيام -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 x-text="month" class="text-xl font-bold text-gray-800"></h3>
            <div class="flex space-x-2">
                <button @click="changeMonth(-1)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                    الشهر السابق
                </button>
                <button @click="changeMonth(1)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                    الشهر التالي
                </button>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-2">
            <template x-for="day in days" :key="day.date">
                <div
                    @click="day.status === 'available' && loadTimes(day.date)"
                    :class="{
                        'bg-green-100 hover:bg-green-200 cursor-pointer': day.status === 'available',
                        'bg-red-100': day.status === 'not_working',
                        'bg-gray-200': day.status === 'past',
                        'bg-blue-100': day.status === 'full',
                        'ring-2 ring-blue-500': selectedDate === day.date
                    }"
                    class="p-3 rounded-lg text-center transition-colors"
                >
                    <div x-text="day.day_name" class="text-sm font-medium"></div>
                    <div x-text="day.day_number" class="text-lg font-bold"></div>
                    <div x-show="day.status === 'available' || day.status === 'full'"
                         x-text="`${day.appointments_count}/${day.max_patients}`"
                         class="text-xs mt-1">
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- عرض الأوقات -->
    <div x-show="times.length > 0" class="mb-8">
        <h3 x-text="`الأوقات المتاحة ليوم ${selectedDateFormatted}`" class="text-xl font-bold mb-4 text-gray-800"></h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <template x-for="time in times" :key="time.time_slot">
                <button
                    @click="bookAppointment(time.time_slot)"
                    :disabled="!time.is_available"
                    :class="{
                        'bg-blue-500 text-white hover:bg-blue-600': time.is_available,
                        'bg-gray-300 text-gray-500 cursor-not-allowed': !time.is_available
                    }"
                    class="p-3 rounded-lg text-center transition-colors"
                >
                    <div x-text="`${time.start_time} - ${time.end_time}`" class="font-medium"></div>
                    <div x-text="`${time.booked_count}/${time.max_patients}`" class="text-sm"></div>
                </button>
            </template>
        </div>
    </div>

    <!-- نموذج الحجز -->
    <form x-show="selectedTime" method="POST" action="{{ route('secretary.appointments.store') }}" class="mt-6">
        @csrf
        <input type="hidden" name="patient_id" x-model="patientId">
        <input type="hidden" name="doctor_id" x-model="doctorId">
        <input type="hidden" name="date" x-model="selectedDate">
        <input type="hidden" name="time_slot" x-model="selectedTime">

        <div class="bg-blue-50 p-4 rounded-lg">
            <h4 class="font-bold text-lg mb-2">تفاصيل الحجز</h4>
            <p x-text="`المريض: ${patientName}`"></p>
            <p x-text="`الطبيب: ${doctorName}`"></p>
            <p x-text="`التاريخ: ${selectedDateFormatted}`"></p>
            <p x-text="`الوقت: ${selectedTime}`"></p>
        </div>

        <button type="submit" class="mt-4 px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
            تأكيد الحجز
        </button>
    </form>
</div>

<script>
function appointmentBooking() {
    return {
        patientId: '',
        doctorId: '',
        patientName: '',
        doctorName: '',
        month: '',
        monthOffset: 0,
        days: [],
        times: [],
        selectedDate: '',
        selectedDateFormatted: '',
        selectedTime: '',
        isLoading: false,

        init() {
            // يمكنك تعيين قيم افتراضية هنا إذا لزم الأمر
        },

        loadDays() {
            if (!this.doctorId) return;

            this.isLoading = true;
            fetch(`/secretary/doctors/${this.doctorId}/available-days?month_offset=${this.monthOffset}`)
                .then(res => res.json())
                .then(data => {
                    this.month = data.month;
                    this.days = data.days;
                    this.monthOffset = data.month_offset;

                    // تحديث أسماء المريض والطبيب
                    const patientSelect = document.getElementById('patientId');
                    const doctorSelect = document.getElementById('doctorId');
                    this.patientName = patientSelect.options[patientSelect.selectedIndex]?.text;
                    this.doctorName = doctorSelect.options[doctorSelect.selectedIndex]?.text;
                })
                .finally(() => this.isLoading = false);
        },

        changeMonth(offset) {
            this.monthOffset += offset;
            this.loadDays();
        },

        loadTimes(date) {
            this.selectedDate = date;
            this.selectedDateFormatted = new Date(date).toLocaleDateString('ar-EG');
            this.selectedTime = '';

            fetch(`/secretary/doctors/${this.doctorId}/available-times?date=${date}`)
                .then(res => res.json())
                .then(data => {
                    this.times = data.time_slots;
                });
        },

        bookAppointment(timeSlot) {
            this.selectedTime = timeSlot;
            // التمرير إلى نموذج الحجز
            document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
        }
    }
}
</script>

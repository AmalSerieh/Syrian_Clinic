<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">

        <!-- معلومات السكرتيرة الحالية -->
        <div class="bg-gray-100 p-4 rounded shadow mb-6">
            <h2 class="text-lg font-bold mb-2">معلومات السكرتيرة الحالية:</h2>
            <p><strong>الاسم:</strong> {{ $secretary->name }}</p>
            <p><strong>الإيميل:</strong> {{ $secretary->email }}</p>
            <p><strong>رقم الهاتف:</strong> {{ $secretary->phone }}</p>
        </div>

        <!-- الفورم -->
        <form action="{{ route('admin.secretary.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off"
       >
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $secretary->id }}"/>

            <div class="mb-4">
                <label for="name">الاسم</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required autocomplete="off">
            </div>

            <div class="mb-4">
                <label for="email">الإيميل</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" required autocomplete="off">
            </div>

            <div class="mb-4">
                <label for="phone">رقم الهاتف</label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2" required autocomplete="off">
            </div>


            <div class="mb-4">
                <label for="password">كلمة المرور الجديدة</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required autocomplete="new-password">
            </div>

            <div class="mb-4">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" autocomplete="off">
            </div>

            <button type="submit" class="bg-red-400 text-black px-4 py-2 rounded">استبدال السكرتيرة</button>
        </form>
    </div>
    <script>
    function confirmSecretaryReplacement() {
        return confirm("⚠️ هل أنت متأكد من أنك تريد استبدال السكرتيرة؟ سيتم حذف بيانات السكرتيرة الحالية واستبدالها بالجديدة.");
    }
</script>

</x-app-layout>

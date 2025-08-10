@extends('layouts.secretary')

@section('content')
    <h1 class="text-xl mb-4 font-bold">الزيارات بانتظار الدفع</h1>

    @foreach ($visits as $visit)
        <div class="border p-4 rounded shadow mb-3">
            <p><strong>المريض:</strong> {{ $visit->patient->name }}</p>
            <p><strong>الطبيب:</strong> {{ $visit->doctor->name }}</p>
            <p><strong>السعر:</strong> {{ $visit->v_price }} د.أ</p>

            <form method="POST" action="{{ route('secretary.visits.confirmPayment', $visit->id) }}">
                @csrf
                @method('PUT')

                <label for="payment_method">طريقة الدفع:</label>
                <select name="payment_method" class="border rounded p-1" required>
                    <option value="cash">نقداً</option>
                    <option value="card">بطاقة</option>
                    <option value="transfer">تحويل</option>
                </select>

                <button type="submit" class="ml-2 bg-green-500 text-white px-4 py-1 rounded">تأكيد الدفع</button>
            </form>
        </div>
    @endforeach
@endsection

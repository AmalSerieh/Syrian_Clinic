<h4>الأدوية المؤقتة</h4>
@foreach ($current as $med)
    <div>
        <strong>{{ $med['med_name'] }}</strong>
        <p>النوع: {{ $med['med_type'] }}</p>
        <p>النسبة المئوية: {{ $med['progress_percent % '] }}%</p>
    </div>
@endforeach

<h4>الأدوية المزمنة</h4>
@foreach ($chronic as $med)
    <div>
        <strong>{{ $med['med_name'] }}</strong>
        <p>نشط: {{ $med['is_active'] ? 'نعم' : 'لا' }}</p>
        <p>أُخذ حتى الآن: {{ $med['taken_till_now'] }}</p>
    </div>
@endforeach

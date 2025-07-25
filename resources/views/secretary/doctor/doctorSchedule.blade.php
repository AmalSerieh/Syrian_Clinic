@extends('layouts.secretary.header')

@section('content')

<h2>📅 جدول دوام الطبيب: {{ $doctor->user->name }}</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>اليوم</th>
                <th>الدوام</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedule as $item)
                <tr>
                    <td>{{ __($item['day']) }}</td>
                    <td>
                        @if($item['has_shift'])
                            من {{ \Carbon\Carbon::parse($item['start_time'])->format('H:i') }}
                            إلى {{ \Carbon\Carbon::parse($item['end_time'])->format('H:i') }}
                        @else
                            🚫 لا يوجد دوام
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

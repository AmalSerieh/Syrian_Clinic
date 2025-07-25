@foreach($appointments as $appointment)
<tr>
    <td>{{ $appointment->patient->user->name }}</td> <br><br>
    <td>{{ $appointment->date }}</td><br><br>
    <td>{{ $appointment->start_time }} - {{ $appointment->end_time }}</td><br><br>
    <td>
        <form method="POST" action="{{ route('secretary.appointment.confirm', $appointment->id) }}">
            @csrf
            <button class="btn btn-success">تأكيد</button>
        </form>
    </td>
    <td>
        <form method="POST" action="{{ route('secretary.appointment.cancel', $appointment->id) }}">
            @csrf
            <button class="btn btn-danger">إلغاء</button>
        </form>
    </td>
</tr>
@endforeach

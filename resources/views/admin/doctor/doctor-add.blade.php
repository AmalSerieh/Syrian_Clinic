<!-- resources/views/admin/doctor/add.blade.php -->
<x-app-layout>

    @section('content')
        <div class="add-doctor-form">
            <h2>➕ Add Doctor</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('message')" />

            <form action="{{ route('admin.doctor.store') }}" method="POST">
                @method('POST')
                @csrf

                <input type="text" name="name" placeholder="Doctor name" value="{{ old('name') }}" required>
                <input type="email" name="email" placeholder="Doctor email" value="{{ old('email') }}" required>
                <input type="text" name="phone" placeholder="Phone Number" value="{{ old('phone') }}" required>

                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

                <select name="room_id" required>
                    <option value="">Select Room</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room['id'] }}">{{ $room['name'] }}"{{ $room['specialty'] }}"</option>
                    @endforeach
                </select>



                <input type="date" name="date_of_appointment" value="{{ old('date_of_appointment') }}" required>

                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>

                <button type="submit">➕ Add</button>
            </form>
        </div>
    </x-app-layout>

@extends('layouts.doctor.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @endsection


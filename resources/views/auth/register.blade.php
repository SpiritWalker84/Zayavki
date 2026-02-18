@extends('layouts.app')

@section('title', ' — Регистрация')

@section('content')
<h1>Регистрация</h1>
<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group">
        <label>Имя</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label>Пароль</label>
        <input type="password" name="password" required>
        @error('password') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label>Подтверждение пароля</label>
        <input type="password" name="password_confirmation" required>
    </div>
    <button type="submit">Зарегистрироваться</button>
</form>
@endsection

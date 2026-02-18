@extends('layouts.app')

@section('title', ' — Вход')

@section('content')
<h1>Вход</h1>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label>Пароль</label>
        <input type="password" name="password" required>
        @error('password') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal;">
            <input type="checkbox" name="remember" style="width: auto;">
            Запомнить меня
        </label>
    </div>
    <button type="submit">Войти</button>
</form>
@endsection

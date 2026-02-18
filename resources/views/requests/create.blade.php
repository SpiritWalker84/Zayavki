@extends('layouts.app')

@section('title', ' — Создание заявки')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1>Создание заявки в ремонтную службу</h1>
    
    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('requests.store') }}" style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        
        <div class="form-group">
            <label for="client_name">Имя клиента <span style="color: red;">*</span></label>
            <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required 
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
            @error('client_name')
                <span style="color: #dc2626; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Телефон <span style="color: red;">*</span></label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required 
                   placeholder="+7 (999) 123-45-67"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
            @error('phone')
                <span style="color: #dc2626; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">Адрес <span style="color: red;">*</span></label>
            <input type="text" id="address" name="address" value="{{ old('address') }}" required 
                   placeholder="г. Москва, ул. Примерная, д. 1, кв. 1"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
            @error('address')
                <span style="color: #dc2626; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="problem_text">Описание проблемы <span style="color: red;">*</span></label>
            <textarea id="problem_text" name="problem_text" rows="5" required 
                      placeholder="Опишите проблему подробно..."
                      style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem; resize: vertical;">{{ old('problem_text') }}</textarea>
            @error('problem_text')
                <span style="color: #dc2626; font-size: 0.875rem;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" style="background: #2563eb; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 1rem; width: 100%;">
            Создать заявку
        </button>
    </form>
</div>
@endsection

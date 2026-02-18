@extends('layouts.app')

@section('title', ' — Панель мастера')

@section('content')
<div>
    <h1>Панель мастера</h1>
    <p style="color: #6b7280; margin-bottom: 1.5rem;">Заявки, назначенные на вас</p>
    
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

    @if($requests->count() > 0)
        <div style="display: grid; gap: 1.5rem;">
            @foreach($requests as $request)
                <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h2 style="margin: 0 0 0.5rem 0; font-size: 1.25rem;">Заявка #{{ $request->id }}</h2>
                            @php
                                $statusColors = [
                                    'new' => '#3b82f6',
                                    'assigned' => '#f59e0b',
                                    'in_progress' => '#8b5cf6',
                                    'done' => '#10b981',
                                    'canceled' => '#ef4444',
                                ];
                                $statusLabels = [
                                    'new' => 'Новая',
                                    'assigned' => 'Назначена',
                                    'in_progress' => 'В работе',
                                    'done' => 'Выполнена',
                                    'canceled' => 'Отменена',
                                ];
                            @endphp
                            <span style="background: {{ $statusColors[$request->status] ?? '#6b7280' }}; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem;">
                                {{ $statusLabels[$request->status] ?? $request->status }}
                            </span>
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">
                            Создана: {{ $request->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <div style="margin-bottom: 0.5rem;"><strong>Клиент:</strong> {{ $request->client_name }}</div>
                        <div style="margin-bottom: 0.5rem;"><strong>Телефон:</strong> {{ $request->phone }}</div>
                        <div style="margin-bottom: 0.5rem;"><strong>Адрес:</strong> {{ $request->address }}</div>
                        <div style="margin-top: 1rem; padding: 1rem; background: #f9fafb; border-radius: 0.25rem;">
                            <strong>Описание проблемы:</strong>
                            <p style="margin: 0.5rem 0 0 0; white-space: pre-wrap;">{{ $request->problem_text }}</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        @if($request->canBeTaken())
                            <form method="POST" action="{{ route('master.requests.take', $request) }}" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: #8b5cf6; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.25rem; cursor: pointer; font-weight: 500;">
                                    Взять в работу
                                </button>
                            </form>
                        @endif

                        @if($request->canBeCompleted())
                            <form method="POST" action="{{ route('master.requests.complete', $request) }}" style="display: inline;"
                                  onsubmit="return confirm('Завершить заявку #{{ $request->id }}?');">
                                @csrf
                                <button type="submit" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.25rem; cursor: pointer; font-weight: 500;">
                                    Завершить
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="background: white; padding: 3rem; border-radius: 0.5rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <p style="color: #6b7280; font-size: 1.125rem;">Нет назначенных заявок</p>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', ' — Панель диспетчера')

@section('content')
<div>
    <h1>Панель диспетчера</h1>
    
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

    <!-- Фильтр по статусу -->
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('dispatcher.index') }}" style="display: flex; gap: 1rem; align-items: end;">
            <div style="flex: 1;">
                <label for="status" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Фильтр по статусу:</label>
                <select id="status" name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                    <option value="">Все статусы</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>Новая</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Назначена</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>В работе</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Выполнена</option>
                    <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Отменена</option>
                </select>
            </div>
            <button type="submit" style="background: #2563eb; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.25rem; cursor: pointer;">
                Применить
            </button>
            <a href="{{ route('dispatcher.index') }}" style="background: #6b7280; color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; text-decoration: none; display: inline-block;">
                Сбросить
            </a>
        </form>
    </div>

    <!-- Список заявок -->
    @if($requests->count() > 0)
        <div style="background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">ID</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Клиент</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Телефон</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Адрес</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Статус</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Мастер</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 1rem;">#{{ $request->id }}</td>
                            <td style="padding: 1rem;">{{ $request->client_name }}</td>
                            <td style="padding: 1rem;">{{ $request->phone }}</td>
                            <td style="padding: 1rem;">{{ Str::limit($request->address, 30) }}</td>
                            <td style="padding: 1rem;">
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
                            </td>
                            <td style="padding: 1rem;">
                                {{ $request->assignedTo ? $request->assignedTo->name : '—' }}
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    @if($request->status === 'new')
                                        <form method="POST" action="{{ route('dispatcher.requests.assign', $request) }}" style="display: inline;">
                                            @csrf
                                            <select name="master_id" required style="padding: 0.25rem; border: 1px solid #d1d5db; border-radius: 0.25rem; font-size: 0.875rem;">
                                                <option value="">Выберите мастера</option>
                                                @foreach($masters as $master)
                                                    <option value="{{ $master->id }}">{{ $master->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" style="background: #10b981; color: white; padding: 0.25rem 0.75rem; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.875rem; margin-left: 0.25rem;">
                                                Назначить
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($request->canBeCanceled())
                                        <form method="POST" action="{{ route('dispatcher.requests.cancel', $request) }}" style="display: inline;" 
                                              onsubmit="return confirm('Отменить заявку #{{ $request->id }}?');">
                                            @csrf
                                            <button type="submit" style="background: #ef4444; color: white; padding: 0.25rem 0.75rem; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.875rem;">
                                                Отменить
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1.5rem;">
            {{ $requests->links() }}
        </div>
    @else
        <div style="background: white; padding: 3rem; border-radius: 0.5rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <p style="color: #6b7280; font-size: 1.125rem;">Заявок не найдено</p>
        </div>
    @endif
</div>
@endsection

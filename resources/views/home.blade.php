@extends('layouts.app')
@section('content')
<h2>Заказы</h2>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

<a href="{{ route('home.orders.create') }}" class="btn btn-primary mb-3">Добавить заказ</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>№</th><th>Дата заказа</th><th>ФИО клиента</th>
            <th>Позиций</th><th>Сумма</th><th>Действия</th>
        </tr>
    </thead>
    <tbody>
    @forelse($orders as $order)
        <tr>
            <td>{{ $order->order_id }}</td>
            <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d.m.Y') }}</td>
            <td>{{ $order->user->full_name }}</td>
            <td>{{ $order->positionsCount }}</td>
            <td>{{ number_format($order->totalSum, 2, ',', ' ') }} ₽</td>
            <td>
                <a href="{{ route('home.orders.show', $order) }}" class="btn btn-sm btn-info">Состав</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('home.orders.edit', $order) }}" class="btn btn-sm btn-warning">Изменить</a>
                @endif
                <form action="{{ route('home.orders.destroy', $order) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Удалить заказ?')">Удалить</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="6">Заказов пока нет.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
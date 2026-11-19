@extends('layouts.app')
@section('content')
<h2>Редактирование заказа №{{ $order->order_id }}</h2>

<form action="{{ route('home.orders.update', $order) }}" method="POST" class="mb-3">
    @csrf @method('PUT')
    <label>Дата заказа</label>
    <input type="date" name="order_date"
           value="{{ \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') }}"
           class="form-control mb-2" required>
    <button class="btn btn-primary">Сохранить дату</button>
</form>

<h4>Товарные позиции</h4>
<table class="table table-sm">
    <thead><tr><th>Товар</th><th>Размер</th><th>Кол-во</th><th>Цена</th><th>Сумма</th><th></th></tr></thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->stockItem->product->name }}</td>
            <td>{{ $item->stockItem->size->value }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_price, 2, ',', ' ') }} ₽</td>
            <td>{{ number_format($item->quantity * $item->unit_price, 2, ',', ' ') }} ₽</td>
            <td>
                <form action="{{ route('home.orders.item.destroy', [$order, $item]) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Удалить</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot><tr><th colspan="4">Итого</th><th colspan="2">{{ number_format($order->totalSum, 2, ',', ' ') }} ₽</th></tr></tfoot>
</table>

<a href="{{ route('home') }}" class="btn btn-secondary">К списку заказов</a>
@endsection
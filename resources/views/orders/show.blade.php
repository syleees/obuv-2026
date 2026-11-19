@extends('layouts.app')
@section('content')
<h2>Заказ №{{ $order->order_id }}</h2>
<p>Дата: {{ \Carbon\Carbon::parse($order->order_date)->format('d.m.Y') }}</p>
<p>Клиент: {{ $order->user->full_name }}</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Модель / наименование / производство</th>
            <th>Размер</th>
            <th>Кол-во</th>
            <th>Цена за ед.</th>
            <th>Сумма</th>
        </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->stockItem->product->name }} — {{ $item->stockItem->product->manufacturer->name ?? '' }}</td>
            <td>{{ $item->stockItem->size->value }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_price, 2, ',', ' ') }} ₽</td>
            <td>{{ number_format($item->quantity * $item->unit_price, 2, ',', ' ') }} ₽</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot><tr><th colspan="4">Итого по заказу</th><th>{{ number_format($order->totalSum, 2, ',', ' ') }} ₽</th></tr></tfoot>
</table>

<a href="{{ route('home') }}" class="btn btn-secondary">К списку заказов</a>
@endsection
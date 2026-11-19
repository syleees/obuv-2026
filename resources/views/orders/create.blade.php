@extends('layouts.app')
@section('content')
<h2>Новый заказ</h2>

<form method="GET" action="{{ route('home.orders.create') }}" class="mb-3 d-flex">
    <input type="text" name="search" value="{{ $search }}" class="form-control me-2" placeholder="Поиск по наименованию">
    <button class="btn btn-secondary">Найти</button>
</form>

<h4>Каталог</h4>
<table class="table table-sm">
    <thead><tr><th>Наименование</th><th>Производитель</th><th>Цена</th><th></th></tr></thead>
    <tbody>
    @foreach($products as $p)
        <tr>
            <td>{{ $p->name }}</td>
            <td>{{ $p->manufacturer->name ?? '—' }}</td>
            <td>{{ number_format($p->price, 2, ',', ' ') }} ₽</td>
            <td><a href="{{ route('home.orders.select', $p) }}" class="btn btn-sm btn-primary">Выбрать</a></td>
        </tr>
    @endforeach
    </tbody>
</table>

<h4>Черновик заказа</h4>
@if($draftItems->isEmpty())
    <p>Позиции не добавлены.</p>
@else
<table class="table table-sm">
    <thead><tr><th>Товар</th><th>Размер</th><th>Кол-во</th><th>Цена</th><th>Сумма</th><th></th></tr></thead>
    <tbody>
    @foreach($draftItems as $d)
        <tr>
            <td>{{ $d->product->name }} ({{ $d->product->manufacturer->name ?? '' }})</td>
            <td>{{ $d->size->value }}</td>
            <td>{{ $d->draftQuantity }}</td>
            <td>{{ number_format($d->unitPrice, 2, ',', ' ') }} ₽</td>
            <td>{{ number_format($d->lineTotal, 2, ',', ' ') }} ₽</td>
            <td>
                <form action="{{ route('home.orders.draft.remove', $d) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Удалить</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot><tr><th colspan="4">Итого</th><th colspan="2">{{ number_format($draftTotal, 2, ',', ' ') }} ₽</th></tr></tfoot>
</table>
@endif

<hr>
<form action="{{ route('home.orders.store') }}" method="POST">
    @csrf
    <div class="mb-2">
        <label>Клиент</label>
        <select name="user_id" class="form-select" required>
            @foreach($clients as $c)
                <option value="{{ $c->user_id }}">{{ $c->full_name }} ({{ $c->login }})</option>
            @endforeach
        </select>
    </div>
    <div class="mb-2">
        <label>Дата заказа</label>
        <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="form-control" required>
    </div>
    <button class="btn btn-success" @if($draftItems->isEmpty()) disabled @endif>Сохранить заказ</button>
    <a href="{{ route('home') }}" class="btn btn-link">Отмена</a>
</form>
@endsection
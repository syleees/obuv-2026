@extends('layouts.app')
@section('content')
<h2>{{ $product->name }}</h2>
<p>Производитель: {{ $product->manufacturer->name ?? '—' }}</p>
<p>Категория: {{ $product->category->name ?? '—' }}</p>
<p>Цена: {{ number_format($product->price, 2, ',', ' ') }} ₽</p>

<form action="{{ route('home.orders.draft.add') }}" method="POST">
    @csrf
    <div class="mb-2">
        <label>Размер</label>
        <select name="stock_item_id" class="form-select" required>
            @foreach($product->stockItems as $s)
                <option value="{{ $s->stock_item_id }}" @if($s->available_quantity <= 0) disabled @endif>
                    {{ $s->size->value }} — в наличии {{ $s->available_quantity }} пар
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-2">
        <label>Количество пар</label>
        <input type="number" name="quantity" min="1" value="1" class="form-control" required>
    </div>
    <button class="btn btn-success">Добавить в заказ</button>
    <a href="{{ route('home.orders.create') }}" class="btn btn-link">Назад к каталогу</a>
</form>
@endsection
@extends('layouts.app')

@section('title', $product->name . ' — Чудо Обувь')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <a href="{{ route('catalog') }}" class="btn btn-outline-secondary mb-3">
                Назад в каталог
            </a>

            @php
                $rel = 'assets/images/catalog/' . $product->image_path;

                $imageUrl = asset(
                    file_exists(public_path($rel))
                        ? $rel
                        : 'assets/images/picture.png'
                );
            @endphp

            <div class="product-card">
                <div class="row g-0">

                    <div class="col-md-5 p-3">
                        <img
                            src="{{ $imageUrl }}"
                            class="img-fluid w-100"
                            alt="{{ $product->name }}"
                        >
                    </div>

                    <div class="col-md-7">
                        <div class="product-content">

                            <h1>{{ $product->name }}</h1>

                            <p>
                                <strong>Производитель:</strong>
                                {{ $product->manufacturer->name ?? 'Не указан' }}
                            </p>

                            <p>
                                <strong>Категория:</strong>
                                {{ $product->category->name ?? 'Не указана' }}
                            </p>

                            <p>
                                <strong>Описание:</strong>
                                {{ $product->description ?? 'Не указано' }}
                            </p>

                            <p>
                                <strong>Состав:</strong>
                                {{ $product->composition ?? 'Не указан' }}
                            </p>

                            <p>
                                <strong>Количество:</strong>
                                {{ $product->totalAvailableQuantity }} пар
                            </p>

                            @if (!$product->hadPreviousMonthOrder)
                                <div class="old-price">
                                    {{ number_format($product->price, 2, ',', ' ') }} ₽
                                </div>

                                <span class="discount-badge">-25%</span>
                            @endif

                            <div class="product-price mb-3">
                                {{ number_format($product->catalogPrice, 2, ',', ' ') }} ₽
                            </div>

                            @if ($product->totalAvailableQuantity > 0)

                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="stock_item_id" class="form-label">
                                            Размер
                                        </label>

                                        <select
                                            name="stock_item_id"
                                            id="stock_item_id"
                                            class="form-select"
                                            required
                                        >
                                            <option value="">Выберите размер</option>

                                            @foreach ($product->stockItems as $stockItem)
                                                @if ($stockItem->available_quantity > 0)
                                                    <option value="{{ $stockItem->stock_item_id }}">
                                                        {{ $stockItem->size->value }}
                                                        — {{ $stockItem->available_quantity }} пар
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">
                                            Количество
                                        </label>

                                        <input
                                            type="number"
                                            name="quantity"
                                            id="quantity"
                                            class="form-control"
                                            min="1"
                                            value="1"
                                            required
                                        >
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary">
                                        Добавить в корзину
                                    </button>

                                </form>

                            @else

                                <div class="alert alert-warning">
                                    Товар закончился.
                                </div>

                            @endif

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
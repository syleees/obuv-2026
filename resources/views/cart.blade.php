@extends('layouts.app')

@section('title', 'Корзина — Чудо Обувь')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="catalog-title mb-0">Корзина</h1>

                <a href="{{ route('catalog') }}" class="btn btn-outline-secondary">
                    ← Назад в каталог
                </a>
            </div>

            @if ($items->isEmpty())

                <div class="alert alert-info">
                    Корзина пуста. Выберите товары в каталоге.
                </div>

                <a href="{{ route('catalog') }}" class="btn btn-outline-primary">
                    Открыть каталог
                </a>

            @else

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">

                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Размер</th>
                                <th>Цена</th>
                                <th>Доступно</th>
                                <th>Количество</th>
                                <th>Сумма</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product->manufacturer->name ?? '' }}
                                        {{ $item->product->name }}
                                    </td>

                                    <td>{{ $item->size->value }}</td>

                                    <td>
                                        {{ number_format($item->catalogPrice, 2, ',', ' ') }} ₽
                                    </td>

                                    <td>{{ $item->available_quantity }} шт.</td>

                                    <td>
                                        <form
                                            action="{{ route('cart.update', $item) }}"
                                            method="POST"
                                            class="d-flex gap-2"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="number"
                                                name="quantity"
                                                min="1"
                                                max="{{ $item->available_quantity }}"
                                                value="{{ $item->cartQuantity }}"
                                                class="form-control"
                                                required
                                            >

                                            <button
                                                class="btn btn-outline-primary"
                                                type="submit"
                                            >
                                                Изменить
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        {{ number_format($item->lineTotal, 2, ',', ' ') }} ₽
                                    </td>

                                    <td>
                                        <form
                                            action="{{ route('cart.destroy', $item) }}"
                                            method="POST"
                                            onsubmit="return confirm('Удалить позицию из корзины?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger"
                                            >
                                                Удалить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Итого:</th>
                                <th colspan="2">
                                    {{ number_format($total, 2, ',', ' ') }} ₽
                                </th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <div class="d-flex justify-content-between gap-2">

                    <form
                        action="{{ route('cart.clear') }}"
                        method="POST"
                        onsubmit="return confirm('Отказаться от заказа и очистить корзину?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-danger">
                            Отказаться от заказа
                        </button>
                    </form>

                    <form
                        action="{{ route('cart.checkout') }}"
                        method="POST"
                        onsubmit="return confirm('Подтвердить оформление заказа?')"
                    >
                        @csrf

                        <button type="submit" class="btn btn-success">
                            Подтвердить заказ
                        </button>
                    </form>

                </div>

            @endif

        </div>
    </div>
</div>

@endsection
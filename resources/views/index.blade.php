@extends('layouts.app')

@section('title', 'Каталог обуви — Чудо Обувь')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="d-flex flex-md-row justify-content-between align-items-md-end mb-4">
                <div>
                    <h1 class="catalog-title mb-1">Каталог обуви</h1>
                    <p class="text-muted mb-0">Качественная обувь для всей семьи</p>
                </div>

                <small class="text-muted">
                    Расчёт цен на {{ $calculationDate->format('d.m.Y') }}
                </small>
            </div>

            @auth
            <form
                id="catalog-filter"
                method="GET"
                action="{{ route('catalog') }}"
                class="p-3 mb-4 product-card"
            >
                <div class="row g-2">

                    <div class="col-lg-4">
                        <label for="search" class="form-label">
                            Поиск по названию и описанию
                        </label>

                        <input
                            id="search"
                            name="search"
                            type="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            maxlength="255"
                            placeholder="Начните вводить текст..."
                        >
                    </div>

                    <div class="col-lg-3">
                        <label for="category" class="form-label">
                            Категория
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="form-select"
                        >
                            <option value="">Все категории</option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->category_id }}"
                                    @selected(request('category') == $category->category_id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <label for="sort" class="form-label">
                            Сортировка цены
                        </label>

                        <select id="sort" name="sort" class="form-select">
                            <option value="">По умолчанию</option>

                            <option
                                value="price_asc"
                                @selected(request('sort') == 'price_asc')
                            >
                                Сначала дешевле
                            </option>

                            <option
                                value="price_desc"
                                @selected(request('sort') == 'price_desc')
                            >
                                Сначала дороже
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-2 d-flex align-items-end">
                        <a
                            href="{{ route('catalog') }}"
                            class="btn btn-outline-secondary w-100"
                        >
                            Сброс
                        </a>
                    </div>

                </div>
            </form>
            @endauth

            @forelse ($products as $product)

                @php
                    $rel = 'assets/images/catalog/' . $product->image_path;

                    $imageUrl = asset(
                        file_exists(public_path($rel))
                            ? $rel
                            : 'assets/images/picture.png'
                    );

                    $isLowStock = $product->totalAvailableQuantity <= 3;
                @endphp

                <article class="product-card {{ $isLowStock ? 'product-card-low' : '' }} mb-3">

                    <div class="row g-0">

                        <div class="col-md-3 d-flex align-items-center">
                            <img
                                src="{{ $imageUrl }}"
                                class="product-image"
                                alt="{{ $product->name }}"
                            >
                        </div>

                        <div class="col-md-9">
                            <div class="product-content">

                                <h2 class="product-heading">
                                    {{ $product->manufacturer->name ?? 'Производитель не указан' }}
                                    |
                                    {{ $product->name }}
                                </h2>

                                <div class="row align-items-center">

                                    <div class="col-7">
                                        <span class="product-label">Категория:</span>
                                        {{ $product->category->name ?? 'Не указана' }}
                                    </div>

                                    <div class="col-5 text-end">

                                        @if (!$product->hadPreviousMonthOrder)
                                            <div class="old-price">
                                                {{ number_format($product->price, 2, ',', ' ') }} ₽
                                            </div>

                                            <span class="discount-badge">-25%</span>
                                        @endif

                                        <div class="product-price {{ $isLowStock ? 'product-price-low' : '' }}">
                                            {{ number_format($product->catalogPrice, 2, ',', ' ') }} ₽
                                        </div>

                                    </div>

                                </div>

                                <div>
                                    <span class="product-label">Количество:</span>
                                    {{ $product->totalAvailableQuantity > 5 ? 'много' : 'мало' }}
                                </div>

                                <div>
                                    <span class="product-label">Состав:</span>
                                    {{ $product->composition ?? 'Не указан' }}
                                </div>

                                <a
                                    href="{{ route('catalog.show', $product) }}"
                                    class="btn btn-outline-primary mt-3"
                                >
                                    Подробнее
                                </a>

                            </div>
                        </div>

                    </div>

                </article>

            @empty

                <div class="alert alert-info">
                    Товары не найдены.
                </div>

            @endforelse

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('catalog-filter');

        if (!form) {
            return;
        }

        const search = form.querySelector('#search');
        const category = form.querySelector('#category');
        const sort = form.querySelector('#sort');

        let timer;

        function submitForm() {
            form.requestSubmit();
        }

        search.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(submitForm, 500);
        });

        category.addEventListener('change', submitForm);
        sort.addEventListener('change', submitForm);
    });
</script>
@endpush

@endsection
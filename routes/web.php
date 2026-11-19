<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\HomeController;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');

Route::middleware('auth')->group(function () {

    Route::get('/catalog/{product}', [CatalogController::class, 'show'])
        ->name('catalog.show');

    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart', [CartController::class, 'store'])
        ->name('cart.store');

    Route::patch('/cart/{stockItem}', [CartController::class, 'update'])
        ->name('cart.update');

    Route::delete('/cart/{stockItem}', [CartController::class, 'destroy'])
        ->name('cart.destroy');

    Route::delete('/cart', [CartController::class, 'clear'])
        ->name('cart.clear');

    Route::post('/cart/checkout', [CartController::class, 'checkout'])
        ->name('cart.checkout');
        Route::middleware('auth')->prefix('home')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/orders/create', [OrderManagementController::class, 'create'])->name('home.orders.create');
    Route::post('/orders', [OrderManagementController::class, 'store'])->name('home.orders.store');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('home.orders.show');
    Route::delete('/orders/{order}', [OrderManagementController::class, 'destroy'])->name('home.orders.destroy');

    Route::get('/products/{product}/select', [OrderManagementController::class, 'selectProduct'])->name('home.orders.select');
    Route::post('/draft/add', [OrderManagementController::class, 'addDraftItem'])->name('home.orders.draft.add');
    Route::delete('/draft/{stockItem}', [OrderManagementController::class, 'removeDraftItem'])->name('home.orders.draft.remove');

    Route::get('/orders/{order}/edit', [OrderManagementController::class, 'edit'])->name('home.orders.edit');
    Route::put('/orders/{order}', [OrderManagementController::class, 'update'])->name('home.orders.update');
    Route::delete('/orders/{order}/items/{item}', [OrderManagementController::class, 'destroyItem'])->name('home.orders.item.destroy');
});
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');




<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cartDetails(Request $request)
    {
        $cart = $request->session()
            ->get('cart', []);

        $items = StockItem::with([
            'product.manufacturer',
            'size'
        ])
            ->whereIn('stock_item_id', array_keys($cart))
            ->get()
            ->map(function ($stockItem) use ($cart) {

                $stockItem->cartQuantity =
                    (int) $cart[$stockItem->stock_item_id];

                $stockItem->catalogPrice =
                    $this->calculateCatalogPrice(
                        $stockItem->product,
                        $this->hadPreviousMonthOrder(
                            $stockItem->product
                        )
                    );

                $stockItem->lineTotal =
                    $stockItem->catalogPrice *
                    $stockItem->cartQuantity;

                return $stockItem;
            });

        return [
            $items,
            (float) $items->sum('lineTotal')
        ];
    }

    public function index(Request $request)
    {
        [$items, $total] = $this->cartDetails($request);

        return view('cart', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => [
                'required',
                'integer',
                'exists:stock_items,stock_item_id'
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        $stockItem = StockItem::with([
            'product',
            'size'
        ])
            ->findOrFail($validated['stock_item_id']);

        $cart = $request->session()
            ->get('cart', []);

        $newQuantity =
            ($cart[$stockItem->stock_item_id] ?? 0)
            + (int) $validated['quantity'];

        if ($newQuantity > $stockItem->available_quantity) {
            return back()->withInput()
                ->with(
                    'error',
                    'Нельзя добавить указанное количество. Доступно: '
                    . $stockItem->available_quantity . '.'
                );
        }

        $cart[$stockItem->stock_item_id] = $newQuantity;

        $request->session()
            ->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Товар «' . $stockItem->product->name .
                '», размер ' . $stockItem->size->value .
                ', добавлен в корзину.'
            );
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $cart = $request->session()
            ->get('cart', []);

        if ($validated['quantity'] > $stockItem->available_quantity) {
            return back()->with(
                'error',
                'Недостаточно товара. Доступно: '
                . $stockItem->available_quantity . '.'
            );
        }

        $cart[$stockItem->stock_item_id] =
            (int) $validated['quantity'];

        $request->session()
            ->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Количество обновлено.');
    }

    public function destroy(Request $request, StockItem $stockItem)
    {
        $cart = $request->session()
            ->get('cart', []);

        unset($cart[$stockItem->stock_item_id]);

        $request->session()
            ->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Позиция удалена из корзины.');
    }

    public function clear(Request $request)
    {
        $request->session()
            ->forget('cart');

        return redirect()
            ->route('catalog')
            ->with(
                'info',
                'Вы отказались от заказа. Корзина очищена.'
            );
    }
}
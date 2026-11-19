<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    private const DRAFT_KEY = 'home_order_draft';

    private function loadOrderWithTotal(Order $order)
    {
        $order->load([
            'user',
            'items.stockItem.product.manufacturer',
            'items.stockItem.size',
        ]);

        $order->totalSum = (float) $order->items->sum(function ($item) {
            return $item->quantity * (float) $item->unit_price;
        });

        return $order;
    }

    private function draftDetails(Request $request)
    {
        $draft = $request->session()->get(self::DRAFT_KEY, []);

        if (empty($draft)) {
            return [collect(), 0.0];
        }

        $items = StockItem::with(['product.manufacturer', 'size'])
            ->whereIn('stock_item_id', array_keys($draft))
            ->get()
            ->map(function ($stockItem) use ($draft) {
                $stockItem->draftQuantity = (int) $draft[$stockItem->stock_item_id];
                $stockItem->unitPrice = (float) $stockItem->product->price;
                $stockItem->lineTotal = $stockItem->unitPrice * $stockItem->draftQuantity;
                return $stockItem;
            });

        $total = (float) $items->sum(fn ($i) => $i->lineTotal);

        return [$items, $total];
    }

    public function show(Order $order)
    {
        $this->loadOrderWithTotal($order);
        return view('orders.show', compact('order'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $search = $validated['search'] ?? null;

        $products = Product::with(['manufacturer', 'category'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->get();

        [$draftItems, $draftTotal] = $this->draftDetails($request);

        $clients = User::where('role_id', User::ROLE_CLIENT)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('orders.create', compact(
            'products', 'search', 'draftItems', 'draftTotal', 'clients'
        ));
    }

    public function selectProduct(Product $product)
    {
        $product->load(['manufacturer', 'category', 'stockItems.size']);
        return view('orders.select', compact('product'));
    }

    public function addDraftItem(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => ['required', 'integer', 'exists:stock_items,stock_item_id'],
            'quantity'      => ['required', 'integer', 'min:1'],
        ]);

        $stockItem = StockItem::with(['product', 'size'])
            ->findOrFail($validated['stock_item_id']);

        $draft = $request->session()->get(self::DRAFT_KEY, []);

        $newQuantity = ($draft[$stockItem->stock_item_id] ?? 0) + (int) $validated['quantity'];

        if ($newQuantity > $stockItem->available_quantity) {
            return back()->with(
                'error',
                'Нельзя добавить указанное количество. Доступно: '
                . $stockItem->available_quantity . '.'
            );
        }

        $draft[$stockItem->stock_item_id] = $newQuantity;
        $request->session()->put(self::DRAFT_KEY, $draft);

        return redirect()->route('home.orders.create')->with(
            'success',
            'Товар ' . $stockItem->product->name
            . ', размер ' . $stockItem->size->value
            . ', добавлен в заказ.'
        );
    }

    public function removeDraftItem(Request $request, StockItem $stockItem)
    {
        $draft = $request->session()->get(self::DRAFT_KEY, []);
        unset($draft[$stockItem->stock_item_id]);
        $request->session()->put(self::DRAFT_KEY, $draft);

        return redirect()->route('home.orders.create')
            ->with('success', 'Позиция удалена из заказа.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => ['required', 'integer', 'exists:users,user_id'],
            'order_date' => ['required', 'date'],
        ]);

        [$draftItems] = $this->draftDetails($request);

        if ($draftItems->isEmpty()) {
            return back()->with('error', 'Нельзя создать пустой заказ. Добавьте хотя бы один товар.');
        }

        $order = Order::create([
            'user_id'    => $validated['user_id'],
            'order_date' => $validated['order_date'],
        ]);

        foreach ($draftItems as $item) {
            OrderItem::create([
                'order_id'      => $order->order_id,
                'stock_item_id' => $item->stock_item_id,
                'quantity'      => $item->draftQuantity,
                'unit_price'    => $item->unitPrice,
            ]);
        }

        $request->session()->forget(self::DRAFT_KEY);

        return redirect()->route('home')
            ->with('success', 'Заказ №' . $order->order_id . ' создан.');
    }

    public function destroy(Order $order)
    {
        $orderId = $order->order_id;
        $order->items()->delete();
        $order->delete();

        return redirect()->route('home')
            ->with('success', 'Заказ №' . $orderId . ' удалён.');
    }

    public function edit(Order $order)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $this->loadOrderWithTotal($order);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $validated = $request->validate([
            'order_date' => ['required', 'date'],
        ]);
        $order->update(['order_date' => $validated['order_date']]);
        return redirect()->route('home.orders.edit', $order)
            ->with('success', 'Дата заказа обновлена.');
    }

    public function destroyItem(Order $order, OrderItem $item)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $item->delete();
        return redirect()->route('home.orders.edit', $order)
            ->with('success', 'Товарная позиция удалена из заказа.');
    }
}
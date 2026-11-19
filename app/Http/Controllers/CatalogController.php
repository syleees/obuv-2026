<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => [
                'nullable',
                'integer',
                'exists:categories,category_id'
            ],
            'sort' => ['nullable', 'in:price_asc,price_desc'],
        ]);

        $calculationDate = Carbon::today();

        [$previousMonthStart, $previousMonthEnd] =
            $this->previousMonthRange($calculationDate);

        $products = Product::query()
            ->with(['category', 'manufacturer'])
            ->withSum(
                'stockItems as totalAvailableQuantity',
                'available_quantity'
            )
            ->withExists([
                'stockItems as hadPreviousMonthOrder' =>
                    function ($query) use (
                        $previousMonthStart,
                        $previousMonthEnd
                    ) {
                        $query->whereHas(
                            'orderItems.order',
                            function ($orderQuery) use (
                                $previousMonthStart,
                                $previousMonthEnd
                            ) {
                                $orderQuery->whereBetween(
                                    'order_date',
                                    [
                                        $previousMonthStart->toDateString(),
                                        $previousMonthEnd->toDateString()
                                    ]
                                );
                            }
                        );
                    },
            ])

            ->when(
                $validated['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($nested) use ($search) {
                        $nested->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'description',
                            'like',
                            '%' . $search . '%'
                        );
                    });
                }
            )

            ->when(
                $validated['category'] ?? null,
                function ($query, $category) {
                    $query->where('category_id', $category);
                }
            )

            ->orderBy('product_id')
            ->get();

        $products->each(function ($product) {
            $product->totalAvailableQuantity =
                (int) ($product->totalAvailableQuantity ?? 0);

            $product->hadPreviousMonthOrder =
                (bool) $product->hadPreviousMonthOrder;

            $product->catalogPrice = $this->calculateCatalogPrice(
                $product,
                $product->hadPreviousMonthOrder
            );
        });

        if (($validated['sort'] ?? null) == 'price_asc') {
            $products = $products->sortBy('catalogPrice')
                ->values();
        } elseif (($validated['sort'] ?? null) == 'price_desc') {
            $products = $products->sortByDesc('catalogPrice')
                ->values();
        }

        $categories = Category::orderBy('name')->get();

        return view(
            'index',
            compact('products', 'categories', 'calculationDate')
        );
    }

    public function show(Product $product)
    {
        $calculationDate = Carbon::today();

        [$previousMonthStart, $previousMonthEnd] =
            $this->previousMonthRange($calculationDate);

        $product->load([
            'category',
            'manufacturer',
            'stockItems' => function ($query) {
                $query->with('size')
                    ->orderBy('size_id');
            },
        ]);

        $product->loadExists([
            'stockItems as hadPreviousMonthOrder' =>
                function ($query) use (
                    $previousMonthStart,
                    $previousMonthEnd
                ) {
                    $query->whereHas(
                        'orderItems.order',
                        function ($orderQuery) use (
                            $previousMonthStart,
                            $previousMonthEnd
                        ) {
                            $orderQuery->whereBetween(
                                'order_date',
                                [
                                    $previousMonthStart->toDateString(),
                                    $previousMonthEnd->toDateString()
                                ]
                            );
                        }
                    );
                },
        ]);

        $product->totalAvailableQuantity =
            (int) $product->stockItems->sum('available_quantity');

        $product->hadPreviousMonthOrder =
            (bool) $product->hadPreviousMonthOrder;

        $product->catalogPrice = $this->calculateCatalogPrice(
            $product,
            $product->hadPreviousMonthOrder
        );

        return view(
            'product',
            compact('product', 'calculationDate')
        );
    }

    protected function previousMonthRange(Carbon $date)
    {
        $start = $date->copy()
            ->subMonthNoOverflow()
            ->startOfMonth();

        $end = $start->copy()
            ->endOfMonth();

        return [$start, $end];
    }

    protected function calculateCatalogPrice(
        Product $product,
        $hadPreviousMonthOrder
    ) {
        return $hadPreviousMonthOrder
            ? (float) $product->price
            : round((float) $product->price * 0.75, 2);
    }
}
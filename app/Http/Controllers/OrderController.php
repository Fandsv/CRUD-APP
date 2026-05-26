<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\Product;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Order::with('products')->latest()->get(), 200);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        // тут считаю total_price на сервере через цены товаров
        $syncData = [];
        $total = 0;

        foreach ($data['items'] as $item) {
            $productId = $item['id'] ?? null;
            $quantity  = (int)($item['quantity'] ?? 1);

            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $total += $product->price * $quantity;
                    $syncData[$productId] = ['quantity' => $quantity];
                }
            }
        }

        $order = Order::create([
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'total_price'    => $total,
        ]);

        $order->products()->sync($syncData);
        broadcast(new \App\Events\OrderCreated($order))->toOthers();
        return response()->json(['message' => 'Заказ создан'], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $syncData = [];
        $total = 0;

        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $productId = $item['id'] ?? null;
                $quantity  = (int)($item['pivot']['quantity'] ?? $item['quantity'] ?? 1);

                if ($productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $total += $product->price * $quantity;
                        $syncData[$productId] = ['quantity' => $quantity];
                    }
                }
            }
        }

        $order->update([
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'total_price'    => $total,
        ]);

        $order->products()->sync($syncData);

        return response()->json(['message' => 'Заказ обновлен'], 200);
    }

    public function destroy($id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $order->products()->detach();
        $order->delete();

        return response()->json(['message' => 'Заказ удален'], 200);
    }
}

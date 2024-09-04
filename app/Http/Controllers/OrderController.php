<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with(['orderItems.menu'])->get();
        return OrderResource::collection($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required',
            'customer_name' => 'required|max:255',
            'menu_items' => 'required|array',
            'menu_items.*.menu_id' => 'required|exists:menus,id',
            'menu_items.*.quantity' => 'required|integer|min:1'
        ]);

        $order = Order::create([
            'table_id' => $validated['table_id'],
            'customer_name' => $validated['customer_name']
        ]);

        foreach ($validated['menu_items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $totalPrice = $menu->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'total_price' => $totalPrice
            ]);
        }

        Table::where('id', $order->table_id)
            ->update([
                'status_id' => 3
            ]);

        Cart::where('table_id', $order->table_id)
            ->delete();

        return new OrderResource($order->loadMissing('orderItems'));
    }

    public function show(Order $order)
    {
        $order->loadMissing('orderItems.menu');
        return new OrderResource($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'menu_items' => 'required|array',
            'menu_items.*.menu_id' => 'required|exists:menus,id',
            'menu_items.*.quantity' => 'required|integer|min:1'
        ]);

        // $existingMenuIds = $order->orderItems->pluck('menu_id')->toArray();
        $newMenuIds = array_column($validated['menu_items'], 'menu_id');

        OrderItem::where('order_id', $order->id)
            ->whereNotIn('menu_id', $newMenuIds)
            ->delete();

        foreach ($validated['menu_items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $totalPrice = $menu->price * $item['quantity'];

            OrderItem::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id']

                ],
                [
                    'quantity' => $item['quantity'],
                    'total_price' => $totalPrice
                ]
            );
        }

        return new OrderResource($order->loadMissing('orderItems.menu'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return new OrderResource($order->loadMissing('orderItems.menu'));
    }
}

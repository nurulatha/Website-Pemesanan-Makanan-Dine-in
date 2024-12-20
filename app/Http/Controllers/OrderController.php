<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with(['orderItems.menu'])->get();
        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|exists:tables,url',
            'customer_name' => 'required|max:255',
            'customer_phone' => 'required|max:15',
            'menu_items' => 'required|array',
            'menu_items.*.menu_id' => 'required|exists:menus,id',
            'menu_items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tableId = Table::where('url', $request->url)->first()->id;

        try {

            $order = Order::create([
                'table_id' => $tableId,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
            ]);

            foreach ($request->menu_items as $item) {
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
                    'table_status_id' => 3
                ]);


            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' =>  new OrderResource($order->loadMissing('orderItems')),
            ], 201);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $order->loadMissing('orderItems.menu');
        return new OrderResource($order);
    }


    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'menu_items' => 'required|array',
            'menu_items.*.menu_id' => 'required|exists:menus,id',
            'menu_items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // $existingMenuIds = $order->orderItems->pluck('menu_id')->toArray();
        $newMenuIds = array_column($request->menu_items, 'menu_id');

        try {

            OrderItem::where('order_id', $order->id)
                ->whereNotIn('menu_id', $newMenuIds)
                ->delete();

            foreach ($request->menu_items as $item) {
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

            return response()->json([
                'status' => true,
                'message' => 'Order updated successfully',
                'data' =>  new OrderResource($order->loadMissing('orderItems.menu')),
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function destroy(Order $order)
    {
        if (!$order->exists) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        try {

            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully',
                'data' => new OrderResource($order->loadMissing('orderItems.menu')),
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}

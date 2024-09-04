<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function index(Request $request)
    {
        $tableId = $request->query('table_id');

        if ($tableId) {
            $carts = Cart::where('table_id', $tableId)->with('menu.category')->get();
        } else {
            $carts = Cart::with('menu.category')->get();
        }
        return response()->json(['carts' => CartResource::collection($carts)]);
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
            'table_id' => 'required|exists:tables,id',
            'id' => 'required|exists:menus,id',
            'quantity' => 'required'
        ]);

        $menu = Menu::findOrFail($validated['id']);

        $totalPrice = $menu->price * $validated['quantity'];

        $cart = Cart::create([
            'table_id' => $validated['table_id'],
            'menu_id' => $validated['id'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
        ]);

        return new CartResource($cart->loadMissing('menu.category'));
    }

    public function show(Cart $cart)
    {
        return response()->json(['cart' => new CartResource($cart)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    public function update(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'quantity' => 'required'
        ]);


        $menu = Menu::findOrFail($cart->menu_id);

        $totalPrice = $menu->price * $validated['quantity'];

        $cart->update([
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice
        ]);

        return new CartResource($cart->loadMissing('menu.category'));
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return new CartResource($cart->loadMissing('menu.category'));
    }
}

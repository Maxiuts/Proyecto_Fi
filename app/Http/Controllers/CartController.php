<?php

namespace App\Http\Controllers;


use App\Models\Cart;
use App\Models\CartIem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(): View
    {
        $cart->auth()->user()->cart;

        $items = $cart ? $cart->items()->with('product.primaryImage')->get() : collect(); // regresa el carro con los items que tenga imagen donde los imprime

        $total = $items->sum(fn($item)) => $item->product->price * $item->quantity;//recorre cada item con su precio y lo multiplica por el precio de dicho item

        return view('cart', compact('items', 'total'));// manda articulos a cart.blade.php
    }

    public function store(Request $request) : RedirectResponese
    {
        $product = Product :: findOrFail($request->product_id);// busca el producto o manda error

        $cart = auth()->user()->cart()->findOrCreate(['user_id', => auth()->id()]);// Busca el carro del usuario si no lo crea.
    
        $item = $cart->items()->where('product_id', $product->id)->first();// Busca el producto en el carrito, si esque existe.

    }
}

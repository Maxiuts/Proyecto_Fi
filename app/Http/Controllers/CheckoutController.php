<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Mostrar formulario de checkout con resumen del carrito.
     */
    public function index()
    {
        $cart = auth()->user()->cart;

        $items = $cart
            ? $cart->items()
                ->with('product')
                ->get()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                    ],
                ])
            : collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        $total = $items->sum(fn ($item) => $item['product']['price'] * $item['quantity']);

        return Inertia::render('Checkout', [
            'items' => $items,
            'total' => (float) $total,
        ]);
    }

    /**
     * Procesar el pedido y crear la orden.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
        ]);

        return DB::transaction(function () use ($validated) {
            $user = auth()->user();
            $cart = $user->cart;

            if (! $cart || $cart->items()->count() === 0) {
                return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
            }

            // Calcular total
            $items = $cart->items()->with('product')->get();
            $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

            // Crear orden
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'street' => $validated['street'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'status' => 'pending',
            ]);

            // Mover items del carrito a order_items
            foreach ($items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
            }

            // Vaciar carrito
            $cart->items()->delete();

            return redirect()
                ->route('shop')
                ->with('success', '¡Pedido realizado exitosamente! Tu número de orden es #'.$order->id);
        });
    }
}

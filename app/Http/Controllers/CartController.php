<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Mostrar el carrito del usuario autenticado.
     */
    public function index()
    {
        $cart = auth()->user()->cart;

        $items = $cart
            ? $cart->items()
                ->with('product.primaryImage')
                ->get()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'primary_image' => $item->product->primaryImage
                            ? [
                                'url' => Storage::disk($item->product->primaryImage->disk)
                                    ->url($item->product->primaryImage->path),
                            ]
                            : null,
                    ],
                ])
            : collect();

        $total = $items->sum(fn ($item) => $item['product']['price'] * $item['quantity']);

        return Inertia::render('Cart', [
            'items' => $items,
            'total' => (float) $total,
        ]);
    }

    /**
     * Agregar o actualizar un producto en el carrito.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Obtener o crear el carrito del usuario
        $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);

        // Buscar si el producto ya existe en el carrito
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $validated['quantity'] ?? 1);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'] ?? 1,
            ]);
        }

        return redirect()->route('cart.index');
    }

    /**
     * Actualizar la cantidad de un artículo en el carrito.
     */
    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $validated['quantity']]);

        return redirect()->route('cart.index');
    }

    /**
     * Eliminar un artículo del carrito.
     */
    public function destroy(CartItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('cart.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function shop(): View
    {
        $products = Product::query()
            ->with('primaryImage')
            ->latest()
            ->get();

        return view('shop', ['products' => $products]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('primaryImage')
            ->latest()
            ->get();
        $editingProduct = null;

        if ($request->filled('edit')) {
            $editingProduct = Product::query()
                ->with('primaryImage')
                ->findOrFail($request->integer('edit'));
        }

        return view('dashboard', [
            'products' => $products,
            'editingProduct' => $editingProduct,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated): void {
            $product = Product::query()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
            ]);

            $this->syncProductImage($product, $request->file('image'));
        });

        return redirect()
            ->route('dashboard')
            ->with('status', 'Producto registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $product, $validated): void {
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
            ]);

            $this->syncProductImage($product, $request->file('image'));
        });

        return redirect()
            ->route('dashboard')
            ->with('status', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->load('images');

        foreach ($product->images as $image) {
            Storage::disk($image->disk)->delete($image->path);
        }

        $product->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Producto eliminado correctamente.');
    }

    private function syncProductImage(Product $product, ?UploadedFile $image): void
    {
        if (! $image instanceof UploadedFile) {
            return;
        }

        $existingImage = $product->primaryImage;

        if ($existingImage instanceof ProductImage) {
            Storage::disk($existingImage->disk)->delete($existingImage->path);
            $existingImage->delete();
        }

        $path = Storage::disk('public')->putFile('products', $image);

        if ($path === false) {
            throw new \RuntimeException('No se pudo guardar la imagen. Verifica los permisos del directorio de storage.');
        }

        $product->images()->create([
            'path' => $path,
            'disk' => 'public',
        ]);
    }
}

<?php

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores a product with an image from the dashboard', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Laptop gamer',
            'description' => 'Equipo con envio incluido',
            'price' => '24999.90',
            'stock' => 3,
            'image' => UploadedFile::fake()->image('laptop.jpg'),
        ]);

    $response
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('status', 'Producto registrado correctamente.');

    $product = Product::query()->first();

    expect($product)->not->toBeNull();

    expect(ProductImage::query()->count())->toBe(1);

    $image = ProductImage::query()->first();

    expect($image)->not->toBeNull();
    expect($image->product_id)->toBe($product->id);

    Storage::disk('public')->assertExists($image->path);
});

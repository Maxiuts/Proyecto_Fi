<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

Route::middleware(['auth', 'verified'])->group(function () {//ruta para el carro
    Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
    Route::get('/cart',[CartController::class, 'index'])->name('cart.index');
    Route::post('/cart',[CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{item}',[CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}',[CartController::class, 'destroy'])->name('cart.destroy');
});

// Rutas para autenticación social con Google y GitHub
Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

// Route para manejar el callback de autenticación social
Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::get('/', function () {
    return view('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureAdmin::class])->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda</title>
    @php use Illuminate\Support\Facades\Storage; @endphp
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-black text-white min-h-screen">

    {{-- Navbar --}}
    <nav class="fixed top-0 inset-x-0 z-10 border-b border-white/10 bg-black/60 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
            <span class="text-lg font-semibold tracking-wide">Tienda</span>
            <div class="flex items-center gap-4">
                @auth
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}"
                       class="text-sm text-gray-400 hover:text-white transition">
                        Administrar
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-gray-400 hover:text-white transition">
                            Salir
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 pt-24 pb-16">

        {{-- Header --}}
        <div class="mb-10">
            <h1 class="text-3xl font-bold mb-1">Productos</h1>
            <p class="text-gray-400 text-sm">{{ $products->count() }} producto{{ $products->count() !== 1 ? 's' : '' }} disponible{{ $products->count() !== 1 ? 's' : '' }}</p>
        </div>

        @if ($products->isEmpty())
            <div class="flex flex-col items-center justify-center py-32 text-gray-500">
                <svg class="w-12 h-12 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                <p class="text-sm">No hay productos disponibles.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="group flex flex-col rounded-xl border border-white/10 bg-white/5
                                hover:border-indigo-500/50 hover:bg-white/8 transition-all duration-200">

                        {{-- Imagen --}}
                        <div class="aspect-square overflow-hidden rounded-t-xl bg-white/5">
                            @if ($product->primaryImage)
                                <img src="{{ Storage::disk($product->primaryImage->disk)->url($product->primaryImage->path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-600">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-col flex-1 p-4 gap-2">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="font-medium text-sm leading-snug line-clamp-2">{{ $product->name }}</h2>
                                @if ($product->stock > 0)
                                    <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                        En stock
                                    </span>
                                @else
                                    <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-red-500/15 text-red-400 border border-red-500/20">
                                        Agotado
                                    </span>
                                @endif
                            </div>

                            @if ($product->description)
                                <p class="text-xs text-gray-400 line-clamp-2">{{ $product->description }}</p>
                            @endif

                            <div class="mt-auto pt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-indigo-400">
                                    ${{ number_format($product->price, 2) }}
                                </span>
                                <button
                                    @if ($product->stock === 0) disabled @endif
                                    class="text-xs px-3 py-1.5 rounded-lg font-medium transition
                                           {{ $product->stock > 0
                                               ? 'bg-indigo-600 hover:bg-indigo-700 text-white'
                                               : 'bg-white/5 text-gray-500 cursor-not-allowed' }}">
                                    Agregar
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </main>

</body>
</html>

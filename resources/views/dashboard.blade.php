<x-app-layout>
    @php
        $isEditing = isset($editingProduct) && $editingProduct !== null;
        $formAction = $isEditing ? route('products.update', $editingProduct) : route('products.store');
        $currentImage = $editingProduct?->primaryImage;
    @endphp

    <div class="min-h-screen bg-gray-100 py-12 dark:bg-gray-900">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    Gestión de productos
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Registra, edita y elimina productos desde tu panel.
                </p>
            </div>

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[460px_minmax(0,1fr)]">
                <div class="rounded-3xl bg-white p-7 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $isEditing ? 'Editar producto' : 'Registrar producto' }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $isEditing ? 'Actualiza la información y reemplaza la imagen si lo necesitas.' : 'Completa los datos y agrega una imagen principal.' }}
                            </p>
                        </div>

                        @if ($isEditing)
                            <a
                                href="{{ route('dashboard') }}"
                                class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                            >
                                Cancelar
                            </a>
                        @endif
                    </div>

                    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-5">
                        @csrf

                        @if ($isEditing)
                            @method('PUT')
                        @endif

                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nombre
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $editingProduct->name ?? '') }}"
                                class="block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:ring-indigo-900"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Descripción
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:ring-indigo-900"
                            >{{ old('description', $editingProduct->description ?? '') }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Precio
                                </label>
                                <input
                                    id="price"
                                    name="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('price', $editingProduct->price ?? '') }}"
                                    class="block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:ring-indigo-900"
                                    required
                                >
                            </div>

                            <div class="space-y-2">
                                <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock
                                </label>
                                <input
                                    id="stock"
                                    name="stock"
                                    type="number"
                                    min="0"
                                    value="{{ old('stock', $editingProduct->stock ?? 0) }}"
                                    class="block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:ring-indigo-900"
                                    required
                                >
                            </div>
                        </div>

                        <div class="space-y-3 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/70">
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Imagen del producto
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Formatos JPG, PNG o WEBP. Tamaño máximo 2 MB.
                                </p>
                            </div>

                            <input
                                id="image"
                                name="image"
                                type="file"
                                accept="image/*"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                            >

                            @if ($currentImage)
                                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-950">
                                    <img
                                        src="{{ Storage::disk($currentImage->disk)->url($currentImage->path) }}"
                                        alt="Imagen actual de {{ $editingProduct->name }}"
                                        class="h-48 w-full object-cover"
                                    >
                                </div>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500"
                        >
                            {{ $isEditing ? 'Actualizar producto' : 'Guardar producto' }}
                        </button>
                    </form>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Productos registrados
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Administra el catálogo y revisa rápidamente su imagen principal.
                            </p>
                        </div>

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            {{ $products->count() }} total
                        </span>
                    </div>

                    @if ($products->isEmpty())
                        <div class="mt-6 rounded-2xl border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            Aún no hay productos registrados.
                        </div>
                    @else
                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400">
                                        <th class="pb-3 pr-4 font-medium">Imagen</th>
                                        <th class="pb-3 pr-4 font-medium">Nombre</th>
                                        <th class="pb-3 pr-4 font-medium">Precio</th>
                                        <th class="pb-3 pr-4 font-medium">Stock</th>
                                        <th class="pb-3 pr-4 font-medium">Descripción</th>
                                        <th class="pb-3 text-right font-medium">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($products as $product)
                                        <tr class="align-top text-gray-700 dark:text-gray-200">
                                            <td class="py-4 pr-4">
                                                @if ($product->primaryImage)
                                                    <img
                                                        src="{{ Storage::disk($product->primaryImage->disk)->url($product->primaryImage->path) }}"
                                                        alt="Imagen de {{ $product->name }}"
                                                        class="h-16 w-16 rounded-2xl object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                                                    >
                                                @else
                                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 text-xs text-gray-500 dark:bg-gray-700 dark:text-gray-300">
                                                        Sin imagen
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4 pr-4 font-medium">{{ $product->name }}</td>
                                            <td class="py-4 pr-4">${{ number_format((float) $product->price, 2) }}</td>
                                            <td class="py-4 pr-4">{{ $product->stock }}</td>
                                            <td class="py-4 pr-4 text-gray-500 dark:text-gray-400">
                                                {{ $product->description ?: 'Sin descripción' }}
                                            </td>
                                            <td class="py-4">
                                                <div class="flex justify-end gap-2">
                                                    <a
                                                        href="{{ route('dashboard', ['edit' => $product->id]) }}"
                                                        class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-950/40"
                                                    >
                                                        Editar
                                                    </a>

                                                    <form action="{{ route('products.destroy', $product) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950/40"
                                                            onclick="return confirm('¿Seguro que quieres eliminar este producto?')"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

import { Link, router } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import toast from 'react-hot-toast';
import { useState } from 'react';

export default function Shop({ products }) {
    const [addingProductId, setAddingProductId] = useState(null);

    const handleAddToCart = (productId, productName) => {
        setAddingProductId(productId);

        router.post('/cart', {
            product_id: productId,
            quantity: 1,
        }, {
            onSuccess: () => {
                toast.success(`"${productName}" agregado al carrito`, {
                    icon: '🛒',
                    duration: 3000,
                });
                setAddingProductId(null);
            },
            onError: (errors) => {
                toast.error('Error al agregar al carrito', {
                    duration: 3000,
                });
                setAddingProductId(null);
            },
        });
    };

    return (
        <AppLayout>
            <div className="mb-10">
                <h1 className="text-3xl font-bold mb-1">Productos</h1>
                <p className="text-gray-400 text-sm">
                    {products.length} producto{products.length !== 1 ? 's' : ''} disponible{products.length !== 1 ? 's' : ''}
                </p>
            </div>

            {products.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-32 text-gray-500">
                    <svg className="w-12 h-12 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <p className="text-sm">No hay productos disponibles.</p>
                </div>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {products.map(product => (
                        <div
                            key={product.id}
                            className="group flex flex-col rounded-xl border border-white/10 bg-white/5
                                        hover:border-indigo-500/50 hover:bg-white/8 transition-all duration-200"
                        >
                            {/* Imagen */}
                            <div className="aspect-square overflow-hidden rounded-t-xl bg-white/5">
                                {product.primary_image ? (
                                    <img
                                        src={product.primary_image.url}
                                        alt={product.name}
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center text-gray-600">
                                        <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                )}
                            </div>

                            {/* Info */}
                            <div className="flex flex-col flex-1 p-4 gap-2">
                                <div className="flex items-start justify-between gap-2">
                                    <h2 className="font-medium text-sm leading-snug line-clamp-2">{product.name}</h2>
                                    {product.stock > 0 ? (
                                        <span className="shrink-0 text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">
                                            En stock
                                        </span>
                                    ) : (
                                        <span className="shrink-0 text-xs px-2 py-0.5 rounded-full bg-red-500/15 text-red-400 border border-red-500/20">
                                            Agotado
                                        </span>
                                    )}
                                </div>

                                {product.description && (
                                    <p className="text-xs text-gray-400 line-clamp-2">{product.description}</p>
                                )}

                                <div className="mt-auto pt-3 flex items-center justify-between">
                                    <span className="text-lg font-bold text-indigo-400">
                                        ${parseFloat(product.price).toFixed(2)}
                                    </span>
                                    <button
                                        onClick={() => handleAddToCart(product.id, product.name)}
                                        disabled={product.stock === 0 || addingProductId === product.id}
                                        className={`text-xs px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1 ${
                                            product.stock > 0
                                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 disabled:cursor-not-allowed'
                                                : 'bg-white/5 text-gray-500 cursor-not-allowed'
                                        }`}
                                    >
                                        {addingProductId === product.id ? (
                                            <>
                                                <svg className="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Agregando...
                                            </>
                                        ) : (
                                            <>
                                                <span>Agregar</span>
                                                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                                </svg>
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </AppLayout>
    );
}

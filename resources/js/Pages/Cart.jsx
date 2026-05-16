import { Link, router } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';

export default function Cart({ items, total }) {
    const [cartItems, setCartItems] = useState(items);
    const [cartTotal, setCartTotal] = useState(total);
    const [processingItemId, setProcessingItemId] = useState(null);

    // Recalcular total cuando cambian los items
    useEffect(() => {
        const newTotal = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
        setCartTotal(newTotal);
    }, [cartItems]);

    const handleIncreaseQuantity = (itemId) => {
        const item = cartItems.find(i => i.id === itemId);
        setProcessingItemId(itemId);

        router.patch(`/cart/${itemId}`, {
            quantity: item.quantity + 1,
        }, {
            onSuccess: () => {
                setCartItems(prev => prev.map(i => 
                    i.id === itemId ? { ...i, quantity: i.quantity + 1 } : i
                ));
                setProcessingItemId(null);
            },
            onError: () => {
                toast.error('Error al actualizar cantidad');
                setProcessingItemId(null);
            }
        });
    };

    const handleDecreaseQuantity = (itemId) => {
        const item = cartItems.find(i => i.id === itemId);
        if (item.quantity > 1) {
            setProcessingItemId(itemId);

            router.patch(`/cart/${itemId}`, {
                quantity: item.quantity - 1,
            }, {
                onSuccess: () => {
                    setCartItems(prev => prev.map(i => 
                        i.id === itemId ? { ...i, quantity: i.quantity - 1 } : i
                    ));
                    setProcessingItemId(null);
                },
                onError: () => {
                    toast.error('Error al actualizar cantidad');
                    setProcessingItemId(null);
                }
            });
        }
    };

    const handleRemoveItem = (itemId) => {
        setProcessingItemId(itemId);

        router.delete(`/cart/${itemId}`, {
            onSuccess: () => {
                setCartItems(prev => prev.filter(item => item.id !== itemId));
                toast.success('Artículo eliminado del carrito', { icon: '🗑️' });
                setProcessingItemId(null);
            },
            onError: () => {
                toast.error('Error al eliminar artículo');
                setProcessingItemId(null);
            }
        });
    };

    return (
        <AppLayout>
            <div className="mb-8">
                <h1 className="text-3xl font-bold mb-1">Mi Carrito</h1>
                <p className="text-gray-400 text-sm">{cartItems.length} artículo{cartItems.length !== 1 ? 's' : ''}</p>
            </div>

            {cartItems.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-32">
                    <svg className="w-16 h-16 mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p className="text-gray-400 text-lg mb-6">Tu carrito está vacío</p>
                    <Link
                        href="/shop"
                        className="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200"
                    >
                        Continuar comprando
                    </Link>
                </div>
            ) : (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Artículos del carrito */}
                    <div className="lg:col-span-2">
                        <div className="space-y-4">
                            {cartItems.map(item => (
                                <div
                                    key={item.id}
                                    className="flex gap-4 p-4 rounded-lg border border-white/10 bg-white/5 hover:bg-white/8 transition-colors"
                                >
                                    {/* Imagen del producto */}
                                    <div className="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-white/5">
                                        {item.product.primary_image ? (
                                            <img
                                                src={item.product.primary_image.url}
                                                alt={item.product.name}
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center text-gray-600">
                                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5}
                                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        )}
                                    </div>

                                    {/* Info del producto */}
                                    <div className="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 className="font-medium text-base line-clamp-2">{item.product.name}</h3>
                                            <p className="text-gray-400 text-sm mt-1">
                                                ${parseFloat(item.product.price).toFixed(2)}
                                            </p>
                                        </div>

                                        {/* Controles de cantidad y eliminación */}
                                        <div className="flex items-center justify-between mt-3">
                                            {/* Controles de cantidad */}
                                            <div className="flex items-center gap-2 bg-white/5 rounded-lg border border-white/10">
                                                <button
                                                    onClick={() => handleDecreaseQuantity(item.id)}
                                                    disabled={processingItemId === item.id}
                                                    className="p-2 text-gray-400 hover:text-white transition disabled:opacity-50"
                                                >
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
                                                    </svg>
                                                </button>
                                                <input
                                                    type="number"
                                                    value={item.quantity}
                                                    readOnly
                                                    className="w-12 h-8 text-center bg-gray-800 text-white text-sm font-medium border border-gray-600 rounded hover:border-gray-500 transition"
                                                />
                                                <button
                                                    onClick={() => handleIncreaseQuantity(item.id)}
                                                    disabled={processingItemId === item.id}
                                                    className="p-2 text-gray-400 hover:text-white transition disabled:opacity-50"
                                                >
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>

                                            {/* Botón eliminar */}
                                            <button
                                                onClick={() => handleRemoveItem(item.id)}
                                                disabled={processingItemId === item.id}
                                                className="p-2 text-red-400 hover:text-red-300 transition disabled:opacity-50"
                                            >
                                                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {/* Subtotal por artículo */}
                                    <div className="flex-shrink-0 text-right">
                                        <p className="font-medium text-lg">
                                            ${(item.product.price * item.quantity).toFixed(2)}
                                        </p>
                                        <p className="text-gray-400 text-xs mt-1">
                                            {item.quantity} × ${parseFloat(item.product.price).toFixed(2)}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Resumen de la orden */}
                    <div className="lg:col-span-1">
                        <div className="sticky top-24 p-6 rounded-lg border border-white/10 bg-white/5">
                            <h2 className="text-lg font-semibold mb-6">Resumen de Orden</h2>

                            {/* Detalles */}
                            <div className="space-y-3 pb-6 border-b border-white/10">
                                <div className="flex justify-between text-sm text-gray-400">
                                    <span>Subtotal ({cartItems.length} {cartItems.length === 1 ? 'artículo' : 'artículos'})</span>
                                    <span>${cartTotal.toFixed(2)}</span>
                                </div>
                                <div className="flex justify-between text-sm text-gray-400">
                                    <span>Envío</span>
                                    <span className="text-green-400">Gratis</span>
                                </div>
                                <div className="flex justify-between text-sm text-gray-400">
                                    <span>Impuestos</span>
                                    <span>${(cartTotal * 0.16).toFixed(2)}</span>
                                </div>
                            </div>

                            {/* Total */}
                            <div className="flex justify-between text-lg font-semibold mt-6 mb-6">
                                <span>Total:</span>
                                <span>${(cartTotal + cartTotal * 0.16).toFixed(2)}</span>
                            </div>

                            {/* Botón de checkout */}
                            <Link
                                href="/checkout"
                                className="block w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 mb-3 text-center"
                            >
                                Continuar con la Compra
                            </Link>

                            {/* Link para continuar comprando */}
                            <Link
                                href="/shop"
                                className="block w-full text-center py-2 px-4 border border-white/20 text-white hover:bg-white/5 rounded-lg transition duration-200"
                            >
                                Continuar Comprando
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

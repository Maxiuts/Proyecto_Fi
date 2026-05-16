import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { toast } from 'react-hot-toast';

export default function Checkout({ items, total }) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [formData, setFormData] = useState({
        street: '',
        city: '',
        postal_code: '',
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (!formData.street || !formData.city || !formData.postal_code) {
            toast.error('Por favor completa todos los campos');
            return;
        }

        setIsSubmitting(true);

        router.post('/checkout', formData, {
            onSuccess: () => {
                toast.success('¡Pedido realizado exitosamente!');
            },
            onError: (errors) => {
                toast.error(errors.error || 'Error al procesar el pedido');
            },
            onFinish: () => {
                setIsSubmitting(false);
            },
        });
    };

    const numericTotal = Number(total) || 0;
    const tax = numericTotal * 0.16;
    const grandTotal = numericTotal + tax;

    return (
        <>
            <Head title="Checkout" />
            <AppLayout>
                <div className="min-h-screen bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8">
                    <div className="max-w-4xl mx-auto">
                        <h1 className="text-4xl font-bold mb-8">Confirmar Pedido</h1>

                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            {/* Resumen del carrito */}
                            <div className="lg:col-span-2">
                                <div className="bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-700">
                                    <h2 className="text-2xl font-semibold mb-6">Resumen del Pedido</h2>
                                    
                                    <div className="space-y-4 mb-6">
                                        {items.map(item => {
                                            const itemPrice = Number(item.product.price) || 0;
                                            const itemTotal = itemPrice * item.quantity;
                                            
                                            return (
                                                <div key={item.id} className="flex justify-between items-center p-4 bg-gray-750 border border-gray-600 rounded-lg">
                                                    <div className="flex-1">
                                                        <p className="font-semibold text-lg">{item.product.name}</p>
                                                        <p className="text-gray-400">Cantidad: {item.quantity}</p>
                                                    </div>
                                                    <div className="text-right">
                                                        <p className="font-semibold text-lg">${itemTotal.toFixed(2)}</p>
                                                        <p className="text-gray-400 text-sm">${itemPrice.toFixed(2)} c/u</p>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    <div className="border-t border-gray-600 pt-4 space-y-3">
                                        <div className="flex justify-between text-gray-300">
                                            <span>Subtotal:</span>
                                            <span>${numericTotal.toFixed(2)}</span>
                                        </div>
                                        <div className="flex justify-between text-gray-300">
                                            <span>Envío:</span>
                                            <span className="text-green-400 font-medium">Gratis</span>
                                        </div>
                                        <div className="flex justify-between text-gray-300">
                                            <span>Impuesto (16%):</span>
                                            <span>${tax.toFixed(2)}</span>
                                        </div>
                                        <div className="flex justify-between text-2xl font-bold pt-4 border-t border-gray-600 text-white">
                                            <span>Total:</span>
                                            <span className="text-blue-400">${grandTotal.toFixed(2)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Formulario de Envío (Diseño Mejorado) */}
                            <div className="lg:col-span-1">
                                <div className="bg-gray-800 rounded-xl shadow-lg p-6 sticky top-4 border border-gray-700">
                                    <h2 className="text-xl font-semibold mb-6">Dirección de Envío</h2>

                                    <form onSubmit={handleSubmit} className="space-y-5">
                                        <div>
                                            <label htmlFor="street" className="block text-sm font-medium mb-2 text-gray-300">
                                                Calle y Número
                                            </label>
                                            <input
                                                type="text"
                                                id="street"
                                                name="street"
                                                value={formData.street}
                                                onChange={handleChange}
                                                placeholder="Ej. Av. Vallarta 123"
                                                className="w-full px-4 py-3 bg-white text-gray-900 border-0 rounded-lg focus:ring-2 focus:ring-blue-500 font-medium placeholder-gray-400 shadow-inner"
                                            />
                                        </div>

                                        <div>
                                            <label htmlFor="city" className="block text-sm font-medium mb-2 text-gray-300">
                                                Ciudad
                                            </label>
                                            <input
                                                type="text"
                                                id="city"
                                                name="city"
                                                value={formData.city}
                                                onChange={handleChange}
                                                placeholder="Ej. Guadalajara"
                                                className="w-full px-4 py-3 bg-white text-gray-900 border-0 rounded-lg focus:ring-2 focus:ring-blue-500 font-medium placeholder-gray-400 shadow-inner"
                                            />
                                        </div>

                                        <div>
                                            <label htmlFor="postal_code" className="block text-sm font-medium mb-2 text-gray-300">
                                                Código Postal
                                            </label>
                                            <input
                                                type="text"
                                                id="postal_code"
                                                name="postal_code"
                                                value={formData.postal_code}
                                                onChange={handleChange}
                                                placeholder="Ej. 45640"
                                                className="w-full px-4 py-3 bg-white text-gray-900 border-0 rounded-lg focus:ring-2 focus:ring-blue-500 font-medium placeholder-gray-400 shadow-inner"
                                            />
                                        </div>

                                        <div className="pt-2">
                                            <button
                                                type="submit"
                                                disabled={isSubmitting}
                                                className="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-500 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-bold rounded-lg transition-colors flex items-center justify-center shadow-lg"
                                            >
                                                {isSubmitting ? 'Procesando...' : 'Confirmar y Pagar'}
                                            </button>

                                            <Link
                                                href="/cart"
                                                className="w-full mt-3 py-3 px-4 bg-transparent border border-gray-500 hover:bg-gray-700 text-gray-300 font-semibold rounded-lg transition-colors text-center block"
                                            >
                                                Regresar al Carrito
                                            </Link>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </AppLayout>
        </>
    );
}
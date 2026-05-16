import { Link, usePage } from '@inertiajs/react';

export default function AppLayout({ children }) {
    const { auth } = usePage().props;

    return (
        <div className="bg-black text-white min-h-screen">
            {/* Navbar */}
            <nav className="fixed top-0 inset-x-0 z-10 border-b border-white/10 bg-black/60 backdrop-blur-md">
                <div className="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
                    <Link href="/shop" className="text-lg font-semibold tracking-wide hover:text-gray-300 transition">
                        Tienda
                    </Link>
                    <div className="flex items-center gap-4">
                        <Link
                            href="/shop"
                            className="text-sm text-gray-400 hover:text-white transition"
                        >
                            Tienda
                        </Link>
                        <Link
                            href="/cart"
                            className="text-sm text-gray-400 hover:text-white transition"
                        >
                            Carrito
                        </Link>
                        {auth.user?.role === 'admin' && (
                            <Link
                                href="/dashboard"
                                className="text-sm text-gray-400 hover:text-white transition"
                            >
                                Administrar
                            </Link>
                        )}
                        <Link
                            href="/profile"
                            className="text-sm text-gray-400 hover:text-white transition"
                        >
                            Perfil
                        </Link>
                        <form method="POST" action="/logout" className="inline">
                            <button
                                type="submit"
                                className="text-sm text-gray-400 hover:text-white transition"
                            >
                                Salir
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-6 pt-24 pb-16">
                {children}
            </main>
        </div>
    );
}

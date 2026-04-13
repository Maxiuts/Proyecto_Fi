<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-6">

        {{-- Error OAuth --}}
        @if (session('error'))
            <div class="mb-4 text-sm text-red-400 bg-red-950 border border-red-800 rounded-lg p-3">
                {{ session('error') }}
            </div>
        @endif

        {{-- Botones OAuth --}}
        <div class="space-y-3 mb-6">
            <a href="{{ route('socialite.redirect', 'google') }}"
               class="flex items-center justify-center gap-3 w-full px-4 py-2.5
                      border border-white/20 rounded-lg text-sm font-medium
                      text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continuar con Google
            </a>

            <a href="{{ route('socialite.redirect', 'github') }}"
               class="flex items-center justify-center gap-3 w-full px-4 py-2.5
                      border border-white/20 rounded-lg text-sm font-medium
                      text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/>
                </svg>
                Continuar con GitHub
            </a>
        </div>

        {{-- Divisor --}}
        <div class="relative mb-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-white/20"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-black text-gray-500">o con tu correo</span>
            </div>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                    Correo electronico
                </label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg
                              text-sm text-white placeholder-gray-500
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('email')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
                    Contrasena
                </label>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       class="w-full px-3 py-2 bg-white/5 border border-white/20 rounded-lg
                              text-sm text-white placeholder-gray-500
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('password')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-400">
                    <input type="checkbox" name="remember"
                           class="rounded border-white/20 bg-white/5">
                    Recordarme
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-indigo-400 hover:text-indigo-300">
                        Olvide mi contrasena
                    </a>
                @endif
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2.5 rounded-lg text-sm
                           font-medium hover:bg-indigo-700 transition">
                Iniciar sesion
            </button>

            <p class="text-center text-sm text-gray-500">
                No tienes cuenta?
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300">
                    Registrate aqui
                </a>
            </p>
        </form>

    </div>

</body>
</html>
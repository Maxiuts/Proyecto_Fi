<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes star-travel {
                from {
                    transform: rotate(var(--angle)) translateY(-3vh) scaleY(.85);
                    opacity: 0;
                }

                15% {
                    opacity: .95;
                }

                to {
                    transform: rotate(var(--angle)) translateY(62vh) scaleY(1.35);
                    opacity: 0;
                }
            }

            @keyframes nebula-drift {
                0%, 100% {
                    transform: translate3d(0, 0, 0) scale(1);
                }

                50% {
                    transform: translate3d(0, 1rem, 0) scale(1.06);
                }
            }
        </style>
    </head>
    <body class="min-h-screen bg-black text-white antialiased">
        <main class="relative isolate flex min-h-screen items-center justify-center overflow-hidden bg-black px-6 py-10">
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(153,69,255,0.18),_transparent_24%),radial-gradient(circle_at_bottom,_rgba(91,33,182,0.2),_transparent_20%),linear-gradient(180deg,_rgba(0,0,0,0.78)_0%,_rgba(0,0,0,0.98)_100%)]"></div>
                <div class="absolute inset-x-0 top-0 h-28 bg-[radial-gradient(circle_at_top,_rgba(168,85,247,0.38),_transparent_58%)] blur-2xl [animation:nebula-drift_12s_ease-in-out_infinite]"></div>
                <div class="absolute inset-x-0 bottom-0 h-32 bg-[radial-gradient(circle_at_bottom,_rgba(168,85,247,0.26),_transparent_58%)] blur-3xl [animation:nebula-drift_14s_ease-in-out_infinite]"></div>

                <div class="absolute inset-0 overflow-hidden">
                    @foreach (range(1, 42) as $index)
                        <span
                            class="absolute left-1/2 top-1/2 block h-8 w-[3px] rounded-full bg-white/95 shadow-[0_0_12px_rgba(255,255,255,0.85)] [animation:star-travel_var(--duration)_linear_infinite]"
                            style="
                                --angle: {{ ($index * 360) / 42 }}deg;
                                --duration: {{ 1.6 + (($index % 7) * 0.18) }}s;
                                margin-left: {{ (($index % 6) - 3) * 10 }}px;
                                animation-delay: -{{ 0.15 * $index }}s;
                            "
                        ></span>
                    @endforeach
                </div>
            </div>

            <div class="relative z-10 w-full max-w-md">
                <div class="absolute inset-x-10 -top-10 h-24 rounded-full bg-violet-500/30 blur-3xl"></div>

                <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/8 p-8 shadow-[0_20px_80px_rgba(0,0,0,0.65)] backdrop-blur-xl sm:p-10">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-violet-300/80 to-transparent"></div>

                    <div class="mb-8 space-y-3 text-center">
                        <span class="inline-flex rounded-full border border-violet-300/20 bg-violet-400/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-violet-200">
                            Acceso seguro
                        </span>

                        <div class="space-y-2">
                            <h1 class="text-4xl font-black tracking-tight text-white sm:text-5xl">Inicia sesión</h1>
                            <p class="text-sm leading-6 text-white/70 sm:text-base">
                                Un fondo tipo warp speed con glow violeta, inspirado en tu referencia.
                            </p>
                        </div>
                    </div>

                    <form class="space-y-5" action="#" method="GET">
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-white/80">Correo electrónico</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="tu@correo.com"
                                class="w-full rounded-2xl border border-white/10 bg-black/35 px-4 py-3 text-sm text-white outline-none ring-0 placeholder:text-white/35 transition focus:border-violet-400/60 focus:bg-black/45"
                            >
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-4">
                                <label for="password" class="text-sm font-medium text-white/80">Contraseña</label>
                                <a href="#" class="text-xs font-medium text-violet-200 transition hover:text-violet-100">
                                    ¿La olvidaste?
                                </a>
                            </div>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full rounded-2xl border border-white/10 bg-black/35 px-4 py-3 text-sm text-white outline-none ring-0 placeholder:text-white/35 transition focus:border-violet-400/60 focus:bg-black/45"
                            >
                        </div>

                        <label class="flex items-center gap-3 text-sm text-white/70">
                            <input
                                type="checkbox"
                                class="h-4 w-4 rounded border-white/20 bg-black/40 text-violet-500 focus:ring-violet-400/50"
                            >
                            Recordarme en este dispositivo
                        </label>

                        <button
                            type="button"
                            class="w-full rounded-2xl bg-gradient-to-r from-violet-500 via-fuchsia-500 to-violet-400 px-4 py-3 text-sm font-bold text-white shadow-[0_10px_30px_rgba(139,92,246,0.45)] transition hover:scale-[1.01] hover:shadow-[0_12px_40px_rgba(139,92,246,0.58)]"
                        >
                            Entrar
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>

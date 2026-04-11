<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stars Background</title>
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-black text-white">

    <!-- Tu contenido -->
    <div class="relative z-10 flex items-center justify-center h-screen">
        <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-2xl p-8 shadow-xl
            hover:text-gray-600">
            <h1 class="text-4xl font-bold mb-4">
                Bienvenido!
            </h1>
        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinámicas y Eventos El Palomo Negro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-stone-900 text-stone-100 font-sans selection:bg-amber-500 selection:text-stone-950" x-data="{ cookieModal: true, currentTab: 'manual' }">

    @include('layouts.cookie-modal')

    @include('layouts.nav')

    <main>
        @yield('content')
    </main>

    <!-- FOOTER & LEGALES -->
    <footer id="politicas" class="bg-stone-950 border-t border-stone-800 py-16 px-6 text-stone-500 text-xs">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div>
                <img src="{{ asset('logo_palomo.jpg') }}" alt="Logo Footer" class="h-12 mb-4 object-contain">
                <p class="text-stone-400 leading-relaxed">Diversión, tranquilidad y transparencia en cada uno de nuestros servicios campestres y dinámicas.</p>
            </div>
            <div>
                <h5 class="text-white font-bold uppercase mb-4 tracking-wider">Enlaces Legales</h5>
                <ul class="space-y-2">
                    <li><a href="#politicas" class="hover:text-amber-500 transition-colors">Términos y Condiciones</a></li>
                    <li><a href="#politicas" class="hover:text-amber-500 transition-colors">Políticas de Privacidad</a></li>
                    <li><a href="#politicas" class="hover:text-amber-500 transition-colors">Tratamiento de Datos Personales</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold uppercase mb-4 tracking-wider">Contacto Directo</h5>
                <p class="text-stone-400 mb-1">📍 Km 12 Vía Campestre, Medellín, Colombia</p>
                <p class="text-stone-400">📧 contacto@palomonegro.com</p>
            </div>
            <div>
                <h5 class="text-white font-bold uppercase mb-4 tracking-wider">Nuestras Redes</h5>
                <div class="flex gap-4 text-lg text-stone-400">
                    <a href="https://instagram.com" target="_blank" class="hover:text-amber-500 transition-colors"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com" target="_blank" class="hover:text-amber-500 transition-colors"><i class="bi bi-facebook"></i></a>
                    <a href="https://tiktok.com" target="_blank" class="hover:text-amber-500 transition-colors"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto border-t border-stone-900 pt-8 text-center text-stone-600">
            &copy; {{ date('Y') }} Dinámicas y Eventos El Palomo Negro. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>
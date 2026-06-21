<nav class="fixed top-0 inset-x-0 bg-stone-950/80 backdrop-blur-md border-b border-stone-800/40 z-50 px-6 py-4" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('logo_palomo.jpg') }}" alt="El Palomo Negro Logo" class="h-11 object-contain">
        </a>        

        <!-- Menú Escritorio -->
        <div class="hidden md:flex gap-6 text-xs uppercase tracking-wider font-semibold text-stone-400">
            <a href="{{ route('home') }}" class="hover:text-amber-500 transition-colors">Inicio</a>
            <a href="{{ route('home') }}#unidades" class="hover:text-amber-500 transition-colors">Instalaciones</a>
            <a href="{{ route('home') }}#participar" class="hover:text-amber-500 transition-colors">Sorteos</a>
            <a href="{{ route('home') }}#reservar" class="hover:text-amber-500 transition-colors">Reservas</a>
            <a href="{{ route('home') }}#opiniones" class="hover:text-amber-500 transition-colors">Reseñas</a>
            <!-- CTA Escritorio -->
            <a href="{{ route('home') }}#reservar" class="hidden md:block bg-amber-500 hover:bg-amber-400 text-stone-950 text-xs font-bold px-4 py-2 rounded-lg transition-all">
                Reservar Mesa
            </a>
        </div>

        <!-- Botón Toggle Móvil -->
        <button @click="open = !open" class="md:hidden text-2xl text-white">
            <i class="bi" :class="open ? 'bi-x' : 'bi-list'"></i>
        </button>

        <!-- Auth Toggle -->
        @auth
            <a href="{{ url('/dashboard') }}" class="bg-stone-800 hover:bg-stone-700 text-white px-4 py-2 rounded-lg transition-all">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="text-amber-500 hover:text-amber-400">Ingresar</a>
        @endauth
    </div>

    <!-- Menú Móvil -->
    <div x-show="open" x-cloak class="md:hidden bg-stone-900 border-t border-stone-800 mt-4 p-6 flex flex-col gap-4 text-xs uppercase font-bold text-stone-400">
        <a href="{{ route('home') }}" @click="open = false">Inicio</a>
        <a href="{{ route('home') }}#participar" @click="open = false">Sorteos</a>
        <a href="{{ route('home') }}#reservar" @click="open = false">Reservas</a>
        
        @auth
            <a href="{{ url('/dashboard') }}" class="bg-stone-800 text-white p-3 rounded-lg text-center">Ir al Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="bg-amber-500 text-stone-950 p-3 rounded-lg text-center">Ingresar / Login</a>
        @endauth
    </div>
</nav>
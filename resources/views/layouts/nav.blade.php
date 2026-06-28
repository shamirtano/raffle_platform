<nav class="fixed top-0 inset-x-0 bg-stone-950/80 backdrop-blur-md border-b border-stone-800/40 z-50 px-6 py-4" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('logo_palomo.jpg') }}" alt="El Palomo Negro Logo" class="h-11 object-contain">
        </a>        
        
        <div class="hidden md:flex gap-6 text-xs uppercase tracking-wider font-semibold text-stone-400 items-center">
            <a href="{{ url('/') }}" class="hover:text-amber-500 transition-colors">Inicio</a>
            <a href="{{ url('/#unidades') }}" class="hover:text-amber-500 transition-colors">Instalaciones</a>
            <a href="{{ url('/#participar') }}" class="hover:text-amber-500 transition-colors">Sorteos</a>
            <a href="{{ url('/#reservar') }}" class="hover:text-amber-500 transition-colors">Reservas</a>
            <a href="{{ url('/#opiniones') }}" class="hover:text-amber-500 transition-colors">Reseñas</a>
            
            <!-- CTA Escritorio -->
            <a href="{{ url('/#reservar') }}" class="hidden md:block bg-amber-500 hover:bg-amber-400 text-stone-950 text-xs font-bold px-4 py-2 rounded-lg transition-all">
                Reservar Mesa
            </a>
        </div>

        <!-- Botón Toggle Móvil y Auth (Se mantienen iguales) -->
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ url('/admin') }}" class="hidden md:block bg-stone-800 hover:bg-stone-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-all">Dashboard</a>
            @else
                <a href="{{ route('filament.admin.auth.login') }}" class="hidden md:block text-amber-500 hover:text-amber-400 text-xs font-bold uppercase tracking-wider">Ingresar</a>
            @endauth

            <button @click="open = !open" class="md:hidden text-2xl text-white focus:outline-none">
                <i class="bi" :class="open ? 'bi-x' : 'bi-list'"></i>
            </button>
        </div>
    </div>

    <!-- Menú Móvil (🟢 URLs Corregidas) -->
    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="md:hidden bg-stone-950 border-t border-stone-800/60 mt-4 p-6 flex flex-col gap-4 text-xs uppercase font-bold text-stone-400 rounded-xl shadow-2xl">
        
        <a href="{{ url('/') }}" @click="open = false" class="hover:text-amber-500 py-2">Inicio</a>
        <a href="{{ url('/#unidades') }}" @click="open = false" class="hover:text-amber-500 py-2">Instalaciones</a>
        <a href="{{ url('/#participar') }}" @click="open = false" class="hover:text-amber-500 py-2">Sorteos</a>
        <a href="{{ url('/#reservar') }}" @click="open = false" class="hover:text-amber-500 py-2">Reservas</a>
        
        <hr class="border-stone-800/60 my-1">

        @auth
            <a href="{{ url('/admin') }}" class="bg-stone-800 text-white p-3 rounded-lg text-center font-bold">Ir al Dashboard</a>
        @else
            <a href="{{ route('filament.admin.auth.login') }}" class="bg-amber-500 text-stone-950 p-3 rounded-lg text-center font-bold">Ingresar / Login</a>
        @endauth
    </div>
</nav>
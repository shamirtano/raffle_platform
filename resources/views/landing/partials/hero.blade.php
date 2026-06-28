<!-- HERO SECTION -->
<section id="hero" class="relative h-screen flex items-center justify-center bg-black overflow-hidden pt-0">    
    <div class="absolute inset-0 bg-cover bg-center opacity-25 scale-105" 
         style="background-image: url('{{ !empty($hero['main_image']) ? asset('storage/' . $hero['main_image']) : 'https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1920' }}');">
    </div>
    
    <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-transparent to-black"></div>
    
    <div class="relative z-10 text-center px-4 max-w-4xl mt-6 pt-6">
        <img src="{{ asset('logo_palomo.jpg') }}" alt="Logo Principal" class="h-64 mt-8 mx-auto mb-6 object-contain filter drop-shadow-[0_5px_15px_rgba(217,119,6,0.2)]">
        
        <!-- 🟢 Título Dinámico -->
        <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4 uppercase">
            {{ $hero['title'] ?? 'Experiencias Exclusivas de Campo' }}
        </h2>
        
        <!-- 🟢 Subtítulo / Descripción Dinámica -->
        <p class="text-stone-400 text-base md:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
            {{ $hero['subtitle'] ?? 'Disfruta de la mejor gastronomía criolla en nuestro restaurante, relájate en las piscinas, adquiere tu lote campestre ideal y participa en nuestras dinámicas de sorteo de fondos.' }}
        </p>
        
        <div class="flex flex-wrap justify-center gap-4">
            <a href="#reservar" class="bg-amber-500 text-stone-950 px-8 py-3.5 rounded-full font-bold text-sm shadow-xl hover:bg-amber-400 transition-all">
                Agendar Espacio / Mesa
            </a>
            
            <!-- 🟢 Botón condicional o Enlace al Video Promocional si existe en Filament -->
            @if(!empty($hero['video_url']))
                <a href="{{ $hero['video_url'] }}" target="_blank" class="bg-stone-800 text-stone-200 border border-stone-700 px-8 py-3.5 rounded-full font-bold text-sm hover:bg-stone-700 transition-all flex items-center gap-2">
                    <span>▶ Ver Video</span>
                </a>
            @else
                <a href="#participar" class="bg-stone-800 text-stone-200 border border-stone-700 px-8 py-3.5 rounded-full font-bold text-sm hover:bg-stone-700 transition-all">
                    Ver Sorteos Activos
                </a>
            @endif
        </div>
    </div>
</section>
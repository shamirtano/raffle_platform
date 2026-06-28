<!-- DETALLE PROFESIONAL DE INSTALACIONES -->
{{-- <section id="unidades" class="py-24 px-6 max-w-7xl mx-auto border-t border-stone-800/40">
    <div class="text-center mb-16">
        <span class="text-amber-500 text-xs font-bold uppercase tracking-widest block mb-2">Instalaciones</span>
        <h3 class="text-3xl font-black text-white">Todo lo que Ofrecemos</h3>
        <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-3"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
        <div>
            <span class="text-amber-500 font-bold text-sm block mb-1">01. Restaurante Criollo Gourmet</span>
            <h4 class="text-2xl font-bold text-white mb-4">Sabor de Brasa Tradicional</h4>
            <p class="text-stone-400 text-sm leading-relaxed mb-4">Especialistas en cortes de carne premium, asados criollos a fuego lento y platos autóctonos en un espacio campestre sofisticado con atención de primera clase.</p>
            <a href="#reservar" class="text-amber-500 text-xs font-bold inline-flex items-center gap-1 hover:underline">Reservar una mesa ahora <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="h-64 bg-stone-800 rounded-3xl overflow-hidden shadow-xl border border-stone-700/30">
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=800');"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center md:flex-row-reverse mb-20">
        <div class="md:order-2">
            <span class="text-amber-500 font-bold text-sm block mb-1">02. Piscinas & Toboganes</span>
            <h4 class="text-2xl font-bold text-white mb-4">Diversión y Relajación Total</h4>
            <p class="text-stone-400 text-sm leading-relaxed mb-4">Zonas húmedas completamente equipadas para el disfrute de niños y adultos. Piscinas bajo estrictas normas de bioseguridad y PH controlado.</p>
        </div>
        <div class="h-64 bg-stone-800 rounded-3xl overflow-hidden shadow-xl border border-stone-700/30 md:order-1">
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?q=80&w=800');"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-amber-500 font-bold text-sm block mb-1">03. Proyecto Inmobiliario Campestre</span>
            <h4 class="text-2xl font-bold text-white mb-4">Venta de Lotes Exclusivos</h4>
            <p class="text-stone-400 text-sm leading-relaxed mb-4">Construye la casa de tus sueños. Ofrecemos lotes con topografía ideal, acceso a servicios públicos, escrituras al día y una proyección de valorización inigualable.</p>
        </div>
        <div class="h-64 bg-stone-800 rounded-3xl overflow-hidden shadow-xl border border-stone-700/30">
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=800');"></div>
        </div>
    </div>
</section> --}}

<section id="unidades" class="py-24 px-6 max-w-7xl mx-auto border-t border-stone-800/40">
    <div class="text-center mb-16">
        <span class="text-amber-500 text-xs font-bold uppercase tracking-widest block mb-2">Instalaciones</span>
        <h3 class="text-3xl font-black text-white">Todo lo que Ofrecemos</h3>
        <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-3"></div>
    </div>

    @if(!empty($menu) && count($menu) > 0)
        @foreach($menu as $index => $item)
            @php
                // Alternamos el orden visual de las columnas usando el índice (Par / Impar)
                $isEven = $index % 2 === 0;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20 last:mb-0">
                <!-- Columna de Texto -->
                <div class="{{ $isEven ? '' : 'md:order-2' }}">
                    <span class="text-amber-500 font-bold text-sm block mb-1">
                        0{{ $index + 1 }}. {{ $item['name'] ?? 'Servicio Especializado' }}
                    </span>
                    
                    @if(isset($item['price']) && $item['price'] > 0)
                        <h4 class="text-2xl font-bold text-white mb-2">
                            Desde ${{ number_format($item['price'], 0, ',', '.') }}
                        </h4>
                    @else
                        <h4 class="text-2xl font-bold text-white mb-2">Calidad Profesional</h4>
                    @endif

                    <p class="text-stone-400 text-sm leading-relaxed mb-4">
                        {{ $item['description'] ?? '' }}
                    </p>

                    @if(!empty($item['link']))
                        <a href="{{ $item['link'] }}" class="text-amber-500 text-xs font-bold inline-flex items-center gap-1 hover:underline">
                            Más detalles del servicio <i class="bi bi-arrow-right"></i>
                        </a>
                    @else
                        <a href="#reservar" class="text-amber-500 text-xs font-bold inline-flex items-center gap-1 hover:underline">
                            Reservar espacio ahora <i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <!-- Columna de Imagen -->
                <div class="h-64 bg-stone-800 rounded-3xl overflow-hidden shadow-xl border border-stone-700/30 {{ $isEven ? '' : 'md:order-1' }}">
                    <div class="w-full h-full bg-cover bg-center" 
                         style="background-image: url('{{ !empty($item['image']) ? asset('storage/' . $item['image']) : 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?q=80&w=800' }}');">
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Fallback estático de seguridad en caso de que borren los datos de la base de datos -->
        <div class="text-center text-stone-500 py-8">
            No hay servicios o instalaciones registradas actualmente.
        </div>
    @endif
</section>
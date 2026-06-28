{{-- <section id="participar" class="bg-stone-950 py-24 px-6 border-t border-stone-800/40">
    <div class="text-center mb-16">
        <h3 class="text-3xl font-black text-white">Sorteos Disponibles</h3>
        <p class="text-stone-400 mt-2">Selecciona un sorteo para ver la disponibilidad de números y formas de juego.</p>
    </div>

    <div class="grid grid-cols-1 gap-12 max-w-7xl mx-auto">
        @if ($activeRaffles->isEmpty())
            <div class="bg-stone-800 border border-stone-700 rounded-3xl p-8 text-center">
                <h4 class="text-xl font-bold text-white mb-2">No hay sorteos activos en este momento</h4>
                <p class="text-stone-400 text-sm">Por favor, vuelve a visitar esta sección más tarde para participar en nuestras emocionantes dinámicas.</p>
            </div>
        @else
            @foreach($activeRaffles as $raffle)
            <div class="bg-stone-900 border border-stone-800 rounded-3xl p-8 hover:border-amber-500 transition-all flex flex-col gap-8">
                <div>
                    <span class="text-amber-500 text-xs font-bold uppercase tracking-widest mb-2 block">JUEGA CON LA LOTERÍA DE {{ $raffle->reference_lottery }}</span>
                    <h4 class="text-2xl font-bold text-white">{{ $raffle->title }}</h4>
                </div>           
                
                <div class="flex flex-col lg:flex-row gap-12 items-start justify-between">
                    <div class="w-full lg:w-2/3 flex flex-col h-full">                        
                        <p class="text-stone-400 text-sm mb-6">{{ $raffle->description }}</p>
                        <div class="space-y-3 flex-grow grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="flex justify-between border-b border-stone-800/60 pb-2 text-stone-400 text-md"><span>Fecha Sorteo:</span> <span class="text-white font-bold">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d M Y') }}</span></div>
                            <div class="flex justify-between border-b border-stone-800/60 pb-2 text-stone-400 text-md"><span>Modalidad:</span> <span class="text-white font-bold">{{ $raffle->is_cash_prize ? 'Premio en Dinero' : 'Premio en Artículo' }}</span></div>
                            <div class="flex justify-between border-b border-stone-800/60 pb-2 text-stone-400 text-md"><span>Premio avaluado en:</span> <span class="text-white font-bold">$ {{ number_format($raffle->jackpot_prize, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between border-b border-stone-800/60 pb-2 text-stone-400 text-md"><span>Precio Oportunidad:</span> <span class="text-white font-bold">$ {{ number_format($raffle->ticket_price, 0, ',', '.') }}</span></div>
                        </div>                        
                    </div>
                    
                    <div class="w-full lg:w-1/3 flex justify-center lg:justify-end">
                        <div class="relative max-w-[320px] w-full min-h-[200px] rounded-2xl overflow-hidden border border-stone-800 shadow-2xl">
                            <img src="{{ asset('storage/' . $raffle->image_path) }}" alt="{{ $raffle->title }}" class="w-full h-auto object-cover">
                        </div>
                    </div>
                </div>

                <div class="border-t border-stone-800/60 pt-6">
                    <h5 class="text-sm font-bold text-stone-400 uppercase tracking-wider mb-4">Combos y Paquetes de Oportunidades</h5>
                    
                    @php                        
                        $packages = $packageMultiples ?? [1, 3, 5, 10];
                    @endphp

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        @foreach($packages as $quantity)
                            <div class="bg-stone-950 border border-stone-800 rounded-2xl p-4 text-center hover:border-amber-500/50 transition-all flex flex-col justify-between">
                                <div>
                                    <span class="text-xs font-bold text-amber-500 uppercase">Combo</span>
                                    <p class="text-2xl font-black text-white my-1">{{ $quantity }}</p>
                                    <p class="text-xs text-stone-400 mb-3">{{ $quantity == 1 ? 'Boleta' : 'Boletas' }}</p>
                                </div>
                                <div class="text-sm font-bold text-white border-t border-stone-800 pt-2">
                                    $ {{ number_format($quantity * $raffle->ticket_price, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <a href="{{ route('raffles.show', $raffle->id) }}" class="block text-center bg-amber-500 hover:bg-amber-600 text-stone-950 font-black py-4 rounded-xl transition-all shadow-lg shadow-amber-500/10">
                    Comprar / Seleccionar Números
                </a>
            </div>
            @endforeach
        @endif
    </div>
</section> --}}

<section id="participar" class="bg-stone-950 py-24 px-6 border-t border-stone-800/40">
    <div class="text-center mb-16">
        <span class="text-amber-500 text-xs font-bold uppercase tracking-widest block mb-2">Dinámicas Activas</span>
        <h3 class="text-3xl font-black text-white">Sorteos Disponibles</h3>
        <p class="text-stone-400 text-sm mt-2 max-w-xl mx-auto">Selecciona un sorteo para ver la disponibilidad de números, combos promocionales y formas de juego.</p>
        <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-4"></div>
    </div>

    <div class="grid grid-cols-1 gap-12 max-w-7xl mx-auto">
        @if ($activeRaffles->isEmpty())
            <div class="bg-stone-900 border border-stone-800 rounded-3xl p-12 text-center max-w-2xl mx-auto shadow-xl">
                <div class="text-amber-500 text-4xl mb-4">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <h4 class="text-xl font-bold text-white mb-2">No hay sorteos activos en este momento</h4>
                <p class="text-stone-400 text-sm leading-relaxed">Por favor, vuelve a visitar esta sección más tarde para participar en nuestras emocionantes dinámicas y sorteos de fondos.</p>
            </div>
        @else
            @foreach($activeRaffles as $raffle)
                <div class="bg-stone-900 border border-stone-800/80 rounded-3xl p-6 md:p-8 hover:border-amber-500/40 transition-all duration-300 flex flex-col gap-6 shadow-xl relative overflow-hidden group">
                    
                    <!-- Encabezado del Sorteo -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 border-b border-stone-800/60 pb-4">
                        <div>
                            <span class="text-amber-500 text-xs font-bold uppercase tracking-widest mb-1 block">
                                🎰 Juega con la Lotería de {{ $raffle->reference_lottery }}
                            </span>
                            <h4 class="text-2xl font-black text-white tracking-tight group-hover:text-amber-400 transition-colors">
                                {{ $raffle->title }}
                            </h4>
                        </div>
                        
                        <!-- Badge de Modalidad -->
                        <span class="self-start px-3 py-1.5 rounded-full text-xs font-bold tracking-wide uppercase {{ $raffle->is_cash_prize ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' }}">
                            {{ $raffle->is_cash_prize ? '💵 Premio Efectivo' : '🎁 Artículo Especial' }}
                        </span>
                    </div>
                
                    <!-- Distribución en 3 Columnas -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                        
                        <!-- Bloque Izquierdo (Ocupa 2 de 3 columnas): Info + Combos -->
                        <div class="lg:col-span-2 flex flex-col justify-between gap-6">                        
                            <div>
                                <p class="text-stone-400 text-sm leading-relaxed mb-6">
                                    {{ $raffle->description }}
                                </p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                    <div class="flex justify-between border-b border-stone-800/40 pb-2">
                                        <span class="text-stone-400">📅 Fecha Sorteo:</span> 
                                        <span class="text-white font-bold">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-stone-800/40 pb-2">
                                        <span class="text-stone-400">💎 Premio Mayor:</span> 
                                        <span class="text-amber-400 font-extrabold">$ {{ number_format($raffle->jackpot_prize, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-stone-800/40 pb-2">
                                        <span class="text-stone-400">🎟️ Valor Boleta:</span> 
                                        <span class="text-white font-bold">$ {{ number_format($raffle->ticket_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between border-b border-stone-800/40 pb-2">
                                        <span class="text-stone-400">📌 Estado:</span> 
                                        <span class="text-emerald-400 font-bold">Disponible</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Combos Promocionales en la misma sección -->
                            <div class="bg-stone-950/40 border border-stone-800/60 rounded-2xl p-4">
                                <h5 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">
                                    🔥 Combos Promocionales Sugeridos
                                </h5>
                                
                                @php
                                    $packages = $packageMultiples ?? [1, 3, 5, 10];
                                @endphp

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($packages as $quantity)
                                        <div class="bg-stone-950 border border-stone-800/80 rounded-xl p-3 text-center hover:border-amber-500/30 transition-colors flex flex-col justify-between">
                                            <div>
                                                <p class="text-xl font-black text-white">{{ $quantity }}</p>
                                                <p class="text-[10px] text-stone-500 uppercase font-semibold tracking-wider mb-1">
                                                    {{ $quantity == 1 ? 'Boleta' : 'Boletas' }}
                                                </p>
                                            </div>
                                            <div class="text-xs font-bold text-amber-500 border-t border-stone-900 pt-2">
                                                $ {{ number_format($quantity * $raffle->ticket_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bloque Derecho (Ocupa 1 de 3 columnas): Imagen Completa -->
                        <div class="lg:col-span-1 w-full flex justify-center items-stretch">
                            <div class="w-full max-w-[340px] lg:max-w-none h-full min-h-[260px] lg:min-h-full rounded-2xl overflow-hidden border border-stone-800 bg-stone-950/60 shadow-2xl relative flex items-center justify-center p-2">
                                <!-- 📸 Imagen de fondo difuminada para un efecto premium (opcional) -->
                                <img src="{{ asset('storage/' . $raffle->image_path) }}" 
                                    class="w-full h-full object-cover opacity-10 blur-md absolute inset-0 select-none pointer-events-none">

                                <!-- 🖼️ Imagen Principal Ajustada al Alto sin recortarse -->
                                <img src="{{ asset('storage/' . $raffle->image_path) }}" 
                                    alt="{{ $raffle->title }}" 
                                    class="max-w-full max-h-full object-contain rounded-xl z-10 group-hover:scale-102 transition-transform duration-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón de Acción Principal -->
                    <a href="{{ route('raffles.show', $raffle->id) }}" 
                       class="block text-center bg-amber-500 hover:bg-amber-400 text-stone-950 font-black py-4 rounded-xl transition-all shadow-lg hover:shadow-amber-500/5 text-sm uppercase tracking-wider mt-2">
                        <i class="bi bi-ticket-detailed-fill me-2"></i> Adquirir Números / Boletas
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</section>
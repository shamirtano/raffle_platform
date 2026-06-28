<section id="ubicacion" class="bg-stone-950 py-24 px-6 border-t border-stone-800/40">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        
        <!-- Información de Horarios -->
        <div>
            <span class="text-amber-500 text-xs font-bold uppercase tracking-widest block mb-2">Cuándo Visitarnos</span>
            <h3 class="text-3xl font-black text-white mb-6">Horarios de Atención</h3>
            
            <div class="space-y-4 max-w-md">                
                @if(!empty($hours))
                    @foreach($hours as $dia => $horario)
                        <div class="flex justify-between items-center border-b border-stone-800/60 pb-3">
                            <span class="text-stone-200 font-bold capitalize text-sm">
                                {{ str_replace('_', ' y ', $dia) }}
                            </span>
                            <span class="text-amber-500 font-medium text-sm text-right">
                                {{ $horario }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-stone-500 text-sm">Horarios no configurados.</p>
                @endif
            </div>
        </div>

        <!-- Mapa / Ubicación -->
        <div class="h-80 bg-stone-900 rounded-3xl overflow-hidden border border-stone-800 shadow-2xl relative">
            <div class="w-full h-full rounded-2xl overflow-hidden border border-stone-800">
                <iframe 
                    src="{!! $business_info['maps_iframe'] ?? 'https://www.google.com/maps/embed?pb=...' !!}" 
                    class="w-full h-full border-0" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>
<!-- SECCIÓN 2: SORTEOS DISPONIBLES (Vitrina) -->
<section id="participar" class="bg-stone-950 py-24 px-6 border-t border-stone-800/40">
    <div class="text-center mb-16">
        <h3 class="text-3xl font-black text-white">Sorteos Disponibles</h3>
        <p class="text-stone-400 mt-2">Selecciona un sorteo para ver la disponibilidad de números y formas de juego.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-1 gap-8">
        @if ($activeRaffles->isEmpty())
            <div class="col-span-full bg-stone-800 border border-stone-700 rounded-3xl p-8 text-center">
                <h4 class="text-xl font-bold text-white mb-2">No hay sorteos activos en este momento</h4>
                <p class="text-stone-400 text-sm">Por favor, vuelve a visitar esta sección más tarde para participar en nuestras emocionantes dinámicas.</p>
            </div>
        @else
            @foreach($activeRaffles as $raffle)
            <div class="bg-stone-900 border border-stone-800 rounded-3xl p-8 hover:border-amber-500 transition-all flex flex-col">
                <span class="text-amber-500 text-xs font-bold uppercase tracking-widest mb-2">{{ $raffle->reference_lottery }}</span>
                <h4 class="text-2xl font-bold text-white mb-4">{{ $raffle->title }}</h4>
                
                <div class="space-y-3 mb-8 flex-grow">
                    <div class="flex justify-between text-stone-400 text-sm"><span>Premio:</span> <span class="text-white font-bold">${{ number_format($raffle->jackpot_prize, 0) }}</span></div>
                    <div class="flex justify-between text-stone-400 text-sm"><span>Precio:</span> <span class="text-white font-bold">${{ number_format($raffle->ticket_price, 0) }}</span></div>
                </div>

                <a href="{{ route('raffles.show', $raffle->id) }}" class="block text-center bg-stone-800 hover:bg-amber-500 hover:text-stone-950 text-white font-bold py-3 rounded-xl transition-all">
                    Participar Ahora
                </a>
            </div>
            @endforeach
        @endif
    </div>
</section>
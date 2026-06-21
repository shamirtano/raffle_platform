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
                <span class="text-amber-500 text-xs font-bold uppercase tracking-widest mb-2">JUEGA CON LA LOTERÍA DE {{ $raffle->reference_lottery }}</span>
                <h4 class="text-2xl font-bold text-white mb-4">{{ $raffle->title }}</h4>
            
                {{-- Dividir en 3 colunas: diseño 2 - 1 --}}
                <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12 items-start justify-between">
                    <div class="w-full lg:w-2/3 flex flex-col">                        
                        <p class="text-stone-400 text-sm mb-6">{{ $raffle->description }}</p>
                        <div class="space-y-3 mb-8 flex-grow">
                            <div class="flex justify-between text-stone-400 text-md mb-3"><span>Fecha Sorteo:</span> <span class="text-white font-bold">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d M Y') }}</span></div>
                            <div class="flex justify-between text-stone-400 text-md"><span>Modalidad:</span> <span class="text-white font-bold">{{ $raffle->is_cash_prize ? 'Premio en Dinero' : 'Premio en Artículo' }}</span></div>
                            <div class="flex justify-between text-stone-400 text-md"><span>Premio avaluado en:</span> <span class="text-white font-bold">$ {{ number_format($raffle->jackpot_prize, 2, ',', '.') }}</span></div>
                            <div class="flex justify-between text-stone-400 text-md"><span>Precio:</span> <span class="text-white font-bold">$ {{ number_format($raffle->ticket_price, 2, ',', '.') }}</span></div>
                        </div>                        
                    </div>
                    <div class="w-full lg:w-1/3 flex justify-center lg:justify-end">
                        <div class="relative max-w-[320px] w-full min-h-[300px] rounded-2xl overflow-hidden border border-stone-800 shadow-2xl">
                            <img src="{{ asset('storage/' . $raffle->image_path) }}" alt="{{ $raffle->title }}" class="w-full h-auto object-cover">
                        </div>
                    </div>
                </div>
                

                <a href="{{ route('raffles.show', $raffle->id) }}" class="block text-center bg-stone-800 hover:bg-amber-500 hover:text-stone-950 text-white font-bold py-3 rounded-xl transition-all">
                    Participar Ahora
                </a>
            </div>
            @endforeach
        @endif
    </div>
</section>
@extends('layouts.frontend')
@section('title', 'Sorteos Activos')

@section('content')
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
                    <div class="bg-stone-800 border border-stone-700 rounded-3xl p-8">
                        <h4 class="text-xl font-bold text-white mb-2">{{ $raffle->name }}</h4>
                        <p class="text-stone-400 text-sm">{{ $raffle->description }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </section>
@endsection
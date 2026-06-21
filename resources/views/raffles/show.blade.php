@extends('layouts.frontend')
@section('title', $raffle->title)

@section('content')
<div class="max-w-5xl mx-auto py-12 px-6">
    <div class="bg-stone-900 border border-stone-800 rounded-3xl p-8 mb-8">
        <h1 class="text-3xl font-black mb-2">{{ $raffle->title }}</h1>
        <p class="text-stone-400 text-sm">Premio: ${{ number_format($raffle->jackpot_prize) }} | Valor ticket: ${{ number_format($raffle->ticket_price) }}</p>
    </div>

    <!-- Tómbola interactiva -->
    <div class="bg-stone-900 p-6 rounded-3xl border border-stone-800">
        <div class="grid grid-cols-6 md:grid-cols-12 gap-2">
            @for ($i = 0; $i < $totalNumbers; $i++)
                @php $num = str_pad($i, $raffle->digits_count, '0', STR_PAD_LEFT); @endphp
                <button 
                    onclick="openModal('{{ $num }}')"
                    @if(in_array($num, $soldTickets)) 
                        disabled class="bg-stone-800 text-stone-600 cursor-not-allowed p-2 rounded text-xs font-mono"
                    @else 
                        class="bg-stone-700 hover:bg-amber-500 hover:text-stone-950 p-2 rounded text-xs font-mono transition-colors"
                    @endif>
                    {{ $num }}
                </button>
            @endfor
        </div>
    </div>
</div>

<!-- Modal sencillo (Alpine.js) -->
<div x-data="{ show: false, selectedNum: '' }" @open-modal.window="show = true; selectedNum = $event.detail">
    <div x-show="show" class="fixed inset-0 bg-black/80 flex items-center justify-center p-6" x-cloak>
        <div class="bg-stone-900 p-8 rounded-3xl border border-stone-800 max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4">¿Participar con el <span class="text-amber-500" x-text="selectedNum"></span>?</h3>
            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf
                <input type="hidden" name="ticket_number" x-model="selectedNum">
                <input type="text" name="customer_name" placeholder="Tu nombre" class="w-full bg-stone-950 p-3 rounded-lg mb-3">
                <button class="w-full bg-amber-500 py-3 rounded-lg font-bold">Confirmar Participación</button>
                <button type="button" @click="show = false" class="w-full mt-2 text-stone-500">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(num) { window.dispatchEvent(new CustomEvent('open-modal', { detail: num })); }
</script>
@endsection
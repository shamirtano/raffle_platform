@extends('layouts.frontend')
@section('title', $raffle->title)

@section('content')
<section id="participar" class="bg-stone-950 py-24 px-4 md:px-8 text-white">    
    
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12 items-start justify-between">
        
        <div class="w-full lg:w-2/3 space-y-5">
            <span class="bg-amber-500/10 text-amber-500 border border-amber-500/20 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">Sorteo Activo</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-white">{{ $raffle->title }}</h1>
            <p class="text-stone-400 text-lg leading-relaxed">{{ $raffle->description }}</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-y border-stone-800/80 py-6 my-6 text-sm">
                <div class="space-y-2">
                    <p class="text-stone-400">Disponibles: <span class="text-emerald-400 text-lg font-bold ml-1">{{ number_format($availableNumbersCount, 0, ',', '.') }}</span>                        
                        <span class="text-amber-500 font-semibold">
                            {{ $availableNumbersCount > 0 ? ($availableNumbersCount / $totalNumbers < 0.1 ? '¡Apúrate, quedan pocos números!' : 'Aún tienes oportunidad de participar.') : 'No hay más números disponibles.' }}
                        </span>
                    </p>
                    <p class="text-stone-400">Vendidos: <span class="text-red-400 text-lg font-bold ml-1">{{ number_format($soldNumbersCount, 0, ',', '.') }}</span>
                        <span class="text-amber-500 font-semibold">
                            Oportunidades 🎉
                        </span>
                    </p>
                    <p class="text-stone-300 font-medium">Valor por número: <span class="text-amber-400 text-base font-bold ml-1">${{ number_format($raffle->ticket_price, 0, ',', '.') }}</span></p>
                </div>
                <div class="space-y-2">
                    <p class="text-stone-400">Fecha Sorteo: <span class="text-white font-semibold ml-1">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d M Y') }}</span></p>
                    <p class="text-stone-400">Lotería Aliada: <span class="text-white font-semibold ml-1">{{ $raffle->reference_lottery }}</span></p>
                    <p class="text-stone-400">Cifras: <span class="text-white font-semibold ml-1">{{ $raffle->digits_count }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <a href="#" target="_blank" class="text-stone-400 hover:text-white transition-colors"><i class="bi bi-instagram text-xl"></i></a>
                <a href="#" target="_blank" class="text-stone-400 hover:text-white transition-colors"><i class="bi bi-facebook text-xl"></i></a>
                <a href="#" target="_blank" class="text-stone-400 hover:text-white transition-colors"><i class="bi bi-tiktok text-xl"></i></a>
                <span class="text-stone-600 font-semibold text-xs ml-auto">Organiza: Dinámicas El Palomo Negro</span>
            </div>
        </div>
        
        <div class="w-full lg:w-1/3 flex justify-center lg:justify-end">
            <div class="relative max-w-[320px] w-full min-h-[300px] rounded-2xl overflow-hidden border border-stone-800 shadow-2xl">
                <img src="{{ asset('storage/' . $raffle->image_path) }}" alt="{{ $raffle->title }}" class="w-full h-auto object-cover">
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto mt-12 bg-stone-900/60 border border-stone-800/60 rounded-3xl p-6 space-y-8">
        <div>
            <h3 class="text-xl font-bold tracking-tight text-white mb-1">Configura tu forma de juego</h3>
            <p class="text-stone-500 text-sm">Selecciona tu paquete de boletas antes de proceder a elegir tus números.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-400">1. Selecciona tu combo obligatorio</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($packageMultiples as $quantity)
                        <button type="button" onclick="selectCombo({{ $quantity }})" id="btn-combo-{{ $quantity }}"
                                class="btn-combo border border-stone-800 bg-stone-950 rounded-xl p-3 text-center transition-all hover:border-amber-500">
                            <span class="text-[10px] font-bold uppercase tracking-wider block text-stone-500">Combo</span>
                            <span class="text-2xl font-black text-white block my-0.5">{{ $quantity }}</span>
                            <span class="text-[11px] text-stone-400 block">{{ $quantity == 1 ? 'Número' : 'Números' }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-400">2. ¿Cómo vas a elegir tus números?</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button" onclick="setPlayMode('manual')" id="btn-mode-manual" disabled
                            class="btn-mode opacity-40 border border-stone-800 bg-stone-950 rounded-xl p-3 flex items-center gap-3 text-left transition-all">
                        <div class="text-amber-500 text-xl"><i class="bi bi-hand-index-thumb"></i></div>
                        <div>
                            <p class="font-bold text-sm text-white">Manual</p>
                            <p class="text-[11px] text-stone-500">Elegir en el tablero</p>
                        </div>
                    </button>
                    <button type="button" onclick="setPlayMode('random')" id="btn-mode-random" disabled
                            class="btn-mode opacity-40 border border-stone-800 bg-stone-950 rounded-xl p-3 flex items-center gap-3 text-left transition-all">
                        <div class="text-amber-500 text-xl"><i class="bi bi-lightning-charge"></i></div>
                        <div>
                            <p class="font-bold text-sm text-white">Aleatorio</p>
                            <p class="text-[11px] text-stone-500">Generación rápida</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-stone-900 transition-all opacity-30 pointer-events-none" id="grid-section">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Selecciona tus números en el tablero</h2>
                <p class="text-stone-500 text-sm mt-1" id="grid-helper-text">Por favor elige tu combo primero.</p>
            </div>
            
            <button type="button" id="btn-trigger-random" onclick="generateRandomNumbers()"
                    class="hidden bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold px-5 py-2.5 rounded-xl text-sm transition-all items-center gap-2">
                <i class="bi bi-arrow-clockwise"></i> Generar Números Aleatorios
            </button>

            <div class="w-full sm:w-72" id="search-container">
                <input type="text" id="search-number" 
                    class="w-full bg-stone-900 border border-stone-800 rounded-xl px-4 py-3 text-white placeholder:text-stone-600 focus:outline-none focus:border-emerald-500 transition-colors text-sm"
                    placeholder="Buscar tu número de la suerte...">
            </div>
        </div>

        <div id="numbers-grid" class="raffle-grid-container">
            @foreach(range(0, $totalNumbers - 1) as $number)
                @php
                    $formatted = str_pad($number, $raffle->digits_count, '0', STR_PAD_LEFT);
                    $isSold = in_array($formatted, $soldTickets ?? []);
                    $isPending = in_array($formatted, $pendingTickets ?? []);
                @endphp
                
                <div 
                    class="number-item raffle-number-box 
                        @if($isSold) number-sold 
                        @elseif($isPending) number-pending 
                        @else number-available @endif"
                    data-number="{{ $formatted }}"
                    onclick="toggleNumber(this)">
                    {{ $formatted }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="max-w-3xl mx-auto mt-16 bg-stone-900/40 border border-stone-900 rounded-3xl p-8 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Completar Solicitud</h2>
            <span id="countdown-timer" class="text-xs font-semibold text-amber-500 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full hidden">Esta reserva expira en 15:00</span>
        </div>
        
        <form action="{{ route('ticket-orders.store') }}" method="POST" id="buy-form">
            @csrf
            <input type="hidden" name="raffle_id" value="{{ $raffle->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Nombre completo</label>
                    <input type="text" name="customer_name" required class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">WhatsApp</label>
                    <input type="tel" name="customer_phone" required class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-emerald-500 transition-colors" placeholder="3XXXXXXXXX">
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-400 mb-2">Tus números seleccionados</label>
                <input type="text" name="ticket_numbers" id="ticket_numbers_input" 
                    class="w-full bg-stone-950 border border-stone-800 text-amber-400 rounded-xl px-4 py-3.5 font-mono font-bold tracking-widest text-base focus:outline-none cursor-not-allowed" readonly required placeholder="Selecciona números de la grilla superior">
            </div>

            <div class="mt-8 pt-6 border-t border-stone-900 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="text-left w-full sm:w-auto">
                    <p class="text-xs text-stone-500 uppercase font-semibold">Total a pagar</p>
                    <p class="text-2xl font-black text-white" id="display-total">$0</p>
                </div>
                <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-8 py-4 rounded-xl transition-all shadow-lg shadow-emerald-900/20 text-sm">
                    Reservar Números
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    let selectedNumbers = [];
    let requiredCount = 0;
    let playMode = '';
    const ticketPrice = {{ $raffle->ticket_price }};
    let timerInterval = null;

    // Seleccionar Combo o Paquete
    function selectCombo(quantity) {
        requiredCount = quantity;
        selectedNumbers = [];
        playMode = '';
        
        document.getElementById('ticket_numbers_input').value = '';
        document.getElementById('display-total').innerText = '$0';
        
        // Limpiar clases de botones combo
        document.querySelectorAll('.btn-combo').forEach(btn => btn.classList.remove('border-amber-500', 'bg-amber-500/10'));
        document.getElementById(`btn-combo-${quantity}`).classList.add('border-amber-500', 'bg-amber-500/10');
        
        // Activar botones de modo
        document.querySelectorAll('.btn-mode').forEach(btn => {
            btn.removeAttribute('disabled');
            btn.classList.remove('opacity-40', 'border-amber-500', 'bg-amber-500/10');
        });

        // Ocultar grilla por seguridad hasta elegir el modo
        document.getElementById('grid-section').classList.add('opacity-30', 'pointer-events-none');
        clearBoardSelection();
    }

    // Configurar Modo de Juego (Manual o Aleatorio)
    function setPlayMode(mode) {
        playMode = mode;
        selectedNumbers = [];
        document.getElementById('ticket_numbers_input').value = '';
        document.getElementById('display-total').innerText = '$0';
        clearBoardSelection();

        document.querySelectorAll('.btn-mode').forEach(btn => btn.classList.remove('border-amber-500', 'bg-amber-500/10'));
        document.getElementById(`btn-mode-${mode}`).classList.add('border-amber-500', 'bg-amber-500/10');

        const gridSection = document.getElementById('grid-section');
        gridSection.classList.remove('opacity-30', 'pointer-events-none');

        const helperText = document.getElementById('grid-helper-text');
        const searchContainer = document.getElementById('search-container');
        const randomBtn = document.getElementById('btn-trigger-random');

        if (mode === 'manual') {
            helperText.innerText = `Haz clic hasta elegir exactamente ${requiredCount} números libres en el tablero.`;
            searchContainer.classList.remove('hidden');
            randomBtn.classList.add('hidden');
        } else {
            helperText.innerText = `Presiona el botón para autogenerar tus ${requiredCount} números disponibles de forma aleatoria.`;
            searchContainer.classList.add('hidden');
            randomBtn.classList.remove('hidden');
            generateRandomNumbers(); // Autogenerar la primera tanda
        }
    }

    // Limpia la grilla visualmente
    function clearBoardSelection() {
        document.querySelectorAll('.number-item').forEach(item => item.classList.remove('number-selected'));
    }

    // FUNCIÓN MODIFICADA: Generar Números Aleatorios y hacer Scroll al Formulario
    function generateRandomNumbers() {
        clearBoardSelection();
        selectedNumbers = [];

        // Extraemos todos los cuadros que tengan la clase nativa de disponibles
        let availableBoxes = Array.from(document.querySelectorAll('.number-available'));
        
        if (availableBoxes.length < requiredCount) {
            Swal.fire('Atención', 'No hay suficientes números libres para este combo en la rifa.', 'warning');
            return;
        }

        // Mezclamos el array aleatoriamente
        availableBoxes.sort(() => 0.5 - Math.random());
        let selectedBoxes = availableBoxes.slice(0, requiredCount);

        selectedBoxes.forEach(box => {
            const num = box.dataset.number;
            selectedNumbers.push(num);
            box.classList.add('number-selected');
        });

        updateFormOutputs();

        // SCROLL ANIMADO AL FORMULARIO Y ENFOQUE AL INPUT
        const inputNombre = document.querySelector('input[name="customer_name"]');
        if (inputNombre) {
            inputNombre.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                inputNombre.focus();
            }, 600); // Espera a que termine la animación del scroll para hacer el focus
        }
    }

    function toggleNumber(el) {
        if (el.classList.contains('number-sold') || el.classList.contains('number-pending') || playMode === 'random') return;

        if (requiredCount === 0) {
            Swal.fire('Paso Requerido', 'Por favor selecciona un combo obligatorio en la parte superior.', 'info');
            return;
        }

        const number = el.dataset.number;

        if (selectedNumbers.includes(number)) {
            selectedNumbers = selectedNumbers.filter(n => n !== number);
            el.classList.remove('number-selected');
        } else {
            if (selectedNumbers.length >= requiredCount) {
                Swal.fire('Combo Completo', `Tu paquete seleccionado es de ${requiredCount} boletas. Desmarca un número si deseas cambiarlo.`, 'warning');
                return;
            }
            selectedNumbers.push(number);
            el.classList.add('number-selected');
        }

        updateFormOutputs();
    }

    // Sincroniza los totales y activa o detiene el temporizador original
    function updateFormOutputs() {
        document.getElementById('ticket_numbers_input').value = selectedNumbers.join(', ');
        const total = selectedNumbers.length * ticketPrice;
        document.getElementById('display-total').innerText = '$' + total.toLocaleString('es-CO');

        const timerBadge = document.getElementById('countdown-timer');
        if (selectedNumbers.length > 0) {
            timerBadge.classList.remove('hidden');
            if(!timerInterval) startTimer(15 * 60);
        } else {
            timerBadge.classList.add('hidden');
            clearInterval(timerInterval);
            timerInterval = null;
        }

        // Actualizar texto dinámico con los faltantes si está en modo manual
        if (playMode === 'manual') {
            const remaining = requiredCount - selectedNumbers.length;
            document.getElementById('grid-helper-text').innerText = remaining > 0 
                ? `Te faltan seleccionar ${remaining} números para completar tu paquete.` 
                : `¡Paquete completado perfectamente! Procede a completar el formulario.`;
        }
    }

    function startTimer(duration) {
        let timer = duration, minutes, seconds;
        clearInterval(timerInterval);
        timerInterval = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            document.getElementById('countdown-timer').innerText = "Esta reserva expira en " + minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(timerInterval);
                Swal.fire({
                    title: '¡Tiempo Expirado!',
                    text: 'El tiempo de reserva ha finalizado. Por favor selecciona tus números de nuevo.',
                    icon: 'warning',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    window.location.reload();
                });
            }
        }, 1000);
    }

    // INTERCEPCIÓN DEL FORMULARIO CON VALIDACIÓN ESTRICTA DEL COMBO COMPLETO
    document.getElementById('buy-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (requiredCount === 0) {
            Swal.fire('Atención', 'Por favor selecciona un combo obligatorio primero.', 'warning');
            return;
        }

        if (selectedNumbers.length !== requiredCount) {
            Swal.fire('Paquete Incompleto', `Debes elegir exactamente los ${requiredCount} números correspondientes a tu combo seleccionado. Llevas: ${selectedNumbers.length}.`, 'warning');
            return;
        }

        Swal.fire({
            title: 'Procesando Reserva',
            text: 'Guardando tus números en el sistema...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const formData = new FormData(this);
            const csrfToken = formData.get('_token');

            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                Swal.fire({
                    title: '🎉 ¡Pedido Creado! 🎉',
                    html: `Hola <b>${formData.get('customer_name')}</b>, tus números han sido reservados con éxito.<br><br>Un asesor validará tu pago y te enviará el comprobante oficial por WhatsApp.`,
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'No se pudo reservar',
                    text: result.message || 'Uno de los números seleccionados ya fue tomado por otro usuario.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            Swal.fire({
                title: 'Error de Comunicación',
                text: 'Hubo un problema al conectar con el servidor. Por favor, intenta de nuevo.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }
    });

    // Buscador interactivo original
    document.getElementById('search-number').addEventListener('input', function() {
        const term = this.value.trim();
        document.querySelectorAll('.number-item').forEach(item => {
            if (term === '' || item.dataset.number.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush
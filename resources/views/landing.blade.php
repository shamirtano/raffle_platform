<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinamicas y Eventos El Palomo Negro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-stone-900 text-stone-100 font-sans selection:bg-amber-500 selection:text-stone-950" x-data="{ cookieModal: true, currentTab: 'manual' }">

    <x-alerts />

    <!-- BANNER DE COOKIES (Profesionalismo & Legal) -->
    <div x-show="cookieModal" class="fixed bottom-5 left-5 right-5 md:max-w-md bg-stone-950/95 border border-stone-800 p-5 rounded-2xl shadow-2xl z-50 flex flex-col gap-3 backdrop-blur-md" style="display: none;">
        <p class="text-xs text-stone-400 leading-relaxed">
            Utilizamos cookies propias para garantizar la mejor experiencia en nuestro portal de reservas y dinámicas de sorteos. Al continuar navegando, aceptas nuestra <a href="#politicas" @click="cookieModal = false" class="text-amber-500 underline">Política de Privacidad</a>.
        </p>
        <button @click="cookieModal = false" class="bg-amber-500 text-stone-950 text-xs font-bold py-2 px-4 rounded-lg self-end hover:bg-amber-400 transition-all">Aceptar</button>
    </div>

    <!-- BOTÓN FLOTANTE WHATSAPP (Listo para integración de Bot/API) -->
    <a href="https://api.whatsapp.com/send?phone=573000000000&text=Hola!%20Me%20gustaría%20obtener%20información%20sobre%20las%20reservaciones%20o%20el%20módulo%20de%20participación." 
       target="_blank" 
       class="fixed bottom-6 right-6 bg-emerald-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-2xl shadow-2xl z-50 hover:bg-emerald-400 hover:scale-110 transition-all group"
       title="Chatea con nuestro Asistente Virtual">
        <i class="bi bi-whatsapp"></i>
        <span class="absolute right-16 bg-stone-950 text-white text-xs px-3 py-1.5 rounded-lg border border-stone-800 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">¿Necesitas ayuda?</span>
    </a>

    <!-- MENÚ DE NAVEGACIÓN -->
    <nav class="fixed top-0 inset-x-0 bg-stone-950/80 backdrop-blur-md border-b border-stone-800/40 z-40 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <img src="{{ asset('logo_palomo.jpg') }}" alt="El Palomo Negro Logo" class="h-11 object-contain">
            <div class="hidden md:flex gap-6 text-xs uppercase tracking-wider font-semibold text-stone-400">
                <a href="#hero" class="hover:text-amber-500 transition-colors">Inicio</a>
                <a href="#unidades" class="hover:text-amber-500 transition-colors">Instalaciones</a>
                <a href="#participar" class="hover:text-amber-500 transition-colors">Sorteos</a>
                <a href="#reservar" class="hover:text-amber-500 transition-colors">Reservas</a>
                <a href="#opiniones" class="hover:text-amber-500 transition-colors">Reseñas</a>
            </div>
            <a href="#reservar" class="bg-amber-500 hover:bg-amber-400 text-stone-950 text-xs font-bold px-4 py-2 rounded-lg transition-all">Reservar Mesa</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="hero" class="relative h-screen flex items-center justify-center bg-black overflow-hidden pt-16">
        <div class="absolute inset-0 bg-cover bg-center opacity-25 scale-105" style="background-image: url('https://images.unsplash.com/photo-1534447677768-be436bb09401?q=80&w=1920');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-transparent to-black"></div>
        
        <div class="relative z-10 text-center px-4 max-w-4xl mt-8 pt-12">
            <img src="{{ asset('logo_palomo.jpg') }}" alt="Logo Principal" class="h-64 mt-8 mx-auto mb-6 object-contain filter drop-shadow-[0_5px_15px_rgba(217,119,6,0.2)]">
            <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4 uppercase">Experiencias Exclusivas de Campo</h2>
            <p class="text-stone-400 text-base md:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">Disfruta de la mejor gastronomía criolla en nuestro restaurante, relájate en las piscinas, adquiere tu lote campestre ideal y participa en nuestras dinámicas de sorteo de fondos.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#reservar" class="bg-amber-500 text-stone-950 px-8 py-3.5 rounded-full font-bold text-sm shadow-xl hover:bg-amber-400 transition-all">Agendar Espacio / Mesa</a>
                <a href="#participar" class="bg-stone-800 text-stone-200 border border-stone-700 px-8 py-3.5 rounded-full font-bold text-sm hover:bg-stone-700 transition-all">Ver Sorteos Activos</a>
            </div>
        </div>
    </section>

    <!-- DETALLE PROFESIONAL DE INSTALACIONES -->
    <section id="unidades" class="py-24 px-6 max-w-7xl mx-auto border-t border-stone-800/40">
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
    </section>

    <!-- FORMULARIO DE RESERVACIÓN DE MESAS Y ESPACIOS -->
    <section id="reservar" class="bg-stone-950 py-24 px-6 border-t border-stone-800/40">
        <div class="max-w-xl mx-auto bg-stone-900 border border-stone-800 p-8 rounded-3xl shadow-2xl">
            <h3 class="text-2xl font-bold text-white text-center mb-1">Reservar Espacio o Mesa</h3>
            <p class="text-stone-400 text-xs text-center mb-6">Asegura tu comodidad y la de tu familia con anticipación</p>
            
            <form action="{{ route('reservations.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase text-stone-400 mb-1">Tu Nombre</label>
                    <input type="text" name="customer_name" required class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3 text-white text-sm focus:border-amber-500 focus:ring-0">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-400 mb-1">Celular / Teléfono</label>
                        <input type="text" name="customer_phone" required class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3 text-white text-sm focus:border-amber-500 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-400 mb-1">¿Qué espacio deseas reservar?</label>
                        <select name="area" class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3 text-white text-sm focus:border-amber-500 focus:ring-0">
                            <option value="RESTAURANT">Mesa en el Restaurante</option>
                            <option value="POOL">Zonas de Piscina (Día de Sol)</option>
                            <option value="FAMILY_ZONE">Kiosko Familiar Privado</option>
                            <option value="EVENT_HALL">Salón de Eventos / Banquetes</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-400 mb-1">Fecha y Hora</label>
                        <input type="datetime-local" name="reservation_time" required class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3 text-white text-sm focus:border-amber-500 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-stone-400 mb-1">N° de Personas</label>
                        <input type="number" name="guests_count" min="1" max="50" required class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3 text-white text-sm text-center font-bold focus:border-amber-500 focus:ring-0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-stone-400 mb-1">Notas Especiales (Opcional)</label>
                    <textarea name="additional_notes" rows="2" placeholder="Ej: Silla para bebé, decoración de cumpleaños..." class="w-full bg-stone-950 border border-stone-800 rounded-xl px-4 py-3 text-white text-sm focus:border-amber-500 focus:ring-0"></textarea>
                </div>

                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-stone-950 font-black py-4 rounded-xl transition-all shadow-lg mt-2 uppercase tracking-wider text-xs">
                    <i class="bi bi-calendar-check-fill me-1"></i> Confirmar Mi Solicitud de Reserva
                </button>
            </form>
        </div>
    </section>

    <!-- SECCION DE RIFAS Y JUEGOS -->
    {{-- <section id="participar" class="bg-stone-950 py-24 px-6 border-t border-stone-800/40">
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
    </section> --}}
    <!-- SECCIÓN 2: SORTEOS DISPONIBLES (Vitrina) -->
    <section id="participar" class="bg-stone-950 py-24 px-6 border-t border-stone-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-black text-white">Sorteos de Financiación Activos</h2>
                <p class="text-stone-400 mt-2">Apoya nuestro fondo de desarrollo y gana premios extraordinarios.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($activeRaffles as $raffle)
                <div class="bg-stone-900 border border-stone-800 rounded-3xl p-8 hover:border-amber-500 transition-all flex flex-col group">
                    <div class="mb-4">
                        <span class="bg-stone-800 text-stone-400 text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full">{{ $raffle->reference_lottery }}</span>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-4 group-hover:text-amber-500 transition-colors">{{ $raffle->title }}</h4>
                    
                    <div class="space-y-3 mb-8 flex-grow">
                        <div class="flex justify-between text-stone-500 text-sm">
                            <span>Premio Mayor:</span> 
                            <span class="text-white font-bold">${{ number_format($raffle->jackpot_prize, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-stone-500 text-sm">
                            <span>Valor Número:</span> 
                            <span class="text-amber-500 font-bold">${{ number_format($raffle->ticket_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('raffles.show', $raffle->id) }}" class="block text-center bg-stone-800 hover:bg-amber-500 text-white hover:text-stone-950 font-bold py-3 rounded-xl transition-all">
                        Participar en este sorteo
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECCIÓN DE RESEÑAS / TESTIMONIOS -->
    <section id="opiniones" class="py-24 px-6 max-w-7xl mx-auto border-t border-stone-800/40">
        <div class="text-center mb-16">
            <span class="text-amber-500 text-xs font-bold uppercase tracking-widest block mb-2">Opiniones</span>
            <h3 class="text-3xl font-black text-white">Lo que dicen nuestros visitantes</h3>
            <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-3"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-stone-800/40 border border-stone-800 p-6 rounded-2xl">
                <div class="flex text-amber-500 gap-1 mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-stone-300 text-xs leading-relaxed mb-4">"La carne a la llanera del restaurante es espectacular. Fuimos en familia el domingo, los niños disfrutaron la piscina y el servicio fue impecable."</p>
                <span class="text-white font-bold text-xs block">- Alejandro G.</span>
            </div>
            <div class="bg-stone-800/40 border border-stone-800 p-6 rounded-2xl">
                <div class="flex text-amber-500 gap-1 mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-stone-300 text-xs leading-relaxed mb-4">"Excelente atención. Compré un combo aleatorio en su sorteo de la rifa y todo el proceso de registro fue transparente. Súper recomendado el sitio."</p>
                <span class="text-white font-bold text-xs block">- Mariana V.</span>
            </div>
            <div class="bg-stone-800/40 border border-stone-800 p-6 rounded-2xl">
                <div class="flex text-amber-500 gap-1 mb-3"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill-0"></i></div>
                <p class="text-stone-300 text-xs leading-relaxed mb-4">"Los lotes campestres tienen una vista increíble, ya apartamos el nuestro. Un ambiente muy familiar, tranquilo y conectado con la naturaleza."</p>
                <span class="text-white font-bold text-xs block">- Carlos H.</span>
            </div>
        </div>
    </section>

    <!-- FOOTER & LEGALES -->
    <footer id="politicas" class="bg-stone-950 border-t border-stone-800 py-16 px-6 text-stone-500 text-xs">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div>
                <img src="{{ asset('logo_palomo.jpg') }}" alt="Logo Footer" class="h-12 mb-4 object-contain">
                <p class="text-stone-400 leading-relaxed">Diversión, tranquilidad y transparencia en cada uno de nuestros servicios campestres y dinámicas.</p>
            </div>
            <div>
                <h5 class="text-white font-bold uppercase mb-4 tracking-wider">Enlaces Legales</h5>
                <ul class="space-y-2">
                    <li><a href="#politicas" class="hover:text-amber-500 transition-colors">Términos y Condiciones</a></li>
                    <li><a href="#politicas" class="hover:text-amber-500 transition-colors">Políticas de Privacidad</a></li>
                    <li><a href="#politicas" class="hover:text-amber-500 transition-colors">Tratamiento de Datos Personales</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-bold uppercase mb-4 tracking-wider">Contacto Directo</h5>
                <p class="text-stone-400 mb-1">📍 Km 12 Vía Campestre, Medellín, Colombia</p>
                <p class="text-stone-400">📧 contacto@palomonegro.com</p>
            </div>
            <div>
                <h5 class="text-white font-bold uppercase mb-4 tracking-wider">Nuestras Redes</h5>
                <div class="flex gap-4 text-lg text-stone-400">
                    <a href="https://instagram.com" target="_blank" class="hover:text-amber-500 transition-colors"><i class="bi bi-instagram"></i></a>
                    <a href="https://facebook.com" target="_blank" class="hover:text-amber-500 transition-colors"><i class="bi bi-facebook"></i></a>
                    <a href="https://tiktok.com" target="_blank" class="hover:text-amber-500 transition-colors"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto border-t border-stone-900 pt-8 text-center text-stone-600">
            &copy; {{ date('Y') }} Dinámicas y Eventos El Palomo Negro. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>
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
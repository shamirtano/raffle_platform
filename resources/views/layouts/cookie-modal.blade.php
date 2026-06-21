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
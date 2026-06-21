<x-alerts />

    <!-- BANNER DE COOKIES - desactivar si se aceptan las cookies -->
    <div x-data="{ show: !localStorage.getItem('cookies_accepted') }" x-show="show" class="fixed bottom-0 inset-x-0 bg-stone-950/90 backdrop-blur-md border-t border-stone-800/40 z-50 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-sm text-stone-400">
            🍪 Usamos cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestra <a href="#" class="text-amber-500 hover:underline">Política de Cookies</a>.
        </div>
        <button @click="localStorage.setItem('cookies_accepted', 'true'); show = false;" class="bg-amber-500 hover:bg-amber-400 text-stone-950 text-xs font-bold px-4 py-2 rounded-lg transition-all">
            Aceptar Cookies
        </button>
    </div>    

    <!-- BOTÓN FLOTANTE WHATSAPP (Listo para integración de Bot/API) -->
    <a href="https://api.whatsapp.com/send?phone=573000000000&text=Hola!%20Me%20gustaría%20obtener%20información%20sobre%20las%20reservaciones%20o%20el%20módulo%20de%20participación." 
       target="_blank" 
       class="fixed bottom-6 right-6 bg-emerald-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-2xl shadow-2xl z-50 hover:bg-emerald-400 hover:scale-110 transition-all group"
       title="Chatea con nuestro Asistente Virtual">
        <i class="bi bi-whatsapp"></i>
        <span class="absolute right-16 bg-stone-950 text-white text-xs px-3 py-1.5 rounded-lg border border-stone-800 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-xl">¿Necesitas ayuda?</span>
    </a>
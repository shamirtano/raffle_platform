<section id="galeria" class="bg-stone-900 py-24 px-6 border-t border-stone-800/40">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-amber-500 text-xs font-bold uppercase tracking-widest block mb-2">Ambiente</span>
            <h3 class="text-3xl font-black text-white">Galería de Experiencias</h3>
            <div class="h-1 w-12 bg-amber-500 mx-auto rounded-full mt-3"></div>
        </div>

        @if(!empty($gallery) && count($gallery) > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($gallery as $image)
                    <div class="group relative h-64 bg-stone-800 rounded-2xl overflow-hidden shadow-lg border border-stone-700/30">
                        <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                             style="background-image: url('{{ asset('storage/' . $image) }}');">
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <i class="bi bi-zoom-in text-white text-2xl"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-stone-500 text-center text-sm">Próximamente fotos de nuestras instalaciones.</p>
        @endif
    </div>
</section>
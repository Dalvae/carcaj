<section class="destacados mb-12 relative z-0 isolate">
    <?php
    $slides = get_field('slider') ?: [];
    $first_slide = !empty($slides) ? $slides[0] : null;
    $slider_data = array_map(function ($slide) {
        return [
            'imagen' => $slide['imagen'],
            'enlace' => esc_url($slide['enlace']),
            'titulo' => esc_html($slide['titulo']),
            'bajada' => wp_kses_post($slide['bajada']),
            'fecha' => esc_html($slide['fecha'])
        ];
    }, $slides);
    
    // Preload LCP image
    if ($first_slide && !empty($first_slide['imagen']['url'])) {
        add_action('wp_head', function() use ($first_slide) {
            echo '<link rel="preload" as="image" href="' . esc_url($first_slide['imagen']['url']) . '" fetchpriority="high">' . "\n";
        }, 1);
    }
    ?>
    <script>
        window.sliderData = <?php echo json_encode($slider_data); ?>;
    </script>
    <div x-data="{
           slides: window.sliderData,
           currentSlide: 0,
           intervalId: null,
           isInView: false,
           next() {
               if (!$store.header.isOpen && this.isInView) {
                   this.currentSlide = (this.currentSlide + 1) % this.slides.length
               }
           },
           prev() {
               if (!$store.header.isOpen && this.isInView) {
                   this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length
               }
           },
           goToSlide(index) {
               if (!$store.header.isOpen && this.isInView) {
                   this.currentSlide = index
               }
           },
           startAutoPlay() {
               if (!$store.header.isOpen && this.isInView) {
                   this.intervalId = setInterval(() => this.next(), 4000)
               }
           },
           stopAutoPlay() {
               if (this.intervalId) {
                   clearInterval(this.intervalId)
               }
           },
           observeIntersection() {
               const observer = new IntersectionObserver((entries) => {
                   entries.forEach(entry => {
                       this.isInView = entry.isIntersecting;
                       if (entry.isIntersecting) {
                           this.startAutoPlay();
                       } else {
                           this.stopAutoPlay();
                       }
                   });
               }, {
                   threshold: 0.2 // El slider debe estar al menos 20% visible
               });
               
               observer.observe(this.$el);
           }
       }"
        x-init="observeIntersection()"
        @mouseenter="stopAutoPlay()"
        @mouseleave="startAutoPlay()"
        class="relative isolation">
        <div class="relative lg:h-[60vh] h-[90vh] max-w-[1286px] mx-auto px-4 lg:px-0 lg:min-h-[450px]">
            <!-- Slider container -->
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full"
                    class="absolute inset-0 will-change-transform">
                    <div class="flex flex-col lg:flex-row items-center w-full py-4">
                        <!-- Contenedor del slider con las flechas -->
                        <div class="w-full lg:w-1/2 lg:ml-44 flex justify-center items-center relative">
                            <!-- Flechas -->
                            <div class="absolute inset-0 pointer-events-none hidden lg:block">
                                <!-- Flecha superior (más arriba) -->
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                    width="434" height="139" loading="lazy"
                                    class="absolute z-10 top-[20%] left-[25%] transform -translate-x-1/2 -translate-y-1/2" alt="">

                                <!-- Flecha del medio (centrada) -->
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                    width="434" height="139" loading="lazy"
                                    class="absolute z-10 top-1/2 left-[20%] transform -translate-x-1/2 -translate-y-1/2" alt="">

                                <!-- Flecha inferior (más abajo) -->
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                    width="434" height="139" loading="lazy"
                                    class="absolute z-10 bottom-[20%] left-[25%] transform -translate-x-1/2 translate-y-1/2" alt="">
                            </div>

                            <!-- Círculo de la imagen -->
                            <a :href="slide.enlace" :aria-label="'Ver especial: ' + slide.titulo" class="rounded-full overflow-hidden block relative w-full max-w-[450px] aspect-square">
                                <img
                                    :src="slide.imagen.url"
                                    :alt="slide.imagen.alt"
                                    :fetchpriority="index === 0 ? 'high' : 'low'"
                                    :loading="index === 0 ? 'eager' : 'lazy'"
                                    class="absolute inset-0 z-20 w-full h-full object-cover">
                            </a>
                        </div>
                        <!-- Texto y contenido -->
                        <div class="w-full lg:w-1/2 lg:-ml-44 p-4 lg:p-6 z-30">
                            <h2 class="lg:text-4xl text-xl font-bold text-center lg:text-left">
                                <a :href="slide.enlace" x-text="slide.titulo" class="bg-rojo text-white px-2 leading-normal"></a>
                            </h2>
                            <div class="mt-6 bg-white shadow-md p-4 lg:p-5 lg:mr-20">
                                <p x-html="slide.bajada" class="text-lg text-black font-medium"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Navigation Buttons -->
        <button @click="prev"
            aria-label="Slide anterior"
            class="absolute z-40 left-4 top-1/2 -translate-y-1/2 text-red-600 hover:bg-white p-2 rounded-full shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click="next"
            aria-label="Siguiente slide"
            class="absolute z-40 right-4 top-1/2 -translate-y-1/2 text-red-600 hover:bg-white p-2 rounded-full shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</section>
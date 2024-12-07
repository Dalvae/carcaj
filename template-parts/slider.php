<section class="destacados mb-12">
    <?php
    $slides = get_field('slider') ?: [];
    $slider_data = array_map(function ($slide) {
        return [
            'imagen' => $slide['imagen'],
            'enlace' => esc_url($slide['enlace']),
            'titulo' => esc_html($slide['titulo']),
            'bajada' => wp_kses_post($slide['bajada']),
            'fecha' => esc_html($slide['fecha'])
        ];
    }, $slides);
    ?>
    <script>
        window.sliderData = <?php echo json_encode($slider_data); ?>;
    </script>

    <div x-data="{
           slides: window.sliderData,
           currentSlide: 0,
           intervalId: null,
           next() {
               this.currentSlide = (this.currentSlide + 1) % this.slides.length
           },
           prev() {
               this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length
           },
           goToSlide(index) {
               this.currentSlide = index
           },
           startAutoPlay() {
               this.intervalId = setInterval(() => this.next(), 4000)
           },
           stopAutoPlay() {
               if (this.intervalId) {
                   clearInterval(this.intervalId)
               }
           }
       }"
        x-init="startAutoPlay()"
        @mouseenter="stopAutoPlay()"
        @mouseleave="startAutoPlay()"
        class="relative ">

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
                    class="absolute inset-0">
                    <div class="flex flex-col lg:flex-row items-center w-full py-4">
                        <!-- Contenedor del slider con las flechas -->
                        <div class="w-full lg:w-1/2 lg:ml-44 flex justify-center items-center relative">
                            <!-- Flechas -->
                            <div class="absolute inset-0 pointer-events-none hidden lg:block">
                                <!-- Flecha superior (más arriba) -->
                                <img src="<?php bloginfo('template_url'); ?>/img/flechahorizontal.webp"
                                    class="absolute z-10 top-[20%] left-[25%] transform -translate-x-1/2 -translate-y-1/2" alt="Flecha Superior">

                                <!-- Flecha del medio (centrada) -->
                                <img src="<?php bloginfo('template_url'); ?>/img/flechahorizontal.webp"
                                    class="absolute z-10 top-1/2 left-[20%] transform -translate-x-1/2 -translate-y-1/2" alt="Flecha Central">

                                <!-- Flecha inferior (más abajo) -->
                                <img src="<?php bloginfo('template_url'); ?>/img/flechahorizontal.webp"
                                    class="absolute z-10 bottom-[20%] left-[25%] transform -translate-x-1/2 translate-y-1/2" alt="Flecha Inferior">
                            </div>

                            <!-- Círculo de la imagen -->
                            <a :href="slide.enlace" class="rounded-full overflow-hidden block relative w-full max-w-[450px] aspect-square">
                                <img
                                    :src="slide.imagen.url"
                                    :alt="slide.imagen.alt"
                                    class="absolute inset-0 z-20 w-full h-full object-cover">
                            </a>
                        </div>
                        <!-- Texto y contenido -->
                        <div class="w-full lg:w-1/2 lg:-ml-44 p-4 lg:p-6 z-30">
                            <h2 class="lg:text-4xl text-xl font-bold text-center lg:text-left">
                                <a :href="slide.enlace" x-text="slide.titulo" class="bg-rojo text-white px-2 leading-normal"></a>
                            </h2>
                            <div class="mt-6 bg-white shadow-md p-4 lg:p-5 lg:mr-20">
                                <p x-html="slide.bajada" class="text-lg text-black"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Navigation Buttons -->
        <button @click="prev"
            class="absolute z-40 left-4 top-1/2 -translate-y-1/2 text-red-600 hover:bg-white p-2 rounded-full shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click="next"
            class="absolute z-40 right-4 top-1/2 -translate-y-1/2 text-red-600 hover:bg-white p-2 rounded-full shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</section>
<section class="destacados mb-12 relative z-0 isolate">
    <?php
    $slides = get_field('slider') ?: [];
    $first_slide = !empty($slides) ? $slides[0] : null;
    
    // Build srcset for first slide (server-side)
    $first_slide_srcset = '';
    $first_slide_src = '';
    if ($first_slide) {
        $image = $first_slide['imagen'];
        $sizes = $image['sizes'] ?? [];
        
        $srcset_parts = [];
        if (!empty($sizes['medium'])) {
            $srcset_parts[] = $sizes['medium'] . ' ' . $sizes['medium-width'] . 'w';
        }
        if (!empty($sizes['medium_large'])) {
            $srcset_parts[] = $sizes['medium_large'] . ' ' . $sizes['medium_large-width'] . 'w';
        }
        if (!empty($sizes['large'])) {
            $srcset_parts[] = $sizes['large'] . ' ' . $sizes['large-width'] . 'w';
        }
        if (!empty($sizes['slider'])) {
            $srcset_parts[] = $sizes['slider'] . ' ' . $sizes['slider-width'] . 'w';
        }
        $srcset_parts[] = $image['url'] . ' ' . $image['width'] . 'w';
        
        $first_slide_srcset = implode(', ', $srcset_parts);
        $first_slide_src = $sizes['medium_large'] ?? $sizes['large'] ?? $image['url'];
    }
    
    // Prepare data for remaining slides (Alpine.js)
    $remaining_slides = array_slice($slides, 1);
    $slider_data = array_map(function ($slide) {
        $image = $slide['imagen'];
        $sizes = $image['sizes'] ?? [];
        
        $srcset_parts = [];
        if (!empty($sizes['medium'])) {
            $srcset_parts[] = $sizes['medium'] . ' ' . $sizes['medium-width'] . 'w';
        }
        if (!empty($sizes['medium_large'])) {
            $srcset_parts[] = $sizes['medium_large'] . ' ' . $sizes['medium_large-width'] . 'w';
        }
        if (!empty($sizes['large'])) {
            $srcset_parts[] = $sizes['large'] . ' ' . $sizes['large-width'] . 'w';
        }
        if (!empty($sizes['slider'])) {
            $srcset_parts[] = $sizes['slider'] . ' ' . $sizes['slider-width'] . 'w';
        }
        $srcset_parts[] = $image['url'] . ' ' . $image['width'] . 'w';
        
        return [
            'imagen' => [
                'url' => $image['url'],
                'alt' => $image['alt'] ?? '',
                'src_mobile' => $sizes['medium_large'] ?? $sizes['large'] ?? $image['url'],
                'srcset' => implode(', ', $srcset_parts),
            ],
            'enlace' => esc_url($slide['enlace']),
            'titulo' => esc_html($slide['titulo']),
            'bajada' => wp_kses_post($slide['bajada']),
            'fecha' => esc_html($slide['fecha'])
        ];
    }, $remaining_slides);
    
    // Total slides count for Alpine
    $total_slides = count($slides);
    ?>
    <script>
        window.sliderData = <?php echo json_encode($slider_data); ?>;
        window.totalSlides = <?php echo $total_slides; ?>;
    </script>
    <div x-data="{
           slides: window.sliderData,
           totalSlides: window.totalSlides,
           currentSlide: 0,
           intervalId: null,
           isInView: false,
           next() {
               if (!$store.header.isOpen && this.isInView) {
                   this.currentSlide = (this.currentSlide + 1) % this.totalSlides
               }
           },
           prev() {
               if (!$store.header.isOpen && this.isInView) {
                   this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides
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
                   threshold: 0.2
               });
               
               observer.observe(this.$el);
           }
       }"
        x-init="observeIntersection()"
        @mouseenter="stopAutoPlay()"
        @mouseleave="startAutoPlay()"
        class="relative isolation">
        <div class="relative lg:h-[60vh] h-[90vh] max-w-[1286px] mx-auto px-4 lg:px-0 lg:min-h-[450px]">
            
            <?php if ($first_slide): ?>
            <!-- First slide rendered server-side WITHOUT x-show for instant LCP -->
            <div x-show="currentSlide === 0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full"
                class="absolute inset-0 will-change-transform"
                style="display: block;">
                <div class="flex flex-col lg:flex-row items-center w-full py-4">
                    <!-- Image container with arrows -->
                    <div class="w-full lg:w-1/2 lg:ml-44 flex justify-center items-center relative">
                        <!-- Arrows -->
                        <div class="absolute inset-0 pointer-events-none hidden lg:block">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                width="434" height="139" loading="lazy"
                                class="absolute z-10 top-[20%] left-[25%] transform -translate-x-1/2 -translate-y-1/2" alt="">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                width="434" height="139" loading="lazy"
                                class="absolute z-10 top-1/2 left-[20%] transform -translate-x-1/2 -translate-y-1/2" alt="">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                width="434" height="139" loading="lazy"
                                class="absolute z-10 bottom-[20%] left-[25%] transform -translate-x-1/2 translate-y-1/2" alt="">
                        </div>

                        <!-- Circle image - LCP element rendered server-side -->
                        <a href="<?php echo esc_url($first_slide['enlace']); ?>" 
                           aria-label="Ver especial: <?php echo esc_attr($first_slide['titulo']); ?>" 
                           class="rounded-full overflow-hidden block relative w-full max-w-[450px] aspect-square">
                            <img
                                src="<?php echo esc_url($first_slide_src); ?>"
                                srcset="<?php echo esc_attr($first_slide_srcset); ?>"
                                sizes="(max-width: 768px) 100vw, 450px"
                                alt="<?php echo esc_attr($first_slide['imagen']['alt'] ?? ''); ?>"
                                fetchpriority="high"
                                loading="eager"
                                width="450"
                                height="450"
                                class="absolute inset-0 z-20 w-full h-full object-cover">
                        </a>
                    </div>
                    <!-- Text content -->
                    <div class="w-full lg:w-1/2 lg:-ml-44 p-4 lg:p-6 z-30">
                        <h2 class="lg:text-4xl text-xl font-bold text-center lg:text-left">
                            <a href="<?php echo esc_url($first_slide['enlace']); ?>" class="bg-rojo text-white px-2 leading-normal"><?php echo esc_html($first_slide['titulo']); ?></a>
                        </h2>
                        <div class="mt-6 bg-white shadow-md p-4 lg:p-5 lg:mr-20">
                            <p class="text-lg text-black font-medium"><?php echo wp_kses_post($first_slide['bajada']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Remaining slides via Alpine.js template -->
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index + 1"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full"
                    class="absolute inset-0 will-change-transform">
                    <div class="flex flex-col lg:flex-row items-center w-full py-4">
                        <!-- Image container with arrows -->
                        <div class="w-full lg:w-1/2 lg:ml-44 flex justify-center items-center relative">
                            <!-- Arrows -->
                            <div class="absolute inset-0 pointer-events-none hidden lg:block">
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                    width="434" height="139" loading="lazy"
                                    class="absolute z-10 top-[20%] left-[25%] transform -translate-x-1/2 -translate-y-1/2" alt="">
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                    width="434" height="139" loading="lazy"
                                    class="absolute z-10 top-1/2 left-[20%] transform -translate-x-1/2 -translate-y-1/2" alt="">
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/flechahorizontal.webp"
                                    width="434" height="139" loading="lazy"
                                    class="absolute z-10 bottom-[20%] left-[25%] transform -translate-x-1/2 translate-y-1/2" alt="">
                            </div>

                            <!-- Circle image -->
                            <a :href="slide.enlace" :aria-label="'Ver especial: ' + slide.titulo" class="rounded-full overflow-hidden block relative w-full max-w-[450px] aspect-square">
                                <img
                                    :src="slide.imagen.src_mobile"
                                    :srcset="slide.imagen.srcset"
                                    sizes="(max-width: 768px) 100vw, 450px"
                                    :alt="slide.imagen.alt"
                                    loading="lazy"
                                    width="450"
                                    height="450"
                                    class="absolute inset-0 z-20 w-full h-full object-cover">
                            </a>
                        </div>
                        <!-- Text content -->
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

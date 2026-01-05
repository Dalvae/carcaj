<section class="destacados mb-12 relative z-0 isolate">
    <?php
    $slides = get_field('slider') ?: [];
    $total_slides = count($slides);
    
    if (empty($slides)) return;
    
    // Helper function to build srcset
    function build_srcset($image) {
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
        
        return implode(', ', $srcset_parts);
    }
    ?>
    
    <div id="slider" class="relative" data-total="<?php echo $total_slides; ?>">
        <div class="relative lg:h-[60vh] h-[90vh] max-w-[1286px] mx-auto px-4 lg:px-0 lg:min-h-[450px]">
            
            <?php foreach ($slides as $index => $slide): 
                $image = $slide['imagen'];
                $sizes = $image['sizes'] ?? [];
                $src = $sizes['medium_large'] ?? $sizes['large'] ?? $image['url'];
                $srcset = build_srcset($image);
                $is_first = $index === 0;
            ?>
            <!-- Slide <?php echo $index; ?> -->
            <div class="slide absolute inset-0 transition-all duration-300 ease-out <?php echo $is_first ? 'opacity-100' : 'opacity-0 translate-x-full pointer-events-none'; ?>"
                 data-index="<?php echo $index; ?>"
                 <?php echo $is_first ? '' : 'aria-hidden="true"'; ?>>
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
                        <a href="<?php echo esc_url($slide['enlace']); ?>" 
                           aria-label="Ver especial: <?php echo esc_attr($slide['titulo']); ?>" 
                           class="rounded-full overflow-hidden block relative w-full max-w-[450px] aspect-square">
                            <img
                                src="<?php echo esc_url($src); ?>"
                                srcset="<?php echo esc_attr($srcset); ?>"
                                sizes="(max-width: 768px) 100vw, 450px"
                                alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
                                <?php if ($is_first): ?>
                                fetchpriority="high"
                                loading="eager"
                                <?php else: ?>
                                loading="lazy"
                                <?php endif; ?>
                                width="450"
                                height="450"
                                class="absolute inset-0 z-20 w-full h-full object-cover">
                        </a>
                    </div>
                    <!-- Text content -->
                    <div class="w-full lg:w-1/2 lg:-ml-44 p-4 lg:p-6 z-30">
                        <h2 class="lg:text-4xl text-xl font-bold text-center lg:text-left">
                            <a href="<?php echo esc_url($slide['enlace']); ?>" class="bg-rojo text-white px-2 leading-normal"><?php echo esc_html($slide['titulo']); ?></a>
                        </h2>
                        <div class="mt-6 bg-white shadow-md p-4 lg:p-5 lg:mr-20">
                            <p class="text-lg text-black font-medium"><?php echo wp_kses_post($slide['bajada']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Buttons -->
        <button id="slider-prev"
            aria-label="Slide anterior"
            class="absolute z-40 left-4 top-1/2 -translate-y-1/2 text-red-600 hover:bg-white p-2 rounded-full shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button id="slider-next"
            aria-label="Siguiente slide"
            class="absolute z-40 right-4 top-1/2 -translate-y-1/2 text-red-600 hover:bg-white p-2 rounded-full shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</section>

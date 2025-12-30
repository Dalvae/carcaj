<?php

/* PDF Viewer Shortcode using PDF.js
----------------------------------------------------------------------------------------------------
Usage: [pdf url="https://example.com/document.pdf" height="600"]
*/
add_shortcode('pdf', function ($atts) {
    $default = array(
        'url' => '',
        'height' => '600',
    );
    $atts = shortcode_atts($default, $atts);
    
    if (empty($atts['url'])) {
        return '<p class="text-red-500">Error: No se especificó URL del PDF</p>';
    }
    
    return '<div class="pdf-container my-8">
        <iframe 
            src="' . esc_url($atts['url']) . '" 
            width="100%" 
            height="' . esc_attr($atts['height']) . 'px"
            class="border border-gray-300 rounded-lg shadow-sm"
            allowfullscreen>
        </iframe>
    </div>';
});

/* `Example shortcode
----------------------------------------------------------------------------------------------------*/
//add_shortcode('button', function ($atts) {
//    $default = array(
//        'link' => '#',
//        'text' => 'My Button',
//        'class' => '',
//        'target' => ''
//    );
//    $atts = shortcode_atts($default, $atts);
//    return '<a class="btn ' . ($atts['class'] ? ' ' . $atts['class'] : '') . '" target="' . $atts['target'] . '" href="' . $atts['link'] . '">' . $atts['text'] . '</a>';
//});
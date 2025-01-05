<?php

/* `SVG` Support - Can be a security risk
----------------------------------------------------------------------------------------------------*/

add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

    global $wp_version;
    if ( $wp_version !== '4.7.1' ) {
        return $data;
    }

    $filetype = wp_check_filetype( $filename, $mimes );

    return [
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    ];

}, 10, 4 );

function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
    echo '<style type="text/css">
    .attachment-266x266, .thumbnail img {
        width: 100% !important;
        height: auto !important;
    }
</style>';
}
add_action( 'admin_head', 'fix_svg' );

// En functions.php o un lugar apropiado
function custom_printfriendly_button() {
    return '<a href="#" rel="nofollow" onclick="window.print(); return false;" class="inline-flex p-2">
        <svg class="w-12 h-12 stroke-current hover:stroke-dorado transition-colors duration-300" viewBox="0 0 24 24" fill="none">
            <path d="M4 4C4 3.44772 4.44772 3 5 3H14H14.5858C14.851 3 15.1054 3.10536 15.2929 3.29289L19.7071 7.70711C19.8946 7.89464 20 8.149 20 8.41421V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4Z" stroke-width="2" stroke-linecap="round"/>
            <path d="M20 8H15V3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11.5 13H11V17H11.5C12.6046 17 13.5 16.1046 13.5 15C13.5 13.8954 12.6046 13 11.5 13Z" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.5 17V13L17.5 13" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 15H17" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7 17L7 15.5M7 15.5L7 13L7.75 13C8.44036 13 9 13.5596 9 14.25V14.25C9 14.9404 8.44036 15.5 7.75 15.5H7Z" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>';
}
add_filter('printfriendly_button', 'custom_printfriendly_button');
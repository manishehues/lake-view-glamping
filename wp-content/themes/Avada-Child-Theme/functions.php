<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css'  );
    wp_enqueue_style( 'fontawesome-style', 'https://cdn.staticaly.com/gh/hung1001/font-awesome-pro/4cac1a6/css/all.css', array( 'fusion-dynamic-css' )  );
    wp_enqueue_style( 'slick-style', get_stylesheet_directory_uri() . '/assets/css/slick.css', array( 'fusion-dynamic-css' ) );
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/assets/css/custom.css', array( 'fusion-dynamic-css' ) );
    wp_enqueue_style( 'responisve-style', get_stylesheet_directory_uri() . '/assets/css/responsive.css', array( 'fusion-dynamic-css' ) );

    wp_enqueue_script( 'slick-slider-script',get_stylesheet_directory_uri() . '/assets/js/slick.min.js', array('jquery'));    
    wp_enqueue_script( 'global-script',get_stylesheet_directory_uri() . '/assets/js/global.js', array('jquery'));    
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 20 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );


add_filter( 'body_class', 'custom_body_class' );
function custom_body_class( array $classes ) {
    $new_class = null;
    if(isset($_REQUEST['is_page_room_extra']) && $_REQUEST['is_page_room_extra'] !=""){

        $new_class = 'extra_options';
        $classes[] = $new_class;
    }

    return $classes;
}


add_action('template_redirect', 'redirect_if_404');
function redirect_if_404() {
	
    if ( is_404() ) {
        // Remember to change the /path-to-go with the URL you like to redirect the users.
        // 301 is permanent redirect. 302 is Temporary redirect.
        wp_redirect(esc_url(home_url('/')), 301);
        // And here will stop the file execution.
        exit();
    }
}

add_shortcode('domes_gallery','hotel_booking_single_room_gallery1');

function hotel_booking_single_room_gallery1(){
    ob_start();
    do_action( 'hotel_booking_single_room_gallery' );
    return ob_get_clean();
}


add_shortcode('domes_price','hotel_booking_loop_room_price1');

function hotel_booking_loop_room_price1(){
    ob_start();
    do_action( 'hotel_booking_loop_room_price' );
    return ob_get_clean();
}
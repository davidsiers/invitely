<?php 

 function invitely_adding_scripts() {
    wp_register_script('custom', invitely_get_url("assets/js/custom.js") );
    wp_enqueue_script('custom');

    wp_register_script('qrcode', invitely_get_url("assets/js/qr/qrcode.min.js") );
    wp_enqueue_script('qrcode');
}
  
add_action( 'wp_enqueue_scripts', 'invitely_adding_scripts' );  


function invitely_adding_styles() {
    wp_register_style('style', invitely_get_url('assets/css/style.css') );
    wp_enqueue_style('style');

    wp_register_style('bootstrap', invitely_get_url('assets/css/bootstrap.min.css') );
    wp_enqueue_style('bootstrap');

    wp_register_style('font-awesome', invitely_get_url('assets/css/font-awesome.min.css') );
    wp_enqueue_style('font-awesome');
}

add_action( 'wp_enqueue_scripts', 'invitely_adding_styles' );  

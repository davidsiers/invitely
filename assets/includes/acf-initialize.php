<?php 
//expose ACF fields to Rest API 
//https://stackoverflow.com/questions/56473929/how-to-expose-all-the-acf-fields-to-wordpress-rest-api-in-both-pages-and-custom

if( (!class_exists('acf_pro')) || (!class_exists('acf')) ) {
    // 1. customize ACF path
    add_filter('acf/settings/path', 'my_acf_settings_path');
    function my_acf_settings_path( $path )
      {
        $path = invitely_get_path('assets/includes/acf/');
        return $path;
      } 
    // 2. customize ACF dir
    add_filter('acf/settings/dir', 'my_acf_settings_dir');
    function my_acf_settings_dir( $dir )
      {
        $dir = invitely_get_url('assets/includes/acf/');
      return $dir;
      }

    // 3. Hide ACF field group menu item
    // add_filter('acf/settings/show_admin', '__return_false');
    
    invitely_include('assets/includes/acf/acf.php');

}

<?php

add_filter('single_template', 'custom_post_type_template');

function custom_post_type_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'invites' ) {
        if ( file_exists( invitely_get_path('assets/templates/single-invites.php') ) ) {
            return invitely_get_path('assets/templates/single-invites.php');
        }
    } 

    return $single;
 
}

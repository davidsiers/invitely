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

    /* Checks for single template by post type */
    if ( $post->post_type == 'devotions' ) {
        if ( file_exists( invitely_get_path('assets/templates/single-devotions.php') ) ) {
            return invitely_get_path('assets/templates/single-devotions.php');
        }
    }

    if ( $post->post_type == 'ctc_event' ) {
        if ( file_exists( invitely_get_path('assets/templates/saved/content-event-full.php') ) ) {
            return invitely_get_path('assets/templates/saved/content-event-full.php');
        }
    } 

    if ( $post->post_type == 'prayers' ) {
        if ( file_exists( invitely_get_path('assets/templates/single-prayer.php') ) ) {
            return invitely_get_path('assets/templates/single-prayer.php');
        }
    } 
 
    return $single;
 
}

add_filter('archive_template', 'custom_post_type_archive_template');

function custom_post_type_archive_template($archive) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'prayers' ) {
        if ( file_exists( invitely_get_path('assets/templates/archive-prayers.php') ) ) {
            return invitely_get_path('assets/templates/archive-prayers.php');
        }
    }
 
    return $archive;

}

add_action('init', function() {
    $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
    if ( $url_path === 'devotion' ) {

       add_filter( 'template_include', function() {
        return invitely_include('assets/templates/single-devotions.php');
    });

    }
  });

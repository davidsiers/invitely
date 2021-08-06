<?php
/**
 *
 * WP Rest API Encouragement Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/encouragements', array(
        'methods' => 'GET',
        'callback' => 'get_encouragements',
      ) );

    register_rest_route( 'invitely/v1', '/encouragements/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_encouragement_by_id',
    ) );

});

function get_encouragements($request) {
 
    $encourage_args = array(
        'numberposts' => 10,
        'post_type'   => 'encouragements',
        'paged' => ($_REQUEST['page'] ? $_REQUEST['page'] : 1)
    );
    
    $encouragements = get_posts( $encourage_args );
    $i = 0;
  
    foreach($encouragements as $encourage) {
      $encourage->featured_image = get_the_post_thumbnail_url( $encourage->ID, 'full' );
      $encourage->post_content = wpautop($encourage->post_content);
      $fields = get_fields($encourage->ID);
      foreach( $fields as $name => $value ) {
        $encourage->$name = $value;
      }
      $i++;
    }
    
    if (empty($encouragements)) {
    return new WP_Error( 'empty_encouragements', 'there are no encouragements', array('status' => 404) );
    }
    
    wp_get_nocache_headers();
    
    $response = new WP_REST_Response($encouragements);
    $response->set_status(200);
    $response->set_headers(array('Cache-Control' => 'no-cache, must-revalidate, max-age=0'));
    return $response;
};

function get_encouragement_by_id($request) {
    $encouragement = get_post($request['id']);
    $encouragement->featured_image = get_the_post_thumbnail_url( $encouragement->ID, 'full' );

    $fields = get_fields($encouragement->ID);
    foreach( $fields as $name => $value ) {
      $encouragement->$name = $value;
    }

    if (empty($encouragement)) {
    return new WP_Error( 'empty_encouragement', 'there is no encouragement with that ID', array('status' => 404) );
    }

    $response = new WP_REST_Response($encouragement);
    $response->set_status(200);
    $response->set_headers(array('Cache-Control' => 'no-cache'));
    return $response;
};

<?php
/**
 *
 * WP Rest API Devotion Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/devotions', array(
        'methods' => 'GET',
        'callback' => 'get_devotions',
      ) );

    register_rest_route( 'invitely/v1', '/devotions/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'get_devotion_by_id',
    ) );

});

function get_devotions($request) {
 
    $devotion_args = array(
        'numberposts' => 1,
        'post_type'   => 'devotions',
        'paged' => ($_REQUEST['page'] ? $_REQUEST['page'] : 1)
    );
    
    $devotions = get_posts( $devotion_args );
    $i = 0;
  
    foreach($devotions as $devotion) {
      $devotion->featured_image = get_the_post_thumbnail_url( $devotion->ID, 'full' );
      $devotion->post_content = wpautop($devotion->post_content);

      $AZTimezone = new DateTimeZone('America/Phoenix');
      $devotion->post_date_ISO_8601 = date_format(date_create($devotion->post_date, $AZTimezone), DATE_ISO8601);

      $fields = get_fields($devotion->ID);
      foreach( $fields as $name => $value ) {
        $devotion->$name = $value;
      }

      if ($devotion->devotional_author) {
        $devotional_author_fields = get_fields($devotion->devotional_author->ID);
        foreach( $devotional_author_fields as $name => $value ) {
          $devotion->devotional_author->$name = $value;
        }
    }

      $i++;
    }
    
    if (empty($devotions)) {
    return new WP_Error( 'empty_devotions', 'there are no devotions', array('status' => 404) );
    }
    
    wp_get_nocache_headers();
    
    $response = new WP_REST_Response($devotions);
    $response->set_status(200);
    $response->set_headers(array('Cache-Control' => 'no-cache, must-revalidate, max-age=0'));
    return $response;
};

function get_devotion_by_id($request) {
    $devotion = get_post($request['id']);
    $devotion->featured_image = get_the_post_thumbnail_url( $devotion->ID, 'full' );

    $AZTimezone = new DateTimeZone('America/Phoenix');
    $devotion->post_date_ISO_8601 = date_format(date_create($devotion->post_date, $AZTimezone), DATE_ISO8601);

    $fields = get_fields($devotion->ID);
    foreach( $fields as $name => $value ) {
      $devotion->$name = $value;
    }

    if ($devotion->devotional_author) {
      $devotional_author_fields = get_fields($devotion->devotional_author->ID);
      foreach( $devotional_author_fields as $name => $value ) {
        $devotion->devotional_author->$name = $value;
      }
  }

    if (empty($devotion)) {
    return new WP_Error( 'empty_devotion', 'there is no devotion with that ID', array('status' => 404) );
    }

    $response = new WP_REST_Response($devotion);
    $response->set_status(200);
    $response->set_headers(array('Cache-Control' => 'no-cache'));
    return $response;
};

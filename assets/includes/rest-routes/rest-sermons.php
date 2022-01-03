<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/sermons', array(
    'methods' => 'GET',
    'callback' => 'get_sermons',
    ) );

    register_rest_route( 'invitely/v1', '/sermons/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_sermon',
    ) );

    register_rest_route( 'invitely/v1', '/sermons/series/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_sermons_by_series',
    ) );

    register_rest_route( 'invitely/v1', '/sermons/speaker/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_sermons_by_speaker',
    ) );

});

function get_sermons($request) {

$sermon_args = array(
    'numberposts' => 10,
    'post_type'   => 'ctc_sermon',
    'paged' => ($_REQUEST['page'] ? $_REQUEST['page'] : 1)
);

$sermons = get_posts( $sermon_args );
$i = 0;
foreach($sermons as $sermon) {
    $sermon->featured_image = get_the_post_thumbnail_url($sermon->ID, 'full');
    $sermon->link = get_permalink($sermon->ID);
    $custom_field_values = get_post_custom($sermon->ID);
    $sermon->custom_fields = $custom_field_values;
    $sermon->speaker = get_the_terms($sermon->ID, 'ctc_sermon_speaker');
    $sermon->series = get_the_terms($sermon->ID, 'ctc_sermon_series');
    $sermon->book = get_the_terms($sermon->ID, 'ctc_sermon_book');
    $sermon->topic = get_the_terms($sermon->ID, 'ctc_sermon_topic');
    $sermon->tag = get_the_terms($sermon->ID, 'ctc_sermon_tag');
    $post_date = date_create($sermon->post_date);
    $sermon->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

    // get custom fields from Series - Advanced Custom Fields
    if ($sermon->series) {
      $series = $sermon->series;
      foreach($series as $item) {
        $fields = get_fields('term_'.$item->term_id);
          foreach( $fields as $name => $value ) {
            $item->$name = $value;
          }
      }
    }

    // get custom fields from Speaker - Advanced Custom Fields
    if ($sermon->speaker) {
      $speakers = $sermon->speaker;
      foreach($speakers as $speaker) {
        $fields = get_fields('term_'.$speaker->term_id);
          foreach( $fields as $name => $value ) {
            $speaker->$name = $value;
          }
      }
    }

    $i++;
}

if (empty($sermons)) {
return new WP_Error( 'empty_sermons', 'there are no sermons', array('status' => 404) );
}

$response = new WP_REST_Response($sermons);
$response->set_status(200);
return $response;
};

function get_sermon($request) {

    $sermon = get_post($request['id']);
    $sermon->featured_image = get_the_post_thumbnail_url($request['id'], 'full');
    $sermon->custom_fields = get_post_custom($request['id']);
    $sermon->speaker = get_the_terms($sermon->ID, 'ctc_sermon_speaker');
    $sermon->series = get_the_terms($sermon->ID, 'ctc_sermon_series');
    $sermon->book = get_the_terms($sermon->ID, 'ctc_sermon_book');
    $sermon->topic = get_the_terms($sermon->ID, 'ctc_sermon_topic');
    $sermon->tag = get_the_terms($sermon->ID, 'ctc_sermon_tag');
    $post_date = date_create($sermon->post_date);
    $sermon->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

    // get custom fields from Series - Advanced Custom Fields
    if ($sermon->series) {
      $series = $sermon->series;
      foreach($series as $item) {
        $fields = get_fields('term_'.$item->term_id);
          foreach( $fields as $name => $value ) {
            $item->$name = $value;
          }
      }
    }

    // get custom fields from Speaker - Advanced Custom Fields
    if ($sermon->speaker) {
      $speakers = $sermon->speaker;
      foreach($speakers as $speaker) {
        $fields = get_fields('term_'.$speaker->term_id);
          foreach( $fields as $name => $value ) {
            $speaker->$name = $value;
          }
      }
    }

    if (empty($sermon)) {
    return new WP_Error( 'empty_sermon', 'there is no sermon with ID '.$request['id'], array('status' => 404) );
    }

    $response = new WP_REST_Response($sermon);
    $response->set_status(200);
    return $response;
};

function get_sermons_by_series($request) {

    $sermons = get_posts(array(
        'post_type' => 'ctc_sermon',
        'numberposts' => -1,
        'tax_query' => array(
          array(
            'taxonomy' => 'ctc_sermon_series',
            'field' => 'term_id',
            'terms' => $request['id'],
            'include_children' => false
          )
        )
      ));

    $i = 0;
    foreach($sermons as $sermon) {
        $sermon->featured_image = get_the_post_thumbnail_url($sermon->ID, 'full');
        $sermon->link = get_permalink($sermon->ID);
        $custom_field_values = get_post_custom($sermon->ID);
        $sermon->custom_fields = $custom_field_values;
        $sermon->speaker = get_the_terms($sermon->ID, 'ctc_sermon_speaker');
        $sermon->series = get_the_terms($sermon->ID, 'ctc_sermon_series');
        $sermon->book = get_the_terms($sermon->ID, 'ctc_sermon_book');
        $sermon->topic = get_the_terms($sermon->ID, 'ctc_sermon_topic');
        $sermon->tag = get_the_terms($sermon->ID, 'ctc_sermon_tag');
        $post_date = date_create($sermon->post_date);
        $sermon->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

        // get custom fields from Series - Advanced Custom Fields
        if ($sermon->series) {
          $series = $sermon->series;
          foreach($series as $item) {
            $fields = get_fields('term_'.$item->term_id);
              foreach( $fields as $name => $value ) {
                $item->$name = $value;
              }
          }
        }

        // get custom fields from Speaker - Advanced Custom Fields
        if ($sermon->speaker) {
          $speakers = $sermon->speaker;
          foreach($speakers as $speaker) {
            $fields = get_fields('term_'.$speaker->term_id);
              foreach( $fields as $name => $value ) {
                $speaker->$name = $value;
              }
          }
        }

        $i++;
    }

    if (empty($sermons)) {
    return new WP_Error( 'empty_sermons', 'there are no sermons', array('status' => 404) );
    }

    $response = new WP_REST_Response($sermons);
    $response->set_status(200);
    return $response;
};

function get_sermons_by_speaker($request) {

    $sermons = get_posts(array(
        'post_type' => 'ctc_sermon',
        'numberposts' => -1,
        'tax_query' => array(
          array(
            'taxonomy' => 'ctc_sermon_speaker',
            'field' => 'term_id',
            'terms' => $request['id'],
            'include_children' => false
          )
        )
      ));

    $i = 0;
    foreach($sermons as $sermon) {
        $sermon->featured_image = get_the_post_thumbnail_url($sermon->ID, 'full');
        $sermon->link = get_permalink($sermon->ID);
        $custom_field_values = get_post_custom($sermon->ID);
        $sermon->custom_fields = $custom_field_values;
        $sermon->speaker = get_the_terms($sermon->ID, 'ctc_sermon_speaker');
        $sermon->series = get_the_terms($sermon->ID, 'ctc_sermon_series');
        $sermon->book = get_the_terms($sermon->ID, 'ctc_sermon_book');
        $sermon->topic = get_the_terms($sermon->ID, 'ctc_sermon_topic');
        $sermon->tag = get_the_terms($sermon->ID, 'ctc_sermon_tag');
        $post_date = date_create($sermon->post_date);
        $sermon->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

        // get custom fields from Series - Advanced Custom Fields
        if ($sermon->series) {
          $series = $sermon->series;
          foreach($series as $item) {
            $fields = get_fields('term_'.$item->term_id);
              foreach( $fields as $name => $value ) {
                $item->$name = $value;
              }
          }
        }

        // get custom fields from Speaker - Advanced Custom Fields
        if ($sermon->speaker) {
          $speakers = $sermon->speaker;
          foreach($speakers as $speaker) {
            $fields = get_fields('term_'.$speaker->term_id);
              foreach( $fields as $name => $value ) {
                $speaker->$name = $value;
              }
          }
        }

        $i++;
    }

    if (empty($sermons)) {
    return new WP_Error( 'empty_sermons', 'there are no sermons', array('status' => 404) );
    }

    $response = new WP_REST_Response($sermons);
    $response->set_status(200);
    return $response;
};


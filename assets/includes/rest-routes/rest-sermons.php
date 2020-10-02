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

});

function get_sermons($request) {

$sermon_args = array(
    'numberposts' => 0,
    'post_type'   => 'ctc_sermon',
);

$sermons = get_posts( $sermon_args );
$i = 0;
foreach($sermons as $sermon) {
    $sermon->featured_image = get_the_post_thumbnail_url($sermon->ID, 'full');
    $custom_field_values = get_post_custom($sermon->ID);
    $sermon->custom_fields = $custom_field_values;
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

if (empty($sermon)) {
return new WP_Error( 'empty_sermon', 'there is no sermon with ID '.$request['id'], array('status' => 404) );
}

$response = new WP_REST_Response($sermon);
$response->set_status(200);
return $response;
};

<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/events', array(
        'methods' => 'GET',
        'callback' => 'get_events',
    ) );

    register_rest_route( 'invitely/v1', '/events/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_event',
    ) );

});

function get_events($request) {

    $event_args = array(
        'numberposts' => 0,
        'post_type'   => 'ctc_event',
    );

    $events = get_posts( $event_args );
    $i = 0;
    foreach($events as $event) {
        $event->featured_image = get_the_post_thumbnail_url($event->ID, 'full');
        $custom_field_values = get_post_custom($event->ID);
        $event->start_date_time = $custom_field_values['_ctc_event_start_date_start_time'][0];
        $event->end_date_time = $custom_field_values['_ctc_event_end_date_end_time'][0];
        $event->custom_fields = $custom_field_values;

        if(date("Y/m/d", strtotime($custom_field_values['_ctc_event_start_date_start_time'][0])) < current_time("Y/m/d")) {
            //deleting item because of duplicate post on WP website 
            unset($events[$i]);
        }

        $i++;
    }

    if (empty($events)) {
    return new WP_Error( 'empty_event', 'there are no events', array('status' => 404) );
    }

    $response = new WP_REST_Response(wp_list_sort($events, 'start_date_time', 'ASC'));
    $response->set_status(200);
    return $response;
};

function get_event($request) {

    $event = get_post($request['id']);
    $event->featured_image = get_the_post_thumbnail_url($request['id'], 'full');
    $event->custom_fields = get_post_custom($request['id']);
    
    if (empty($event)) {
    return new WP_Error( 'empty_event', 'there is no event with ID '.$request['id'], array('status' => 404) );
    }

    $response = new WP_REST_Response($event);
    $response->set_status(200);
    return $response;
};

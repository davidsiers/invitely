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
        'post_type'   => 'ctc_event',
        'posts_per_page'	=> 10,
        'meta_key'			=> '_ctc_event_start_date_start_time',
        'orderby'			=> 'meta_value',
        'order'				=> 'ASC',
        'paged' => ($_REQUEST['page'] ? $_REQUEST['page'] : 1),
        'meta_query'	=> array(
            'relation'		=> 'AND',
            array(
                'key'		=> '_ctc_event_start_date',
                'value' => date("Y-m-d"), // Set today's date (note the similar format) "2021-02-07 18:00:00"
                'compare' => '>=', // Return the ones greater than today's date
                'type' => 'DATE' // Let WordPress know we're working with date
            )
        )
    );

    $events = get_posts( $event_args );
    $i = 0;
    foreach($events as $event) {
        $event->featured_image = get_the_post_thumbnail_url($event->ID, 'full');
        $custom_field_values = get_post_custom($event->ID);
        $event->start_date_time = date_format(date_create($custom_field_values['_ctc_event_start_date_start_time'][0]), DATE_ISO8601);
        $event->end_date_time = date_format(date_create($custom_field_values['_ctc_event_end_date_end_time'][0]), DATE_ISO8601);
        $event->custom_fields = $custom_field_values;
        $post_date = date_create($event->post_date);
        $event->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

        if(date("Y/m/d", strtotime($custom_field_values['_ctc_event_start_date_start_time'][0])) < current_time("Y/m/d")) {
            //deleting item because of duplicate post on WP website 
            unset($events[$i]);
        }

        $i++;
    }

    if (empty($events)) {
        return new WP_Error( 'empty_event', 'there are no events', array('status' => 404) );
    }

    $response = new WP_REST_Response($events);
    $response->set_status(200);
    return $response;
};

function get_event($request) {

    $event = get_post($request['id']);
    $event->featured_image = get_the_post_thumbnail_url($request['id'], 'full');
    $event->custom_fields = get_post_custom($request['id']);
    $event->start_date_time = date_format(date_create($custom_field_values['_ctc_event_start_date_start_time'][0]), DATE_ISO8601);
    $event->end_date_time = date_format(date_create($custom_field_values['_ctc_event_end_date_end_time'][0]), DATE_ISO8601);
    $post_date = date_create($event->post_date);
    $event->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

    if (empty($event)) {
    return new WP_Error( 'empty_event', 'there is no event with ID '.$request['id'], array('status' => 404) );
    }

    $response = new WP_REST_Response($event);
    $response->set_status(200);
    return $response;
};

<?php
/**
 *
 * WP Rest API integrations
 *
 */


    add_action( 'rest_api_init', function () {

        register_rest_route( 'invitely/v1', '/invite/(?P<id>\d+)', array(
          'methods' => 'GET',
          'callback' => 'get_invites_by_id',
        ) );
    });

    function get_invites_by_id($request) {
        $post = get_post($request['id']);
        $events = wp_get_object_terms( $request['id'], 'invite_event' );
        $event_id = $events[0]->term_id;
        $custom_fields = get_field_objects($request['id']);
        $event_custom_fields = get_field_objects('term_'.$event_id);
        $invite = array($post, $custom_fields, $events, $event_custom_fields);

        if (empty($post)) {
        return new WP_Error( 'empty_invite', 'there is no invite with that ID', array('status' => 404) );

        }

        $response = new WP_REST_Response($invite);
        $response->set_status(200);
        return $response;
    };


// function add_cors_http_header(){
// 	header("Access-Control-Allow-Origin: *");
// }
add_action('init','add_cors_http_header');
 
add_filter('kses_allowed_protocols', function($protocols) {
	$protocols[] = 'capacitor';
	return $protocols;
});
 
add_filter('kses_allowed_protocols', function($protocols) {
	$protocols[] = 'ionic';
	return $protocols;
});

add_filter('kses_allowed_protocols', function($protocols) {
	$protocols[] = 'localhost:8100';
	return $protocols;
});

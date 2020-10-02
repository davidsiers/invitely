<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/invite/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_invites_by_id',
      ) );

    register_rest_route( 'invitely/v1', '/invite', array(
    'methods' => 'POST',
    'callback' => 'create_invite',
    ) );

});

function create_invite($request) {
    $data = json_decode($request->get_body());

    $invite = array(
        'post_title'    => $data->first_name." ".$data->last_name,
        'post_status'   => 'publish',
        'post_type'     => 'invites',
        'post_author'   => 1
      );
    $post_id = wp_insert_post( $invite );
    wp_set_object_terms( $post_id, array( $data->event ), 'invite_event' );
  
    update_field( 'first_name', $data->first_name, $post_id );
    update_field( 'last_name', $data->last_name, $post_id );
    update_field( 'service_time', $data->service_time, $post_id );
    update_field( 'number_of_guests', $data->number_of_guests, $post_id );
    update_field( 'marital_status', $data->marital_status, $post_id );
    update_field( 'phone_number', $data->phone_number, $post_id );
    update_field( 'email', $data->email, $post_id );
    update_field( 'growth_group', $data->growth_group, $post_id );
    update_field( 'age_bracket', $data->age_bracket, $post_id );
    update_field( 'street', $data->street, $post_id );
    update_field( 'zip_code', $data->zip_code, $post_id );
    update_field( 'invited_by_first_name', $data->invited_by_first_name, $post_id );
    update_field( 'invited_by_last_name', $data->invited_by_last_name, $post_id );
    update_field( 'invited_by_phone', $data->invited_by_phone, $post_id );
    update_field( 'invited_by_email', $data->invited_by_email, $post_id );
    update_field( 'check_in_status', $data->check_in_status, $post_id );
    update_field( 'check_in_timestamp', $data->check_in_timestamp, $post_id );

    $response = new WP_REST_Response($data);
    $response->set_status(200);
    return $response;
}

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

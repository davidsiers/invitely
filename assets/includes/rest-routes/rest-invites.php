<?php
/**
 *
 * WP Rest API Invitation Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/invite/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_invite_by_id',
      ) );

      register_rest_route( 'invitely/v1', '/invite/(?P<id>\d+)/unpublish', array(
        'methods' => 'PUT',
        'callback' => 'unpublish_invite_by_id',
      ) );

      register_rest_route( 'invitely/v1', '/invites/user/(?P<uid>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'get_invites_by_user', 
      ) );

    register_rest_route( 'invitely/v1', '/invite', array(
    'methods' => 'POST',
    'callback' => 'create_invite',
    ) );

    register_rest_route( 'invitely/v1', '/invite/default-event', array(
      'methods' => 'GET',
      'callback' => 'get_default_event',
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
    update_field( 'uid', $data->invited_by_uid, $post_id ); 
    update_field( 'check_in_status', $data->check_in_status, $post_id );
    update_field( 'check_in_timestamp', $data->check_in_timestamp, $post_id );

    $result = array( $data, get_post($post_id) );

    $response = new WP_REST_Response($result);
    $response->set_status(200);
    return $response;
}

function get_invite_by_id($request) {
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

function unpublish_invite_by_id($request) {
  $post = get_post($request['id']);
  $post->post_status = 'draft';
  wp_update_post($post);

  if (empty($post)) {
  return new WP_Error( 'empty_invite', 'there is no invite with that ID', array('status' => 404) );

  }

  $response = new WP_REST_Response($invite);
  $response->set_status(200);
  return $response;
};

function get_invites_by_user($request) {

  $invite_args = array(
      'numberposts' => 100,
      'post_type'   => 'invites',
      'meta_key'		=> 'uid',
      'meta_value'	=> $request['uid']
  );
  
  $invites = get_posts( $invite_args );
  $i = 0;

  foreach($invites as $invite) {
    $invite->events = wp_get_object_terms( $invite->ID, 'invite_event' );
    $event_id = $invite->events[0]->term_id;
    $invite->event_fields = get_field_objects('term_'.$event_id);
    $fields = get_fields($invite->ID);
    foreach( $fields as $name => $value ) {
      $invite->$name = $value;
    }
    $i++;
  }
  
  if (empty($invites)) {
  return new WP_Error( 'empty_invites', 'there are no invites by this user', array('status' => 404) );
  }
  
  $response = new WP_REST_Response($invites);
  $response->set_status(200);
  return $response;
  };

  function get_default_event() {

    $invite_event_args = array(
        'taxonomy'   => 'invite_event',
        'meta_key'		=> 'default_event',
        'meta_value'	=> 1
    );

    $invite_events = get_terms( $invite_event_args );
    $i = 0;

    foreach($invite_events as $invite_event) {
      $invite_event->event_fields = get_field_objects('term_'.$invite_event->term_id);
      $i++;
    }
    
    if (empty($invite_events)) {
    return new WP_Error( 'empty_events', 'there is not a default event selected.', array('status' => 404) );
    }
    
    $response = new WP_REST_Response($invite_events);
    $response->set_status(200);
    return $response;

    };
<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/prayer-requests', array(
      'methods' => 'GET',
      'callback' => 'get_prayer_requests',
      ) );

    register_rest_route( 'invitely/v1', '/prayer-requests/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_prayer_requests_by_id',
      ) );

    register_rest_route( 'invitely/v1', '/prayer-request/(?P<id>\d+)/prayed', array(
      'methods' => 'PUT',
      'callback' => 'prayed_for_prayer_requests_by_id',
    ) );

    register_rest_route( 'invitely/v1', '/prayer-request', array(
    'methods' => 'POST',
    'callback' => 'create_prayer_request',
    ) );

});

function get_prayer_requests($request) {
 
  $prayer_args = array(
      'numberposts' => 10,
      'post_type'   => 'prayers',
      'meta_query' => array(
        array(
            'key' => 'private_request',
            'value'   => array(true),
            'compare' => 'NOT IN'
        )
        ),
      'paged' => ($_REQUEST['page'] ? $_REQUEST['page'] : 1)
  );
  
  $prayers = get_posts( $prayer_args );
  $i = 0;

  foreach($prayers as $prayer) {
    $fields = get_fields($prayer->ID);
    foreach( $fields as $name => $value ) {
      $prayer->$name = $value;
    }
    $i++;
  }
  
  if (empty($prayers)) {
  return new WP_Error( 'empty_prayer_requests', 'there are no prayer requests', array('status' => 404) );
  }
  
  $response = new WP_REST_Response($prayers);
  $response->set_status(200);
  return $response;
  };

  function push_notify_user($userID, $title, $body) {
    $url = 'https://us-central1-tucson-baptist-church.cloudfunctions.net/pushToUserDevices';
    wp_remote_post(
      $url,
      array(
          'body' => array(
              'userID'   => $userID,
              'title'     => $title,
              'body' => $body
          )
      )
  );
  }

function prayed_for_prayer_requests_by_id($request) {
  $data = json_decode($request->get_body());

  $post_id = $request['id'];
  $value = get_field( 'number_of_prayers', $post_id );
  $userID = get_field( 'uid', $post_id );
  $subject = get_field( 'subject', $post_id );
  $firstname = $data->first_name;
  $lastname = $data->last_name;

  if( $value ) {
    $value++;
  } else {
    $value = 1;
  }
  
  update_field( 'number_of_prayers', $value, $post_id );

  push_notify_user($userID, $firstname.' prayed for you!', $firstname.' '.$lastname.' prayed for your prayer about '.$subject.'.');

  $response = new WP_REST_Response('Prayed for '.$value.' times.');
  $response->set_status(200);
  return $response;
}

function notify_about_new_prayer_request($data, $post_id) {
  $to = 'prayer@tucsonbaptist.com';
  $subject = 'New Prayer Request';
  $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <info@tucsonbaptist.com>');
  
  if( $data->private_request ) {
    $body = 'A new prayer request has been submitted by '.$data->first_name.' '.$data->last_name.'.<br><br>'.
  '<strong>First Name: </strong>'.$data->first_name.'<br>'.
  '<strong>Last Name: </strong>'.$data->last_name.'<br>'.
  '<strong>Email: </strong>'.$data->email.'<br>'.
  '<strong>User ID: </strong>'.$data->uid.'<br>'.
  '<strong>Subject: </strong>'.$data->subject.'<br>'.
  '<strong>Description: </strong>'.$data->description.'<br>'.
  '<strong>Private: </strong> Keep Private <br><br>'.
  '<strong>Publish: </strong><a href="https://www.tucsonbaptist.com/wp-admin/post.php?post='.$post_id.'&action=edit">https://www.tucsonbaptist.com/wp-admin/post.php?post='.$post_id.'&action=edit</a>';
 } else {
    $body = 'A new prayer request has been submitted by '.$data->first_name.' '.$data->last_name.'.<br><br>'.
  '<strong>First Name: </strong>'.$data->first_name.'<br>'.
  '<strong>Last Name: </strong>'.$data->last_name.'<br>'.
  '<strong>Email: </strong>'.$data->email.'<br>'.
  '<strong>User ID: </strong>'.$data->uid.'<br>'.
  '<strong>Subject: </strong>'.$data->subject.'<br>'.
  '<strong>Description: </strong>'.$data->description.'<br><br>'.
  '<strong>Publish: </strong><a href="https://www.tucsonbaptist.com/wp-admin/post.php?post='.$post_id.'&action=edit">https://www.tucsonbaptist.com/wp-admin/post.php?post='.$post_id.'&action=edit</a>';
  }
  
  wp_mail( $to, $subject, $body, $headers );
}

function create_prayer_request($request) {
    $data = json_decode($request->get_body());

    $prayer = array(
        'post_title'    => $data->subject,
        'post_status'   => 'draft',
        'post_type'     => 'prayers',
        'post_author'   => 1
    );
    $post_id = wp_insert_post( $prayer );
  
    update_field( 'first_name', $data->first_name, $post_id );
    update_field( 'last_name', $data->last_name, $post_id );
    update_field( 'email', $data->email, $post_id );
    update_field( 'uid', $data->uid, $post_id );
    update_field( 'subject', $data->subject, $post_id );
    update_field( 'description', $data->description, $post_id );
    update_field( 'answered_timestamp', $data->answered_timestamp, $post_id );
    update_field( 'private_request', $data->private_request, $post_id );
    
    notify_about_new_prayer_request($data, $post_id);

    $response = new WP_REST_Response($data);
    $response->set_status(200);
    return $response;
}

function get_prayer_requests_by_id($request) {
    $post = get_post($request['id']);
    $custom_fields = get_field_objects($request['id']);
    $prayer = array($post, $custom_fields);

    if (empty($post)) {
    return new WP_Error( 'empty_invite', 'there is no invite with that ID', array('status' => 404) );
    }

    $response = new WP_REST_Response($prayer);
    $response->set_status(200);
    return $response;
};

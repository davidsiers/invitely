<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/next-step', array(
    'methods' => 'POST',
    'callback' => 'create_next_step',
    ) );

    register_rest_route( 'invitely/v1', '/next-step/text-connect-card', array(
      'methods' => 'POST',
      'callback' => 'create_text_next_step',
    ) );

});
 


function notify_about_next_step($data, $post_id) {
  $to = 'nextstep@tucsonbaptist.com';
  $subject = 'Next Step for '.$data->first_name.' '.$data->last_name;
  $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <info@tucsonbaptist.com>');
  
    $body = $data->first_name.' '.$data->last_name.' wants to take their Next Step!<br><br>'.
  '<strong>First Name: </strong>'.$data->first_name.'<br>'.
  '<strong>Last Name: </strong>'.$data->last_name.'<br>'.
  '<strong>Email: </strong>'.$data->email.'<br>'.
  '<strong>Phone: </strong>'.$data->phoneNumber.'<br>'.
  '<strong>Next Step: </strong>'.$data->nextStep.'<br>'.
  '<a href="https://www.tucsonbaptist.com/wp-admin/post.php?post='.$post_id.'&action=edit">View Online</a>';
 
  
  wp_mail( $to, $subject, $body, $headers );
}

function create_next_step($request) {
    $data = json_decode($request->get_body());

    $next_step = array(
        'post_title'    => $data->first_name.' '.$data->last_name,
        'post_status'   => 'publish',
        'post_type'     => 'next_step',
        'post_author'   => 1
    );

    $post_id = wp_insert_post( $next_step );
  
    update_field( 'first_name', $data->first_name, $post_id );
    update_field( 'last_name', $data->last_name, $post_id );
    update_field( 'email', $data->email, $post_id );
    update_field( 'phoneNumber', $data->phoneNumber, $post_id );
    update_field( 'nextStep', $data->nextStep, $post_id );
    
    notify_about_next_step($data, $post_id);

    $response = new WP_REST_Response($data);
    $response->set_status(200);
    return $response;
}

function create_text_next_step($request) {
  $data = json_decode($request->get_body());

  $next_step = array(
      'post_title'    => $data->first_name.' '.$data->last_name,
      'post_status'   => 'publish',
      'post_type'     => 'next_step',
      'post_author'   => 1
  );

  $post_id = wp_insert_post( $next_step );

  update_field( 'first_name', $data->first_name, $post_id );
  update_field( 'last_name', $data->last_name, $post_id );
  update_field( 'email', $data->email, $post_id );
  update_field( 'phoneNumber', $data->phoneNumber, $post_id );
  update_field( 'street', $data->street, $post_id );
  update_field( 'zipcode', $data->zipcode, $post_id );
  update_field( 'nextStep', $data->nextStep, $post_id );
  
  notify_about_next_step($data, $post_id);

  $response = new WP_REST_Response($data);
  $response->set_status(200); 
  return $response;
}

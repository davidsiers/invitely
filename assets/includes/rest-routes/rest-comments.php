<?php
/**
 *
 * WP Rest API Comment Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/comments', array(
      'methods' => 'GET',
      'callback' => 'get_post_comments',
      ) );

    register_rest_route( 'invitely/v1', '/comments/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_post_comment_by_post_id',
      ) );

    register_rest_route( 'invitely/v1', '/comments/new', array(
    'methods' => 'POST',
    'callback' => 'create_new_post_comment',
    ) );

});

function get_post_comments($request) {
  $comments = get_comments();

  if (empty($comments)) {
  return new WP_Error( 'empty_comments', 'there are no comments associated with that ID', array('status' => 404) );
  }

  $response = new WP_REST_Response($comments);
  $response->set_status(200);
  return $response;
};



function get_post_comment_by_post_id($request) {
    $post_id = $request['id'];

    $args = array(
      'post_id' => $post_id, 
    );

    $comments = get_comments( $args );

    if (empty($comments)) {
    return new WP_Error( 'empty_comments', 'there are no comments associated with that ID', array('status' => 404) );
    }

    $response = new WP_REST_Response($comments);
    $response->set_status(200);
    return $response;
};

function create_new_post_comment($request) {
  $data = json_decode($request->get_body());
  $current_user = wp_get_current_user();
  $time = current_time('mysql');
  $comment_data = array(
      'comment_post_ID' => $data->post_ID,
      'comment_author' => $data->first_name.' '.$data->last_name,
      'comment_author_email' => $data->email,
      'comment_content' => $data->comment,
      'user_id' => $current_user->ID,
      'comment_date' => $time,
      'comment_approved' => 1,
      'comment_type' => 'feedback'
  );
  wp_insert_comment($comment_data);
  notify_post_author($data->post_ID, $data);

  $author_email = get_the_author_meta('', $data->post_ID);

  $response = new WP_REST_Response($comment_data);
  $response->set_status(200);
  return $response;
}

function notify_post_author($postID, $data) {

    $post = get_post($postID);
    $author_email = get_the_author_meta('user_email', $post->post_author);

    $to = $author_email;
    $subject = 'New Feedback';
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <info@tucsonbaptist.com>');
    
    $body = 'A new prayer request has been submitted by '.$data->first_name.' '.$data->last_name.'.<br><br>'.
    '<strong>Post Title: </strong>'.$post->post_title.'<br>'.
    '<strong>First Name: </strong>'.$data->first_name.'<br>'.
    '<strong>Last Name: </strong>'.$data->last_name.'<br>'.
    '<strong>Email: </strong>'.$data->email.'<br>'.
    '<strong>Comment: </strong>'.$data->comment.'<br>';

    wp_mail( $to, $subject, $body, $headers );

}
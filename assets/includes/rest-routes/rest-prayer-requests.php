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
              'body' => $body,
              'url' => '/tabs/discover/prayer'
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
  $email = get_field( 'email', $post_id );
  $firstname = $data->first_name;
  $lastname = $data->last_name;

  if( $value ) {
    $value++;
  } else {
    $value = 1;
  }
  
  update_field( 'number_of_prayers', $value, $post_id );

  push_notify_user($userID, $firstname.' prayed for you!', $firstname.' '.$lastname.' prayed for your prayer about '.$subject.'.');
  notify_of_being_prayed_for($data, $subject, $email);
  create_new_prayer_comment($post_id, $data);

  $response = new WP_REST_Response('Prayed for '.$value.' times.');
  $response->set_status(200);
  return $response;
}

function create_new_prayer_comment($postID, $data) {
  $current_user = wp_get_current_user();
  $time = current_time('mysql');
  $comment_data = array(
      'comment_post_ID' => $postID,
      'comment_author' => $data->first_name.' '.$data->last_name,
      'comment_author_email' => $data->email,
      'comment_content' => $data->first_name.' '.$data->last_name." prayed.",
      'user_id' => $current_user->ID,
      'comment_date' => $time,
      'comment_approved' => 1,
      'comment_type' => 'feedback'
  );
  wp_insert_comment($comment_data);
}

function notify_of_being_prayed_for($data, $prayerSubject, $prayerEmail) {
  $to = $prayerEmail;
  $subject = $data->first_name.' prayed for you!';
  $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <prayer@tucsonbaptist.com>');
  
  $body = $data->first_name.' '.$data->last_name.' prayed for your request about '.$prayerSubject.'.';
  
  wp_mail( $to, $subject, $body, $headers );
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

function notify_of_being_published($prayerSubject, $prayerEmail) {
  $to = $prayerEmail;
  $subject = 'Your Prayer Request is Published';
  $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <prayer@tucsonbaptist.com>');
  
  $body = 'Your prayer request about '.$prayerSubject.' has been published on the Church App.';
  
  wp_mail( $to, $subject, $body, $headers );
}

function create_push_notification_on_prayer_publish($post) { 
    if ( 'prayers' === $post->post_type ) {
      // $topics = wp_get_post_terms( $post->ID, 'notification_group', array( 'fields' => 'slugs' ));
      // $topic = $topics[0];
      $topic = 'all';
      $url = 'https://us-central1-tucson-baptist-church.cloudfunctions.net/pushToAllUsers';

      $first_name = get_field( "first_name", $post );
      $last_name = get_field( "last_name", $post );
      $email = get_field( "email", $post );
      $subject = get_field( "subject", $post );
      $notify = get_field( "notify", $post );
      $private_request = get_field( "private_request", $post );

      $title = 'New Prayer Request';
      $body = 'Open to pray for ' . $first_name . ' ' . $last_name . '.';

      if ($private_request === false) {
        notify_of_being_published($subject, $email);
        if (!$topic) { $topic = 'all'; };

        if ($notify === true) {

            wp_remote_post(
              $url,
              array(
                  'body' => array(
                    'title' => $title,
                    'body' => $body,
                    'topic' => $topic,
                    'url' => '/tabs/discover/prayer'
                  )
              )
          );

        };
      }

    }
}

add_action( 'draft_to_publish', 'create_push_notification_on_prayer_publish' );

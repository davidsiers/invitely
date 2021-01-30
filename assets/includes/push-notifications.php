<?php
/**
 *
 * WP Push Notifications
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1/push-notification/topic', '/subscribe', array(
        'methods' => 'POST',
        'callback' => 'subscribeUserToTopic',
    ) );

    register_rest_route( 'invitely/v1/push-notification/topic', '/unsubscribe', array(
        'methods' => 'POST',
        'callback' => 'unsubscribeUserFromTopic',
    ) );
}); 

function subscribeUserToTopic($req) {

    $request = json_decode($req->get_body());
    $userID = $request->userID;
    $topic = $request->topic;
    $url = 'https://us-central1-tucson-baptist-church.cloudfunctions.net/subscribeUserToTopic';
    
    wp_remote_post(
      $url,
      array( 'body' => array(
              'userID' => $userID,
              'topic' => $topic
    )));

    $response = new WP_REST_Response($request);
    $response->set_status(200);
    return $response;
}

function unsubscribeUserFromTopic($req) {

    $request = json_decode($req->get_body());
    $userID = $request->userID;
    $topic = $request->topic;
    $url = 'https://us-central1-tucson-baptist-church.cloudfunctions.net/unsubscribeUserFromTopic';
    
    wp_remote_post(
      $url,
      array( 'body' => array(
              'userID' => $userID,
              'topic' => $topic
    )));

    $response = new WP_REST_Response($request);
    $response->set_status(200);
    return $response;
}

add_action( 'transition_post_status', 'create_push_notification', 10, 3 );

function create_push_notification($new_status, $old_status, $post) {
    if ( ( 'publish' === $new_status && 'publish' !== $old_status ) && 'notifications' === $post->post_type ) {
    $title = $post->post_title;
    $body = $post->post_content;
    $topics = wp_get_post_terms( $post->ID, 'notification_group', array( 'fields' => 'slugs' ));
    $url = 'https://us-central1-tucson-baptist-church.cloudfunctions.net/pushToAllUsers';
    $topic = $topics[0];
    
    if (!$topic) { $topic = 'all'; };

    wp_remote_post(
      $url,
      array(
          'body' => array(
              'title' => $title,
              'body' => $body,
              'topic' => $topic
          )
      )
  );
}
}

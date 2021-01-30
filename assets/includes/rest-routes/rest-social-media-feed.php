<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/media-feed', array(
        'methods' => 'GET',
        'callback' => 'aggregate_feeds',
      ) );

});

function aggregate_feeds() {
    $facebook_API = "https://graph.facebook.com/v7.0/";
    $facebook_page = "1417151068561866/";
    $instagram_page = "17841402168154421/";
    $facebook_filter_fields = "feed?fields=attachments{description,title,url,media_type,media},call_to_action,id,message,properties,status_type,updated_time,permalink_url&limit=10&access_token=";
    $instagram_filter_fields = "media?fields=media_type,media_url,permalink,timestamp,caption,id&limit=10&access_token=";
    $facebook_access_token = "EAAH2TC4jmscBALOx65NwwR2CNZBYT9RlMQ9sCoi4sb620j6yQNLYg3BDe1VGWnDA2sZBXEI2Pj8RnqnferYmLVsRXNxm8VLVKjTGouI9t8ytL7PV6cwo93m5N8DUz1NrmG0mKuBq36fM82yaKZCPPjfpRbB48tYNz1toPaOSAZDZD";

    $sermon_args = array(
        'numberposts' => 10,
        'post_type'   => 'ctc_sermon'
    );
    $post_args = array(
        'numberposts' => 10,
        'post_type'   => 'post'
    );
    $feed_list = array();

    $sermons = get_posts( $sermon_args );
    $posts = get_posts( $post_args );
    $instagram_posts = json_decode(wp_remote_retrieve_body(wp_remote_get( $facebook_API.$instagram_page.$instagram_filter_fields.$facebook_access_token )));
    $facebook_posts = json_decode(wp_remote_retrieve_body(wp_remote_get( $facebook_API.$facebook_page.$facebook_filter_fields.$facebook_access_token )));
    
    foreach($sermons as $sermon) {                 
        $sermon->content_type = 'sermon';
        $post_date = date_create($sermon->post_date_gmt);
        $sermon->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);
        $sermon->post_featured_image = get_the_post_thumbnail_url( $sermon->ID, 'full' );
        $sermon->link = get_permalink($sermon->ID);
        array_push($feed_list, $sermon);
    };
    foreach($posts as $post) { 
        $post->content_type = 'post';
        $post_date = date_create($post->post_date_gmt);
        $post->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);
        $post->post_featured_image = get_the_post_thumbnail_url( $post->ID, 'full' );
        $post->link = get_permalink($post->ID);
        array_push($feed_list, $post);
    };
    foreach($instagram_posts as $instagram_post) { 
        if (is_array($instagram_post)) {
            foreach ($instagram_post as $instagram_post_data) {
                $post_date = date_create($instagram_post_data->timestamp);
                $instagram_post_data->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);
                $instagram_post_data->content_type = 'instagram';
                array_push($feed_list, $instagram_post_data);
            } 
        }
    };
    $i = 0;
    foreach($facebook_posts as $facebook_post) {
        if (is_array($facebook_post)) {
            foreach ($facebook_post as $facebook_post_data) {
                if(
                    strpos($facebook_post_data->message, 'Compelling Thought') !== false ||
                    strpos($facebook_post_data->message, 'One Thought') !== false ||
                    strpos($facebook_post_data->attachments->data[0]->title, 'Sunday Morning Service') !== false
                ){
                    //deleting item because of duplicate post on WP website 
                    unset($facebook_post[$i]);
                } else {
                $post_date = date_create($facebook_post_data->updated_time);
                $facebook_post_data->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);
                $facebook_post_data->content_type = 'facebook';
                array_push($feed_list, $facebook_post_data);
                }
            }
        }
        $i++;
    };
    
    if (empty($feed_list)) {
    return new WP_Error( 'empty_aggregated_feeds', 'no feed data', array('status' => 404) );

    }

    $response = new WP_REST_Response(wp_list_sort($feed_list, 'post_date_ISO_8601', 'DESC'));
    $response->set_status(200);
    return $response;
};

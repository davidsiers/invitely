<?php
/**
 *
 * WP Rest API Sermon Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/forms', array(
        'methods' => 'GET',
        'callback' => 'wpcf7_rest_get_contact_forms',
    ) );

    register_rest_route( 'invitely/v1', '/forms/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'wpcf7_rest_get_contact_form',
    ) );

    register_rest_route( 'invitely/v1', '/forms/(?P<id>\d+)/feedback', array(
        'methods' => 'POST',
        'callback' => 'wpcf7_rest_create_feedback',
    ) );

});



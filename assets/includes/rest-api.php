<?php
/**
 *
 * WP Rest API integrations
 *
 */

    add_action( 'rest_api_init', function () {

        register_rest_route( 'invitely/v1', '/person/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => 'get_user',
          ) );

        register_rest_route( 'invitely/v1', '/mail', array(
            'methods' => 'GET',
            'callback' => 'send_mail',
        ) );
        register_rest_route( 'invitely/v1', '/connect', array(
            'methods' => 'POST',
            'callback' => 'pos_connect',
        ) );
    }); 

    include(invitely_get_path('assets/includes/rest-routes/rest-sermons.php'));
    include(invitely_get_path('assets/includes/rest-routes/rest-contact-form-7.php'));
    include(invitely_get_path('assets/includes/rest-routes/rest-invites.php'));
    include(invitely_get_path('assets/includes/rest-routes/rest-events.php'));
    include(invitely_get_path('assets/includes/rest-routes/rest-social-media-feed.php')); 
    include(invitely_get_path('assets/includes/rest-routes/rest-prayer-requests.php')); 

    // https://tucsonbaptist.shelbynextchms.com/api/user/login?return_perms=true

    function get_user($request) {
        $shelbyCHMS_API = "https://tucsonbaptist.shelbynextchms.com/api/";
        $SessionId = "3255431cf0d94e8267b628ba0f842479";

        $args = array(
            'headers' => array(
              'X-SessionId' => $SessionId
            )
        );

        $person_id = $request['id'];
        $person_data = json_decode(wp_remote_retrieve_body(wp_remote_get( $shelbyCHMS_API.'people/'.$person_id, $args )));
        // $person_data = $shelbyCHMS_API.'people/'.$person_id;

        if (empty($person_id)) {
        return new WP_Error( 'empty_person_id', 'there is no person with that ID', array('status' => 404) );
        }

        $response = new WP_REST_Response($person_data);
        $response->set_status(200);
        return $response;

    }

    function send_mail() {
        $to = 'david@tucsonbaptist.com';
        $subject = 'The subject';
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <info@tucsonbaptist.com>');

        ob_start();

        include(invitely_get_path('assets/templates/email.php'));
    
        $body = ob_get_contents();
       
        ob_end_clean();

        wp_mail( $to, $subject, $body, $headers );
    }

    function pos_connect($req) {

        $request = json_decode($req->get_body());
        $FirstName = $request->FirstName;
        $LastName = $request->LastName;
        $Phone = $request->Phone;
        $Drink = $request->Drink;
        $Type = $request->Type;
        $AddIn = $request->AddIn;
        
        $to = 'david@tucsonbaptist.com';
        $subject = 'Connect';
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Tucson Baptist Church <info@tucsonbaptist.com>');

        $body = "<strong>First Name:</strong> $FirstName<br>
        <strong>Last Name:</strong> $LastName<br>
        <strong>Phone:</strong> $Phone<br>
        <strong>Drink:</strong> $Drink<br>
        <strong>Type:</strong> $Type<br>
        <strong>Add-In:</strong> $AddIn<br>";

        wp_mail( $to, $subject, $body, $headers );

        $response = new WP_REST_Response($request);
        $response->set_status(200);
        return $response;
    }
    

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

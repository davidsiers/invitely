<?php
/**
 *
 * WP Rest API Event Routes
 *
 */

add_action( 'rest_api_init', function () {

    register_rest_route( 'invitely/v1', '/events', array(
        'methods' => 'GET',
        'callback' => 'get_events',
    ) );

    register_rest_route( 'invitely/v1', '/events/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_event',
    ) );

});

function get_events($request) {

    $event_args = array(
        'post_type'   => 'ctc_event',
        'posts_per_page'	=> 40,
        'meta_key'			=> '_ctc_event_start_date_start_time',
        'orderby'			=> 'meta_value',
        'order'				=> 'ASC',
        'paged' => ($_REQUEST['page'] ? $_REQUEST['page'] : 1),
        'meta_query'	=> array(
            'relation'		=> 'OR',
            array(
                'key'		=> '_ctc_event_end_date',
                'value' => date("Y-m-d"), // Set today's date (note the similar format) "2021-02-07 18:00:00"
                'compare' => '>=', // Return the ones greater than today's date
                'type' => 'DATE' // Let WordPress know we're working with date
            ),
            array(
                'key'		=> '_ctc_event_start_date',
                'value' => date("Y-m-d"), // Set today's date (note the similar format) "2021-02-07 18:00:00"
                'compare' => '>=', // Return the ones greater than today's date
                'type' => 'DATE' // Let WordPress know we're working with date
            )
            )
    );

    $events = get_posts( $event_args );
    $i = 0;
    foreach($events as $event) {

        $event->featured_image = get_the_post_thumbnail_url($event->ID, 'full');
        $custom_field_values = get_post_custom($event->ID);

        $event->categories = get_the_terms($event->ID, 'ctc_event_category');

        foreach($event->categories as $category) {
            if( get_post_meta($event->ID, '_yoast_wpseo_primary_ctc_event_category',true) == $category->term_id ) {
                $event->primary_category = $category;
            }
        }

        $AZTimezone = new DateTimeZone('America/Phoenix');
        $event->start_date_time = date_format(date_create($custom_field_values['_ctc_event_start_date_start_time'][0], $AZTimezone), DATE_ISO8601);
        $event->end_date_time = date_format(date_create($custom_field_values['_ctc_event_end_date_end_time'][0], $AZTimezone), DATE_ISO8601);

        $event->start_date = date_format(date_create($custom_field_values['_ctc_event_start_date'][0], $AZTimezone), DATE_ISO8601);
        $event->end_date = date_format(date_create($custom_field_values['_ctc_event_end_date'][0], $AZTimezone), DATE_ISO8601);

        $event->custom_fields = $custom_field_values;
        $post_date = date_create($event->post_date);
        $event->categories = get_the_terms($event->ID, 'ctc_event_category');
        $event->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);

        // get custom fields - Advanced Custom Fields
        $fields = get_fields($event->ID);
        foreach( $fields as $name => $value ) {
          $event->$name = $value;
        }

        if ($event->event_contact) {
            $event_contact_fields = get_fields($event->event_contact->ID);
            foreach( $event_contact_fields as $name => $value ) {
              $event->event_contact->$name = $value;
            }
        }


        if ( !function_exists( 'wpcf7_contact_form' ) ) { 
            require_once ABSPATH . PLUGINDIR . 'contact-form-7/includes/contact-form.php'; 
        }

        // if ( !function_exists( 'Ninja_Forms' ) ) { 
        //     require_once ABSPATH . PLUGINDIR . 'ninja-forms/ninja-forms.php'; 
        // }


        if ($event->event_registration_form) {
            // $formID = $event->event_registration_form;
            // $nf = Ninja_Forms::instance();
            // $event->form = $nf->form( $formID )->get();
            // $event->formData = Ninja_Forms()->form( $event->event_registration_form )->get_fields();

            // $formContentData = $event->event_registration_form->data->formContentData;
            // foreach( $formContentData as $fieldName ) {
            //     if ( class_exists( 'Ninja_Forms' ) ) {
            //     $event->event_registration_form->field = Ninja_Forms()->form()->field( $fieldName )->get();
            //     }
            // }

            // $form_item = wpcf7_contact_form($event->event_registration_form->ID);
            // $event->event_registration_form->form = wpcf7_get_properties_for_api( $form_item );

            // $properties = $form_item->get_properties();
            // $properties['form'] = array(
            //     'content' => (string) $properties2['form'],
            //     'fields' => array_map(
            //         function( WPCF7_FormTag $form_tag ) {
            //             return array(
            //                 'basetype' => $form_tag->basetype,
            //                 'name' => $form_tag->name,
            //                 'required' => $form_tag->is_required(),
            //                 'labels' => $form_tag->labels,
            //                 'options' => $form_tag->options,
            //                 'raw_values' => $form_tag->raw_values,
            //                 'values' => $form_tag->values,
            //                 'attr' => $form_tag->attr,
            //                 'group' => $form_tag->group,
            //                 'DateMin' => $form_tag->get_date_option('min'),
            //                 'DateMax' => $form_tag->get_date_option('max'),
            //                 'NumberMin' => $form_tag->get_option('min', 'signed_int', true),
            //                 'NumberMax' => $form_tag->get_option('max', 'signed_int', true),
            //                 'pipes' => $form_tag->pipes instanceof WPCF7_Pipes
            //                     ? $form_tag->pipes->to_array()
            //                     : $form_tag->pipes,
            //                 'content' => $form_tag->content,
            //             );
            //         },
            //         $form_item->scan_form_tags()
            //     ),
            // );
            // $event->event_registration_form->form = $properties['form'];

        }

        // if(date("Y/m/d", strtotime($custom_field_values['_ctc_event_start_date_start_time'][0])) < current_time("Y/m/d")) {
        //     //deleting item because of duplicate post on WP website 
        //     unset($events[$i]);
        // }

        // deleting the sunday morning worship event from the app events section
        if ($event->ID === 2697) {
            unset($events[$i]);
            $events = array_values($events); 
        }

        $i++;
    }

    if (empty($events)) {
        return new WP_Error( 'empty_event', 'there are no events', array('status' => 404) );
    }

    $response = new WP_REST_Response($events);
    $response->set_status(200);
    return $response;
};

function get_event($request) {

    $event = get_post($request['id']);
    $event->featured_image = get_the_post_thumbnail_url($request['id'], 'full');
    $custom_field_values = get_post_custom($request['id']);
    $event->custom_fields = $custom_field_values;

    $event->categories = get_the_terms($event->ID, 'ctc_event_category');

    foreach($event->categories as $category) {
        if( get_post_meta($event->ID, '_yoast_wpseo_primary_ctc_event_category', true) == $category->term_id ) {
            $event->primary_category = $category;
        }
    }

    $AZTimezone = new DateTimeZone('America/Phoenix');
    $event->start_date_time = date_format(date_create($custom_field_values['_ctc_event_start_date_start_time'][0], $AZTimezone), DATE_ISO8601);
    $event->end_date_time = date_format(date_create($custom_field_values['_ctc_event_end_date_end_time'][0], $AZTimezone), DATE_ISO8601);

    $event->start_date = date_format(date_create($custom_field_values['_ctc_event_start_date'][0], $AZTimezone), DATE_ISO8601);
    $event->end_date = date_format(date_create($custom_field_values['_ctc_event_end_date'][0], $AZTimezone), DATE_ISO8601);

    $post_date = date_create($event->post_date);
    $event->categories = get_the_terms($request['id'], 'ctc_event_category');
    $event->post_date_ISO_8601 = date_format($post_date, DATE_ISO8601);


    // get custom fields - Advanced Custom Fields
    $fields = get_fields($event->ID);
    foreach( $fields as $name => $value ) {
        $event->$name = $value;
    }

    if ($event->event_contact) {
        $event_contact_fields = get_fields($event->event_contact->ID);
        foreach( $event_contact_fields as $name => $value ) {
          $event->event_contact->$name = $value;
        }
    }

    // if ($event->event_registration_form) {
    //     $form_item = wpcf7_contact_form($event->event_registration_form->ID);
    //     // $event->event_registration_form->form = wpcf7_get_properties_for_api( $form_item );

    //     $properties = $form_item->get_properties();
    //     $properties['form'] = array(
    //         'content' => (string) $properties2['form'],
    //         'fields' => array_map(
    //             function( WPCF7_FormTag $form_tag ) {
    //                 return array(
    //                     'basetype' => $form_tag->basetype,
    //                     'name' => $form_tag->name,
    //                     'required' => $form_tag->is_required(),
    //                     'labels' => $form_tag->labels,
    //                     'options' => $form_tag->options,
    //                     'raw_values' => $form_tag->raw_values,
    //                     'values' => $form_tag->values,
    //                     'attr' => $form_tag->attr,
    //                     'group' => $form_tag->group,
    //                     'DateMin' => $form_tag->get_date_option('min'),
    //                     'DateMax' => $form_tag->get_date_option('max'),
    //                     'NumberMin' => $form_tag->get_option('min', 'signed_int', true),
    //                     'NumberMax' => $form_tag->get_option('max', 'signed_int', true),
    //                     'pipes' => $form_tag->pipes instanceof WPCF7_Pipes
    //                         ? $form_tag->pipes->to_array()
    //                         : $form_tag->pipes,
    //                     'content' => $form_tag->content,
    //                 );
    //             },
    //             $form_item->scan_form_tags()
    //         ),
    //     );
    //     $event->event_registration_form->form = $properties['form'];

    // }

    if (empty($event)) {
    return new WP_Error( 'empty_event', 'there is no event with ID '.$request['id'], array('status' => 404) );
    }

    $response = new WP_REST_Response($event);
    $response->set_status(200);
    return $response;
};

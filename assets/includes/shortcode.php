<?php 

add_shortcode('invitely_form', 'invitely_form_shortcode');

  function invitely_form_shortcode() { 

    if (
        isset($_POST['invitely_form_create_quote_submitted'])
        && wp_verify_nonce($_POST['invitely_form_create_quote_submitted'],'invitely_form_create_quote')
    ) {
 
        $invitely_quote_title = trim($_POST['first_name'])." ".trim($_POST['last_name']);
        $invitely_quote_author = 1;
        $invitely_quote_text = trim($_POST['service_time']);

        if ($invitely_quote_author != '' && $invitely_quote_text != '') {

            $quote_data = array(
                'post_title' => $invitely_quote_title,
                'post_content' => $invitely_quote_text,
                'post_status' => 'publish',
                'post_author' => $invitely_quote_author,
                'tax_input' => array(
                  "invite_event" => array(119),
                  "invite_event" => array("119"),
                  "invite_event" => 119,
                  "invite_event" => "119"
                ),
                'post_type' => 'invites' 
            );

            if($quote_id = wp_insert_post($quote_data)){

              $field_key = "first_name";
              $value = trim($_POST['first_name']);
              update_field( $field_key, $value, $quote_id );

              $field_key = "last_name";
              $value = trim($_POST['last_name']);
              update_field( $field_key, $value, $quote_id );

              $field_key = "service_time";
              $value = trim($_POST['service_time']);
              update_field( $field_key, $value, $quote_id );

              $field_key = "number_of_guests";
              $value = trim($_POST['number_of_guests']);
              update_field( $field_key, $value, $quote_id );
               
              echo '<p>Quote created and awaiting moderation!</p>';

            } 

        } else { // author or text field is empty

            echo '<p>Quote NOT saved! Who said it? and Quote must not be empty.</p>';

        }
    }  

    echo invitely_get_create_quote_form($invitely_quote_author, $invitely_quote_text, $invitely_quote_category);

} 

function invitely_get_create_quote_form ($invitely_quote_author = '', $invitely_quote_text = '', $invitely_quote_category = 0) {

    $out .= '<form id="create_quote_form" method="post" action="">';

    $out .= wp_nonce_field('invitely_form_create_quote', 'invitely_form_create_quote_submitted');

    $out .= '<label for="first_name">First Name</label><br/>';
    $out .= '<input type="text" id="first_name" name="first_name" value=""/><br/>';

    $out .= '<label for="last_name">Last Name</label><br/>';
    $out .= '<input type="text" id="last_name" name="last_name" value=""/><br/>';

    $out .= '<label for="service_time">Service Time</label><br/>';
    $out .= '<select name="service_time" id="service_time">
              <option value="8AM">8AM - Enhanced Physical Distance Service</option>
              <option value="9AM">9AM - Growth Group</option>
              <option value="10AM">10AM - Worship Service</option>
              <option value="5PM">5PM - Growth Group</option>
            </select><br/>';

    $out .= '<label for="number_of_guests">Number of Guests</label><br/>';
    $out .= '<input type="text" id="number_of_guests" name="number_of_guests" value=""/><br/><br/>';
    
    $out .= '<input type="submit" id="invitely_submit" name="invitely_submit" value="Invite">';

    $out .= '</form>';
 
    return $out;
}
<?php
/**
 *
 * WP Register Custom Post Type Invite
 *
 */


add_action('init', 'init_custom_post_invites');
 
function init_custom_post_invites(){
 
  $invite_type_labels = array(
    'name' => _x('Invites', 'post type general name'),
    'singular_name' => _x('Invite', 'post type singular name'),
    'add_new' => _x('Add New Invite', 'invite'),
    'add_new_item' => __('Add New Invite'),
    'edit_item' => __('Edit Invite'),
    'new_item' => __('Add New Invite'), 
    'all_items' => __('View Invites'),
    'view_item' => __('View Invite'),
    'search_items' => __('Search Invites'),
    'not_found' =>  __('No Invites found'),
    'not_found_in_trash' => __('No Invites found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Invites'
  );
   
  $invite_type_args = array(
    'labels' => $invite_type_labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'menu_icon' => 'dashicons-tickets-alt',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title', 'editor', 'author')
  ); 
   
  register_post_type('invites', $invite_type_args);
 
  $invite_event_labels = array(
    'name' => _x( 'Events', 'taxonomy general name' ),
    'singular_name' => _x( 'Event', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Events' ),
    'all_items' => __( 'All Events' ),
    'parent_item' => __( 'Parent Events' ),
    'parent_item_colon' => __( 'Parent Event:' ),
    'edit_item' => __( 'Edit Event' ), 
    'update_item' => __( 'Update Event' ),
    'add_new_item' => __( 'Add New Event' ),
    'new_item_name' => __( 'New Event' ),
    'menu_name' => __( 'Events' ),
  );    
 
  $invite_event_args = array(
    'hierarchical' => true,
    'labels' => $invite_event_labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'invite_events' ),
  );
   
  register_taxonomy('invite_event', array('invites'), $invite_event_args);
   
  $default_invite_events = array('Worship Service', 'Easter', 'Christmas', 'Fall Outreach');
   
  foreach($default_invite_events as $cat){
   
    if(!term_exists($cat, 'invite_event')) wp_insert_term($cat, 'invite_event');
     
  }
}

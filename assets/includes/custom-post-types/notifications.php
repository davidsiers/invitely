<?php
/**
 *
 * WP Register Custom Post Type Notifications
 *
 */


add_action('init', 'init_custom_post_notifications');
 
function init_custom_post_notifications(){
 
  $notification_type_labels = array(
    'name' => _x('Notifications', 'post type general name'),
    'singular_name' => _x('Notification', 'post type singular name'),
    'add_new' => _x('Add New Notification', 'notification'),
    'add_new_item' => __('Add New Notification'),
    'edit_item' => __('Edit Notification'),
    'new_item' => __('Add New Notification'), 
    'all_items' => __('View Notifications'),
    'view_item' => __('View Notification'),
    'search_items' => __('Search Notifications'),
    'not_found' =>  __('No Notifications found'),
    'not_found_in_trash' => __('No Notifications found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Notifications'
  );
   
  $notification_type_args = array(
    'labels' => $notification_type_labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'menu_icon' => 'dashicons-megaphone',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title', 'editor', 'author')
  ); 
   
  register_post_type('notifications', $notification_type_args);

  $notification_group_labels = array(
    'name' => _x( 'Groups', 'taxonomy general name' ),
    'singular_name' => _x( 'Group', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Groups' ),
    'all_items' => __( 'All Groups' ),
    'parent_item' => __( 'Parent Groups' ),
    'parent_item_colon' => __( 'Parent Group:' ),
    'edit_item' => __( 'Edit Group' ), 
    'update_item' => __( 'Update Group' ),
    'add_new_item' => __( 'Add New Group' ),
    'new_item_name' => __( 'New Group' ),
    'menu_name' => __( 'Groups' ),
  );    
 
  $notification_group_args = array(
    'hierarchical' => true,
    'labels' => $notification_group_labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'notification_group' ),
  );
   
  register_taxonomy('notification_group', array('notifications'), $notification_group_args);
   
  $default_notification_groups = array( 
    array('name' => "All Devices", 'slug' => 'all'),
    array('name' => "Pastor's Weekly Note", 'slug' => 'pastor_encourage'),
    array('name' => "Shelli's Weekly Note", 'slug' => 'shelli_encourage'),
    array('name' => 'Student Ministry', 'slug' => 'student_ministry'),
    array('name' => 'Spanish Ministry', 'slug' => 'spanish'),
    array('name' => 'AWANA', 'slug' => 'awana')
  );
    
  foreach($default_notification_groups as $cat){
   
    if(!term_exists($cat['slug'], 'notification_group')) wp_insert_term($cat['name'], 'notification_group', array('slug' => $cat['slug']));
     
  }

}

<?php
/**
 *
 * WP Register Custom Post Type Prayers
 *
 */


add_action('init', 'init_custom_post_prayers');
 
function init_custom_post_prayers(){
 
  $prayer_type_labels = array(
    'name' => _x('Prayer Requests', 'post type general name'),
    'singular_name' => _x('Prayer Request', 'post type singular name'),
    'add_new' => _x('Add New Prayer Request', 'prayer request'),
    'add_new_item' => __('Add New Prayer Request'),
    'edit_item' => __('Edit Prayer Request'),
    'new_item' => __('Add New Prayer Request'), 
    'all_items' => __('View Prayer Requests'),
    'view_item' => __('View Prayer Request'),
    'search_items' => __('Search Prayer Requests'),
    'not_found' =>  __('No Prayer Requests found'),
    'not_found_in_trash' => __('No Prayer Requests found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Prayer Requests'
  );
   
  $prayer_type_args = array(
    'labels' => $prayer_type_labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'menu_icon' => 'dashicons-cloud-upload',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title', 'editor', 'author')
  ); 
   
  register_post_type('prayers', $prayer_type_args);

}

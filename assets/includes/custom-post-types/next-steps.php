<?php
/**
 *
 * WP Register Custom Post Type Next Steps
 *
 */


add_action('init', 'init_custom_post_next_steps');
 
function init_custom_post_next_steps(){
 
  $next_step_type_labels = array(
    'name' => _x('Next Steps', 'post type general name'),
    'singular_name' => _x('Next Step', 'post type singular name'),
    'add_new' => _x('Add New Next Step', 'notification'),
    'add_new_item' => __('Add New Next Step'),
    'edit_item' => __('Edit Next Step'),
    'new_item' => __('Add New Next Step'), 
    'all_items' => __('View Next Steps'),
    'view_item' => __('View Next Steps'),
    'search_items' => __('Search Next Steps'),
    'not_found' =>  __('No Next Steps found'),
    'not_found_in_trash' => __('No Next Steps found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Next Steps'
  );
   
  $next_step_type_args = array(
    'labels' => $next_step_type_labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'menu_icon' => 'dashicons-chart-bar',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments')
  ); 
   
  register_post_type('next_step', $next_step_type_args);

}

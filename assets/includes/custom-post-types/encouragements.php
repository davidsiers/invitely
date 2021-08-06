<?php
/**
 *
 * WP Register Custom Post Type Encouragements
 *
 */


add_action('init', 'init_custom_post_encouragements');
 
function init_custom_post_encouragements(){
 
  $encouragement_type_labels = array(
    'name' => _x('Encouragements', 'post type general name'),
    'singular_name' => _x('Encouragement', 'post type singular name'),
    'add_new' => _x('Add New Encouragement', 'notification'),
    'add_new_item' => __('Add New Encouragement'),
    'edit_item' => __('Edit Encouragement'),
    'new_item' => __('Add New Encouragement'), 
    'all_items' => __('View Encouragements'),
    'view_item' => __('View Encouragement'),
    'search_items' => __('Search Encouragements'),
    'not_found' =>  __('No Encouragements found'),
    'not_found_in_trash' => __('No Encouragements found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Encouragements'
  );
   
  $encouragement_type_args = array(
    'labels' => $encouragement_type_labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'menu_icon' => 'dashicons-coffee',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments')
  ); 
   
  register_post_type('encouragements', $encouragement_type_args);

  $encouragement_group_labels = array(
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
 
  $encouragement_group_args = array(
    'hierarchical' => true,
    'labels' => $encouragement_group_labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'encouragement_group' ),
  );
   
  register_taxonomy('encouragement_group', array('encouragements'), $encouragement_group_args);
   
  $default_encouragement_groups = array( 
    array('name' => "Pastor's Weekly Note", 'slug' => 'pastor_encourage'),
    array('name' => "Shelli's Weekly Note", 'slug' => 'shelli_encourage'),
  );
    
  foreach($default_encouragement_groups as $cat){
   
    if(!term_exists($cat['slug'], 'encouragement_group')) wp_insert_term($cat['name'], 'encouragement_group', array('slug' => $cat['slug']));
     
  }

}

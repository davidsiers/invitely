<?php
/**
 *
 * WP Register Custom Post Type Devotions
 *
 */


add_action('init', 'init_custom_post_devotions');
 
function init_custom_post_devotions(){
 
  $devotion_type_labels = array(
    'name' => _x('Devotions', 'post type general name'),
    'singular_name' => _x('Devotion', 'post type singular name'),
    'add_new' => _x('Add New Devotion', 'notification'),
    'add_new_item' => __('Add New Devotion'),
    'edit_item' => __('Edit Devotion'),
    'new_item' => __('Add New Devotion'), 
    'all_items' => __('View Devotions'),
    'view_item' => __('View Devotions'),
    'search_items' => __('Search Devotions'),
    'not_found' =>  __('No Devotions found'),
    'not_found_in_trash' => __('No Devotions found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Devotions'
  );
   
  $devotion_type_args = array(
    'labels' => $devotion_type_labels,
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
   
  register_post_type('devotions', $devotion_type_args);

  // $devotion_group_labels = array(
  //   'name' => _x( 'Groups', 'taxonomy general name' ),
  //   'singular_name' => _x( 'Group', 'taxonomy singular name' ),
  //   'search_items' =>  __( 'Search Groups' ),
  //   'all_items' => __( 'All Groups' ),
  //   'parent_item' => __( 'Parent Groups' ),
  //   'parent_item_colon' => __( 'Parent Group:' ),
  //   'edit_item' => __( 'Edit Group' ), 
  //   'update_item' => __( 'Update Group' ),
  //   'add_new_item' => __( 'Add New Group' ),
  //   'new_item_name' => __( 'New Group' ),
  //   'menu_name' => __( 'Groups' ),
  // );    
 
  // $devotion_group_args = array(
  //   'hierarchical' => true,
  //   'labels' => $devotion_group_labels,
  //   'show_ui' => true,
  //   'query_var' => true,
  //   'rewrite' => array( 'slug' => 'devotion_group' ),
  // );
   
  // register_taxonomy('devotion_group', array('devotions'), $devotion_group_args);
   
  // $default_devotion_groups = array( 
  //   array('name' => "Pastor's Weekly Note", 'slug' => 'pastor_encourage'),
  //   array('name' => "Shelli's Weekly Note", 'slug' => 'shelli_encourage'),
  // );
    
  // foreach($default_devotion_groups as $cat){
   
  //   if(!term_exists($cat['slug'], 'devotion_group')) wp_insert_term($cat['name'], 'devotion_group', array('slug' => $cat['slug']));
     
  // }

}

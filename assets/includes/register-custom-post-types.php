<?php
/**
 *
 * WP Custom Post Types
 *
 */

include(invitely_get_path('assets/includes/custom-post-types/invites.php'));
include(invitely_get_path('assets/includes/custom-post-types/prayers.php'));
include(invitely_get_path('assets/includes/custom-post-types/notifications.php'));
include(invitely_get_path('assets/includes/custom-post-types/devotions.php'));
include(invitely_get_path('assets/includes/custom-post-types/next-steps.php'));

function invitely_query_vars( $qvars ) {
    $qvars[] = 'token-id';
    return $qvars;
  }
  add_filter( 'query_vars', 'invitely_query_vars' );
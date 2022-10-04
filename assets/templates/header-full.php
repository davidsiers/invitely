<?php
/**
 * Theme Header
 *
 * Outputs <head> and header content (logo, menu, search icon, banner, breadcrumb, etc.).
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); // prints out <title>, JavaScript, CSS, etc. as needed by WordPress, theme, plugins, etc. ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="saved-header">

<!-- get_template_part( CTFW_THEME_PARTIAL_DIR . '/header-top' ); -->
<?php get_template_part( invitely_include('assets/templates/saved/partials/header-top.php') ); // header-top.php (logo, menu, icons) ?>

<!-- get_template_part( CTFW_THEME_PARTIAL_DIR . '/header-banner' ); -->
<?php get_template_part( invitely_include('assets/templates/saved/partials/header-banner.php') ); // header-top.php (logo, menu, icons) ?>

</header>

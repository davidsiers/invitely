<?php
/**
 * Theme Footer
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Footer icons
$footer_icons = saved_social_icons( ctfw_customization( 'footer_icon_urls' ), 'return' );

// Get first location post
$location = ctfw_first_ordered_post( 'ctc_location' );

// Get locations data, if showing location
$location_count = 0;
$locations_page = ctfw_get_page_by_template( 'locations.php' );
if ( ctfw_customization( 'show_footer_location' ) && ! empty( $location['ID'] ) ) {

	// Meta data for page
	extract( ctfw_location_data( $location['ID'] ) );

	// Get Locations page and count
	$location_counts = wp_count_posts( 'ctc_location' );
	$location_count = isset( $location_counts->publish ) ? $location_counts->publish : 0;

}

// Showing a map?
$has_map = false;
if ( ! empty( $map_lat ) && ! empty( $map_lng ) ) {
	$has_map = true;
}

// Notice / Copyright
$footer_notice = ctfw_customization( 'footer_notice' );

// Footer menu
$footer_menu = wp_nav_menu( array(
	'theme_location'	=> 'footer',
	'menu_id'			=> 'saved-footer-menu-content',
	'menu_class'		=> '',
	'depth'				=> 2, // no more than 1 sub menu
	'container'			=> false, // don't wrap in div
	'fallback_cb'		=> false, // don't show pages if no menu found; show nothing
	'echo'				=> false // return instead
) );

// Classes
$classes = array();

	// Location
	if ( $location_count ) {
		$classes[] = 'saved-footer-has-location';
	} else {
		$classes[] = 'saved-footer-no-location';
	}

	// Location Map
	if ( $has_map ) {
		$classes[] = 'saved-footer-has-map';
	} else {
		$classes[] = 'saved-footer-no-map';
	}

	// Social Icons
	if ( $footer_icons ) {
		$classes[] = 'saved-footer-has-icons';
	} else {
		$classes[] = 'saved-footer-no-icons';
	}

	// Notice
	if ( $footer_notice ) {
		$classes[] = 'saved-footer-has-notice';
	} else {
		$classes[] = 'saved-footer-no-notice';
	}

	// Footer Menu
	if ( $footer_menu ) {
		$classes[] = 'saved-footer-has-menu';
	} else {
		$classes[] = 'saved-footer-no-menu';
	}

	// Footer Menu has submenu(s)
	if ( preg_match( '/class="sub-menu"/', $footer_menu ) ) {
		$classes[] = 'saved-footer-has-submenu';
	} else {
		$classes[] = 'saved-footer-no-submenu';
	}

	// Has widgets
	if ( is_active_sidebar( 'ctcom-footer' ) ) {
		$classes[] = 'saved-footer-has-widgets';
	} else {
		$classes[] = 'saved-footer-no-widgets';
	}

	$classes = implode( ' ', $classes );
	if ( $classes ) {
		$class_attr = ' class="' . esc_attr( $classes ) . '"';
	}

?>

<footer id="saved-footer"<?php echo $class_attr; ?>>

	<?php get_sidebar( 'footer' ); ?>

	<?php
	// Load map section (also used on homepage)
	// get_template_part( CTFW_THEME_PARTIAL_DIR . '/map-section' );
	get_template_part( invitely_include('assets/templates/saved/partials/map-section.php') );
	?>

	<?php if ( $footer_icons || $footer_notice || $footer_menu ) : ?>

		<div id="saved-footer-bottom" class="saved-color-main-bg">

			<div id="saved-footer-bottom-inner" class="saved-centered-large">

				<?php if ( $footer_icons || $footer_notice ) : ?>

					<div id="saved-footer-icons-notice">

						<?php if ( $footer_icons ) : ?>

							<div id="saved-footer-icons">
								<?php echo $footer_icons; ?>
							</div>

						<?php endif; ?>

						<?php if ( $footer_notice ) : ?>

							<div id="saved-footer-notice">
								<?php echo nl2br( wptexturize( do_shortcode( $footer_notice ) ) ); ?>
							</div>

						<?php endif; ?>

					</div>

				<?php endif; ?>

				<?php if ( $footer_menu ) : ?>

					<nav id="saved-footer-menu">
						<?php echo $footer_menu; ?>
					</nav>

				<?php endif; ?>

			</div>

		</div>

	<?php endif; ?>

</footer>

<?php
// Show latest events, comments link, etc. fixed to bottom of screen
// get_template_part( CTFW_THEME_PARTIAL_DIR . '/footer-sticky' );
get_template_part( invitely_include('assets/templates/saved/partials/footer-sticky.php') );
?>

<?php
wp_footer(); // a hook for extra code in the footer, if needed
?>

</body>
</html>
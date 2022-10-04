<?php
/**
 * Header Top
 *
 * Outputs logo and menu / search.
 *
 * This is loaded by header.php.
 */

// No direct access
if (! defined( 'ABSPATH' )) exit;

// Search icon?
$search_icon = ctfw_customization( 'header_search' );

// Header icons
$header_icons = ctfw_customization( 'header_icon_urls' );

// Classes
$classes = array();

	// Line
	if (ctfw_customization( 'header_line' )) {
		$classes[] = 'saved-header-has-line';
	} else {
		$classes[] = 'saved-header-no-line';
	}

	// Search
	if ($search_icon) {
		$classes[] = 'saved-header-has-search';
	} else {
		$classes[] = 'saved-header-no-search';
	}

	// Icons
	if ($header_icons) {
		$classes[] = 'saved-header-has-icons';
	} else {
		$classes[] = 'saved-header-no-icons';
	}

	// Class attribute
	$classes = implode( ' ', $classes );
	$class_attr = '';
	if ($classes) {
		$class_attr = ' class="' . esc_attr( $classes ) . '"';
	}

?>

<div id="saved-header-top"<?php echo $class_attr; ?>>

	<div>

		<div id="saved-header-top-bg"></div>

		<div id="saved-header-top-container" class="saved-centered-large">

			<div id="saved-header-top-inner">

			<!-- get_template_part( CTFW_THEME_PARTIAL_DIR . '/header-logo' ); -->
			<?php get_template_part( invitely_include('assets/templates/saved/partials/header-logo.php') ); ?>


				<nav id="saved-header-menu">

					<div id="saved-header-menu-inner">

						<?php
						wp_nav_menu( array(
							'theme_location'	=> 'header',
							'menu_id'			=> 'saved-header-menu-content',
							'menu_class'		=> 'sf-menu',
							'depth'				=> 3, // no more than 2 sub menus or risks running of screen either side
							'container'			=> false, // don't wrap in div
							'fallback_cb'		=> false, // don't show pages if no menu found - show nothing
							//'walker'			=> new CTFW_Walker_Nav_Menu_Description

						) );
						?>

					</div>

				</nav>

				<?php if ($search_icon) : ?>

					<div id="saved-header-search" role="search">

						<div id="saved-header-search-opened">

							<?php get_search_form(); ?>

							<a href="#" id="saved-header-search-close" class="<?php saved_icon_class( 'search-cancel' ); ?>" title="<?php echo esc_attr(_x('Close Search', 'top bar search icon', 'saved')); ?>"></a>

						</div>

						<div id="saved-header-search-closed">
							<a href="#" id="saved-header-search-open" class="<?php saved_icon_class( 'search-button' ); ?>" title="<?php echo esc_attr(_x('Open Search', 'top bar search icon', 'saved')); ?>"></a>
						</div>

					</div>

				<?php endif; ?>

				<?php if ($search_icon && $header_icons) : ?>

					<div id="saved-header-icons-divider">
						<div id="saved-header-icons-divider-line"></div>
					</div>

				<?php endif; ?>

				<?php if ($header_icons) : ?>

					<div id="saved-header-icons">
						<?php echo saved_social_icons( $header_icons ); ?>
					</div>

				<?php endif; ?>

				<div id="saved-header-mobile-menu"></div>

			</div>

		</div>

	</div>

</div>

<?php
/**
 * Header Banner
 *
 * Outputs an image overlayed by a title of the current section based on content type.
 * Pages can use the "Banner" meta box to control how and where this is shown.
 *
 * If no page/post featured image is provided, the Customizer's Header Image is used.
 * If that doesn't exist, "Main Color" is background.
 *
 * This is loaded by header.php.
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Not on homepage
if ( ctfw_is_page_template( 'homepage' ) ) {
	return;
}

// Get banner data pertaining to related content
// This helps know which image to use (ie. use Sermons template's image on
// an individual sermons when it doesn't have its own)
$banner = saved_banner_data();

// Image data
$image_url = '';
$image_opacity = '';

// Get Customizer's Header Image in case needed as default
$default_image_url = get_header_image();

// Use featured image for page or post if available
// This might be the post's own image or one from a related page
if ( $banner['page'] ) { // banner page found

	// Get banner image URL from post's Featured Image
	$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $banner['page']->ID ), 'saved-banner' );
	$image_url = ! empty( $image_src[0] ) ? $image_src[0] : false;

	// Get brightness and opacity and prepare styles for image div
	if ( $image_url ) {
		$image_brightness = get_post_meta( $banner['page']->ID, '_ctcom_banner_image_brightness', true );
		$image_opacity = get_post_meta( $banner['page']->ID, '_ctcom_banner_image_opacity', true );
	}

}

// Use default Header Image since no featured image found
if ( ! $image_url && $default_image_url ) {

	// Get banner image URL from post's Featured Image
	$image_url = $default_image_url;

	// Get brightness and opacity from Customizer for default image
	$image_brightness = ctfw_customization( 'header_image_brightness' );
	$image_opacity = ctfw_customization( 'header_image_opacity' );

}

// Prepare styles for image overlay
$image_style = '';
if ( $image_url ) {

	// Brightness
	$image_brightness_decimal = 1 - ( ! empty( $image_brightness ) ? $image_brightness : ctfw_customization( 'header_image_brightness' ) ) / 100; // color overlay so reverse
	$image_brightness_style = "opacity: $image_brightness_decimal;";

	// Opacity
	$image_opacity_decimal = ( ! empty( $image_opacity ) ? $image_opacity : ctfw_customization( 'header_image_opacity' ) ) / 100; // color is under image, so opacity lets it show through
	$image_style = "opacity: $image_opacity_decimal; background-image: url($image_url);";

}

// Title
$title_paged = saved_title_paged( '', 'return' );
$title_paged_length = strlen( strip_tags( $title_paged ) );
$title_paged_length_ten = floor( $title_paged_length / 5 ) * 5; // round down to nearest 5
$title_paged_length_ten = $title_paged_length_ten > 40 ? 40 : $title_paged_length_ten; // max 40 so same size font after 40 chars; let it wrap

// Person image
$image_by_title = '';
if ( is_singular( 'ctc_person' ) && has_post_thumbnail() ) {
	$image_by_title = get_the_post_thumbnail( $post, 'saved-square-small' );
}

// Breadcrumbs and archives
$breadcrumbs = '';
$archives = array();
$show_bottom = false;
if ( ! ctfw_is_page_template( 'homepage' ) ) { // not on homepage

	// Get breadcrumbs
	$breadcrumbs = saved_breadcrumbs( 'content' );

	// Get content type
	$content_type = ctfw_current_content_type();

	// Get archives data for current section (content type)
	$archives = ctfw_content_type_archives( array(
		'content_type' => $content_type,
	) );

	// Number of terms to show before link to taxonomy's index page
	$max_archives = apply_filters( 'saved_archive_dropdowns_max', 12 );

	// Map content type and archive keys to page templates for creating links in dropdowns to index pages
	$page_templates = array(
		'sermon' => array(
			'ctc_sermon_topic'		=> 'sermon-topics.php',
			'ctc_sermon_series'		=> 'sermon-series.php',
			'ctc_sermon_book'		=> 'sermon-books.php',
			'ctc_sermon_speaker'	=> 'sermon-speakers.php',
			'months'				=> 'sermon-dates.php',
		),
	);
	$page_templates = isset( $page_templates[$content_type] ) ? $page_templates[$content_type] : array();

	// Add event views to archive dropdowns
	if ( $archives && 'event' == $content_type ) {

		$calendar_url = ctfw_get_page_url_by_template( 'events-calendar.php' );
		$upcoming_url = ctfw_get_page_url_by_template( 'events-upcoming.php' );
		$past_url = ctfw_get_page_url_by_template( 'events-past.php' );

		$dropdown['view'] = array();
		$dropdown['view']['type'] = 'custom';
		$dropdown['view']['name'] = _x( 'Views', 'event view', 'saved' );

		$items = array();
		$i = -1; // $i++ will start keys at 0

		if ( $calendar_url ) {
			$i++;
			$items[$i] = new stdClass();
			$items[$i]->name = _x( 'Calendar', 'event view', 'saved' );
			$items[$i]->url = $calendar_url;
		}

		if ( $upcoming_url ) {
			$i++;
			$items[$i] = new stdClass();
			$items[$i]->name = _x( 'List &mdash; Upcoming', 'event view', 'saved' );
			$items[$i]->url = $upcoming_url;
		}

		if ( $past_url ) {
			$i++;
			$items[$i] = new stdClass();
			$items[$i]->name = _x( 'List &mdash; Past', 'event view', 'saved' );
			$items[$i]->url = $past_url;
		}

		// Have at least 2 items
		if ( count( $items ) >= 2 ) {
			$dropdown['view']['items'] = $items;
			$archives = array_merge( $dropdown, $archives );
		}

	}

	// Show bottom bar if have breadcrumbs or section nav
 	if ( $breadcrumbs || $archives ) {
 		$show_bottom = true;
 	}

}

?>

<div id="saved-banner" class="<?php

	$classes = array( 'saved-color-main-bg' );

	// Length class for reducing font size on long titles
	$classes[] = 'saved-banner-title-length-' . $title_paged_length_ten;

	// Image
	if ( $image_style ) {
		$classes[] = 'saved-has-header-image';
	} else {
		$classes[] = 'saved-no-header-image';
	}

	// Title
	if ( $title_paged ) {
		$classes[] = 'saved-has-header-title';
	} else {
		$classes[] = 'saved-no-header-title';
	}

	// Breadcrumbs
	if ( $breadcrumbs ) {
		$classes[] = 'saved-has-breadcrumbs';
	}

	// Archives / section nav
	if ( $archives ) {
		$classes[] = 'saved-has-header-archives';
	}

	// Header bottom (breadcrumbs / archives)
	if ( $show_bottom ) {
		$classes[] = 'saved-has-header-bottom';
	} else {
		$classes[] = 'saved-no-header-bottom';
	}

	// Concatenate classes
	$classes = implode( ' ', $classes );

	// Output classes
	echo esc_attr( $classes );

?>">

	<?php if ( $image_style ) : // have image to show ?>

		<div id="saved-banner-image" style="<?php echo esc_attr( $image_style ); ?>">

			<div id="saved-banner-image-brightness" style="<?php echo esc_attr( $image_brightness_style ); ?>"></div>

			<div class="saved-banner-image-gradient"></div>

		</div>

	<?php else : // no image, just main color ?>

		<div id="saved-banner-darken"></div>

	<?php endif; ?>

	<div id="saved-banner-inner" class="saved-centered-large<?php if ( $image_by_title ) : ?> saved-banner-has-image-by-title<?php endif; ?>">

		<?php if ( $image_by_title ) : ?>

			<div id="saved-banner-image-by-title">
				<?php echo $image_by_title; ?>
			</div>

		<?php endif; ?>

		<div id="saved-banner-title">
			<?php /* Using div instead of H1, because H1 is in <article> as hidden assistive text for proper HTML5 and Outline */ ?>
			<div class="saved-h1"><?php echo $title_paged; ?></div>
		</div>

	</div>

	<?php if ( $show_bottom ) : // breadcrumbs / section nav ?>

		<div id="saved-header-bottom">

			<div id="saved-header-bottom-inner" class="saved-centered-large saved-clearfix">

				<?php echo $breadcrumbs; ?>

				<?php if ( $archives ) : ?>

					<ul id="saved-header-archives">

						<li id="saved-header-archives-section-name" class="saved-header-archive-top">

							<?php
							$section_post_types = ctfw_current_content_type_data( 'post_types' );
							$post_type_obj = get_post_type_object( $section_post_types[0] );
							$section_name = $post_type_obj->labels->name;
							$section_page_templates = ctfw_current_content_type_data( 'page_templates' );
							$section_url = isset( $section_page_templates[0] ) ? ctfw_get_page_url_by_template( $section_page_templates[0] ): '';
							?>

							<?php if ( $section_url ) : ?>
								<a href="<?php echo esc_url( $section_url ); ?>"><?php echo esc_html( $section_name ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $section_name ); ?>
							<?php endif; ?>

						</li>

						<?php

						// Get last key
						end( $archives );
						$last_archive_key = key( $archives );

						// Loop archives
						foreach ( $archives as $archive_key => $archive ) :

							// Has page using an index page template
							$all_url = isset( $page_templates[$archive_key] ) ? ctfw_get_page_url_by_template( $page_templates[$archive_key] ) : '';

							// Reduce number of terms shown
							// Do this if have index page to link to or if is months dropdown (sermons, blog, events) or is blog tags (that can get huge, so show most used only)
							if ( $all_url || 'months' == $archive_key || 'post_tag' == $archive_key ) {
								$archive['items'] = array_slice( $archive['items'], 0, $max_archives );
							}
						?>

							<?php if ( $archive['name'] && ! empty( $archive['items'] ) ) : // not empty ?>

								<li class="saved-header-archive-top">

									<a href="#" class="saved-header-archive-top-name">
										<?php echo esc_html( $archive['name'] ) ?>
										<span class="<?php echo saved_get_icon_class( 'archive-dropdown' ); ?>"></span>
									</a>

									<div id="saved-header-<?php echo esc_attr( ctfw_make_friendly( $archive_key ) ); ?>-dropdown" class="saved-header-archive-dropdown jq-dropdown<?php echo ( $last_archive_key == $archive_key ? ' jq-dropdown-anchor-right' : '' ) ?>">

			  							<div class="jq-dropdown-panel">

											<ul class="saved-header-archive-list">

												<?php foreach ( $archive['items'] as $archive_item ) : ?>

													<li>

														<a href="<?php echo esc_url( $archive_item->url ); ?>" title="<?php echo esc_attr( $archive_item->name ); ?>"><?php echo esc_html( $archive_item->name ); ?></a>

														<?php if ( isset( $archive_item->count ) ) : ?>
															<span class="saved-header-archive-dropdown-count"><?php echo esc_html( $archive_item->count ); ?></span>
														<?php endif; ?>

													</li>

												<?php endforeach; ?>

												<?php if ( $all_url ) : ?>

													<li class="saved-header-archive-dropdown-all">

														<a href="<?php echo esc_url( $all_url ); ?>">
															<?php
															printf(
																/* translators: %s is archive dropdown name (Topics, Series, etc.) */
																_x( 'All %s', 'archive dropdown', 'saved' ),
																esc_html( $archive['name'] )
															);
															?>
														</a>

													</li>

												<?php endif; ?>

											</ul>

										</div>

									</div>

								</li>

							<?php endif; ?>

						<?php endforeach; ?>

					</ul>

				<?php endif; ?>

			</div>

		</div>

	<?php endif; ?>

</div>

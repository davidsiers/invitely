<?php
/**
 * Map Section
 *
 * This shows in CT Locations widget on homepage and on single events/locations.
 * It will also show in the footer if not used on homepage and not on an event or location.
 *
 * Use get_template_part to load this (see widget-templates/widget-locations.php, footer.php,
 * partials/content-event.php and partials/content-location.php).
 */

// No direct access
if (! defined( 'ABSPATH' )) exit;

// Don't attempt again in footer if was shown at top of page already
if (! empty( $GLOBALS['saved_top_map_shown'] )) {
	return;
}

// No map yet
$has_map = false;
$placement = '';

// Top of page (not footer)
if (empty( $GLOBALS['saved_top_map_attempted'] )) {

	// Homepage
	if (ctfw_is_page_template( 'homepage' )) {

		// Showing on top was attempted
		$GLOBALS['saved_top_map_attempted'] = true;

		// Show if have CT Locations widget present on homepage
		if (! empty( $GLOBALS['saved_home_locations_widget'] )) {

			// Don't show map a second time in footer if shown further up at top
			$GLOBALS['saved_top_map_shown'] = true;

			// Map setup
			$placement = 'homepage';
			$container_tag = 'section';
			$container_class = "saved-bg-section saved-map-section " . saved_alternating_bg_class();
			$before = '';
			$after = '';

		}

	}

	// Event
	if (is_singular( 'ctc_event' )) {

		// Showing on top was attempted
		$GLOBALS['saved_top_map_attempted'] = true;

		// Get event data
		$args = array();
		$args['time_and_desc_format'] = __( '<span class="saved-map-section-item-text">%1$s</span> <span class="saved-map-section-item-note">%2$s</span>', 'saved' );
		$data = ctfw_event_data( $args );

		// Categories
		$categories = get_the_term_list(
			$post->ID,
			'ctc_event_category',
			'',
			/* translators: used between list items, there is a space after the comma */
			__( ', ', 'saved' )
		);

		// Show if have coordinates and address
		if ($data['map_lat'] && $data['map_lng'] && $data['address']) {

			$has_map = true;

			// Don't show map a second time in footer if shown further up at top
			$GLOBALS['saved_top_map_shown'] = true;

			// Map setup
			$placement = 'event';
			$container_tag = 'div';
			$container_class = "saved-map-section";
			$before = '<div class="saved-entry-full-map">';
			$after = '</div>';

		} else {
			return;
		}

	}

	// Location
	if (is_singular( 'ctc_location' )) {

		// Showing on top was attempted
		$GLOBALS['saved_top_map_attempted'] = true;

		// Get location data
		$data = ctfw_location_data();

		// Show if have coordinates and address
		if ($data['map_lat'] && $data['map_lng'] && $data['address']) {

			$has_map = true;

			// Don't show map a second time in footer if shown further up at top
			$GLOBALS['saved_top_map_shown'] = true;

			// Map setup
			$placement = 'event';
			$container_tag = 'div';
			$container_class = "saved-map-section";
			$before = '<div class="saved-entry-full-map">';
			$after = '</div>';

		} else {
			return;
		}

	}

}

// Get location data for Homepage or Footer
// $data has aleady been gotten if is event or location single view
if (! in_array( $placement, array( 'event', 'location' ) )) {

	// Get first location post
	$location = ctfw_first_ordered_post( 'ctc_location' );

	// Get locations data
	$location_count = 0;
	if (! empty( $location['ID'] )) {

		// Meta data for first location
		$data = ctfw_location_data( $location['ID'] );

		// Have coordinates for map (and address)?
		if (! empty( $data['map_lat'] ) && ! empty( $data['map_lng'] ) && ! empty( $data['address'] )) {

			$has_map = true;

			// Get locations page
			$locations_page = ctfw_get_page_by_template( 'locations.php' );

			// Locations page URL
			$locations_page_url = '';
			if (isset( $locations_page->ID )) {
				$locations_page_url = get_permalink( $locations_page->ID );
			}

			// Count locations
			$location_counts = wp_count_posts( 'ctc_location' );
			$location_count = isset( $location_counts->publish ) ? $location_counts->publish : 0;

		}

		// URL for single location or all locations page, if have multiple, and page exists
		$locations_page_ready = false;
		$single_or_multiple_locations_url = get_permalink( $location['ID'] );
		if ($location_count > 1 && $locations_page_url) {
			$locations_page_ready = true;
			$single_or_multiple_locations_url = $locations_page_url;
		}

	}

}

// Footer?
if (empty( $placement ) && ctfw_customization( 'show_footer_location' )) {

	// Map setup
	$placement = 'footer';
	$container_tag = 'div';
	$container_class = "saved-map-section";
	$before = '';
	$after = '';

}

// Nothing to show
if (! $has_map || ! $placement) {
	return;
}

// Header or Footer
$header_or_footer = in_array( $placement, array( 'homepage', 'footer' ) ) ? true : false;

// Show Buttons?
$show_buttons = $header_or_footer || ! empty( $data['directions_url'] ) || ! empty( $data['registration_url'] ) || ! empty( $locations_page_ready ) ? true : false;

// Show location fields?

	// Show Phone
	$show_phone = false;
	if (! empty( $data['phone'] )) {
		$show_phone = true;
	}

	// Show Email?
	$show_email = false;
	if (! empty( $data['email'] )) {
		$show_email = true;
	}

	// Show Service Times
	$show_location_times = false;
	if (! empty( $data['times'] )) {
		$show_location_times = true;
	}

// Show event fields?

	// Show Venue
	$show_venue = false;
	if (! empty( $data['venue'] )) {
		$show_venue = true;
	}

	// Show Date
	$show_date = false;
	if (! empty( $data['date'] )) {
		$show_date = true;
	}

	// Show Time
	$show_event_time = false;
	if (! empty( $data['time_range_and_description'] )) {
		$show_event_time = true;
	}

	// Show categories
	$show_categories = false;
	if (! empty( $categories )) {
		$show_categories = true;
	}

?>

<?php if ($before) echo $before; ?>

<<?php echo $container_tag; ?> class="<?php echo $container_class; ?>"<?php if ('homepage' === $placement) : ?> <?php echo saved_homepage_section_id_attr(); ?><?php endif; ?>>

	<?php if ('section' == $container_tag) : ?>
		<h2 class="screen-reader-text"><?php _ex( 'Location Details', 'map section', 'saved' ); ?></h2>
	<?php endif; ?>

	<?php
	if (
		! empty( $data['address'] )
		|| $show_venue
		|| $show_location_times
		|| $show_phone
		|| $show_email
		|| $show_buttons
		|| $show_categories
	) :
	?>

		<div id="saved-map-section-content-bg"></div>

		<div id="saved-map-section-content-container" class="saved-centered-large">

			<div id="saved-map-section-content">

				<div id="saved-map-section-content-inner">

					<?php if (! empty( $data['address'] ) || $show_buttons) : ?>

						<div id="saved-map-section-header"<?php $show_buttons ? ' class="saved-map-section-has-buttons"' : ''; ?>>

							<?php if (! empty( $data['address'] )) : ?>

								<h2 id="saved-map-section-address">

									<?php

									// Prepare address
									$address = $data['address'];
									$address = esc_html( wptexturize( $data['address'] ) );

									// Multiple line address
									// Make each line nowrap so buttons get pushed over
									// (a single line address is too long to nowrap; let it push buttons immediately)
									$address_lines = explode( "\n", $address);
									if (count( $address_lines ) > 1) { // more than one line
										$address = '';
										foreach ($address_lines as $address_line) {
											$address .= '<span class="saved-nowrap saved-block">' . $address_line . '</span>';
										}
									}

									echo $address;

									?>

								</h2>

							<?php endif; ?>

							<?php if ($show_buttons) : ?>

								<ul id="saved-map-section-buttons" class="saved-buttons-list saved-buttons-list-close saved-map-section-<?php echo ( empty( $location_count ) || 1 == $location_count ) ? 'single-location' : 'multiple-locations' ?>">

									<?php if ($header_or_footer && empty( $locations_page_ready )) : // will show only if have 1 location; otherwise "Locations" shows below ?>
										<li class="saved-map-button-more-item"><a href="<?php echo esc_url( $single_or_multiple_locations_url ); ?>" class="saved-map-button-more"><?php _ex( 'More Info', 'map section', 'saved' ); ?></a></li>
									<?php endif; ?>

									<?php if (! empty( $data['directions_url'] )) : ?>
										<li><a href="<?php echo esc_url( $data['directions_url'] ); ?>" class="saved-map-button-directions" target="_blank" rel="noopener noreferrer"><?php _e( 'Directions', 'saved' ); ?></a></li>
									<?php endif; ?>

									<?php if (! empty( $locations_page_ready )) : // show link if have Locations page and more than one location ?>
										<li><a href="<?php echo esc_url( $locations_page_url ); ?>" class="saved-map-button-locations"><?php _ex( 'Locations', 'map section', 'saved' ); ?></a></li>
									<?php endif; ?>

									<?php if (! empty( $data['registration_url'] )) : // show link if have Locations page and more than one location ?>
										<li><a href="<?php echo esc_url( $data['registration_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="saved-map-button-register"><?php echo esc_html( _x( 'Register', 'event registration', 'saved' ) ); ?></a></li>
									<?php endif; ?>

								</ul>

							<?php endif; ?>

						</div>

					<?php endif; ?>

					<ul id="saved-map-section-list" class="saved-clearfix">

						<?php if ($show_venue) : ?>

							<li id="saved-map-section-venue" class="saved-map-info-full">

								<p>
									<?php echo nl2br( esc_html( wptexturize( $data['venue'] ) ) ); ?>
								</p>

							</li>

						<?php endif; ?>

						<?php if ($show_date) : ?>

							<li id="saved-map-section-date">

								<p>

									<span class="saved-map-section-item-text">
										<?php echo nl2br( esc_html( wptexturize( $data['date'] ) ) ); ?>
									</span>

									<?php if ($data['recurrence_note'] || $data['excluded_dates_note']) : ?>

										<span class="saved-map-section-item-note">

											<?php if ($data['recurrence_note']) : // also includes excluded dates in tooltip. ?>

												<?php if (strlen( $data['recurrence_note'] ) != strlen( $data['recurrence_note_short'] )) : ?>

													<a href="#" title="<?php echo esc_attr( $data['recurrence_note'] ); ?>">
														<?php echo wptexturize( $data['recurrence_note_short'] ); ?>
													</a>

												<?php else : ?>
													<?php echo wptexturize( $data['recurrence_note_short'] ); ?>
												<?php endif; ?>


											<?php elseif ($data['excluded_dates_note']) : // don't show if recurring, because that tooltip includes excluded dates. ?>

												<a href="#" title="<?php echo esc_attr( $data['excluded_dates_note'] ); ?>">
													<?php esc_html_e( 'Excluded Dates', 'saved' ); ?>
												</a>

											<?php endif; ?>

										</span>

									<?php endif; ?>

								</p>

							</li>

						<?php endif; ?>

						<?php if ($show_event_time) : ?>

							<li id="saved-map-section-event-time">

								<p>
									 <?php echo wptexturize( $data['time_range_and_description'] ); ?>
								</p>

							</li>

						<?php endif; ?>

						<?php if ($show_location_times) : ?>

							<li id="saved-map-section-location-time" class="saved-map-info-full">

								<p>
									<?php echo nl2br( esc_html( wptexturize( $data['times'] ) ) ); ?>
								</p>

							</li>

						<?php endif; ?>

						<?php if ($show_phone) : ?>

							<li id="saved-map-section-phone">

								<p>
									<?php echo ctfw_format_phone( $data['phone'] ); // escaped, possibly linked ?>
								</p>

							</li>

						<?php endif; ?>

						<?php if ($show_email) : ?>

							<li id="saved-map-section-email">

								<p>
									<?php echo ctfw_email( $data['email'] ); // link with using antispambot() and breaking before @ ?>
								</p>

							</li>

						<?php endif; ?>

						<?php if ($show_categories) : ?>

							<li id="saved-map-section-categories">

								<?php echo $categories; ?>

							</li>

						<?php endif; ?>

					</ul>

				</div>

			</div>

		</div>

	<?php endif; ?>

	<div id="saved-map-section-map">

		<?php

		// Use Google Maps JavaScript API
		echo ctfw_google_map( array(
			'latitude'			=> $data['map_lat'],
			'longitude'			=> $data['map_lng'],
			'type'				=> $data['map_type'],
			'zoom'				=> $data['map_zoom'],
			'container'			=> false, // no container wrapping the map canvas
			'canvas_id'			=> 'saved-map-section-canvas', // custom map canvas element ID (default ctfw-google-map-##### random)
			'canvas_class'		=> '', // add class
			'responsive'		=> false, // false removes .ctfw-google-map-responsive
			'marker'			=> false, // no marker, adding custom overlay
			'center_resize'		=> false, // no centering after resize
			'callback_loaded'	=> 'saved_position_map_section', // see main.js function
			'callback_resize'	=> 'saved_position_map_section', // see main.js function
		) );

		?>

		<div id="saved-map-section-marker"><span class="<?php saved_icon_class( 'map-marker' ); ?>"></span></div>

	</div>

</<?php echo $container_tag; ?>>

<?php if ($after) echo $after; ?>

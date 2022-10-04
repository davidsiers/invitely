<?php
/**
 * Full Event Content (Single)
 */

// No direct access
if (! defined( 'ABSPATH' )) exit;

get_template_part( invitely_include('assets/templates/header-full.php') );

// Show time and description on two lines, if have time and description
/* translators: time range (%1$s) and description (%2$s) for an event */
$args['time_and_desc_format'] = __( '%1$s <div class="saved-event-time-description saved-entry-full-meta-second-line">%2$s</div>', 'saved' );

// Get data
// $date (localized range), $start_date, $end_date, $start_time, $end_time, $start_time_formatted, $end_time_formatted, $hide_time_range, $time (description), $time_range, $time_range_and_description, $time_range_or_description, $venue, $address, $show_directions_link, $directions_url, $map_lat, $map_lng, $map_has_coordinates, $map_type, $map_zoom, $registration_url
// Recurrence fields, $recurrence_note and $recurrence_note_short are also provided as well as $excluded_dates_note (Pro).
extract( ctfw_event_data( $args ) );

// Categories
$categories = get_the_term_list(
	$post->ID,
	'ctc_event_category',
	'',
	/* translators: used between list items, there is a space after the comma */
	__( ', ', 'saved' )
);

// Classes
$classes = '';

// Show meta
// Not showing when have map coordinates because same details are shown in box by map already
if (! $map_has_coordinates && ( $date || $time_range_and_description || $address || $venue || $categories || $registration_url || $directions_url )) {
	$show_meta = true;
	$classes = 'saved-entry-has-meta';
} else {
	$show_meta = false;
	$classes = 'saved-entry-no-meta';
}

// Has buttons?
if ($registration_url || $directions_url) {
	$show_buttons = true;
	$classes .= ' saved-entry-meta-has-buttons';
} else {
	$show_buttons = false;
	$classes .= ' saved-entry-meta-no-buttons';
}

// Has content?
if (ctfw_has_content()) {
	$classes .= ' saved-entry-has-content';
} else {
	$classes .= ' saved-entry-no-content';
}

?>

	<?php
	//CUSTOM CODE TO ADD CUSTOM BUTTONS THAT APPEAR IN THE TBC APP

	// get custom fields - Advanced Custom Fields
    $fields = get_fields($post->ID);
    foreach( $fields as $name => $value ) {
        $post->$name = $value;
    }

    if ($post->event_contact) {
        $event_contact_fields = get_fields($post->event_contact->ID);
        foreach( $event_contact_fields as $name => $value ) {
			$post->event_contact->$name = $value;
        }
    }

	?>

<main id="saved-content"<?php

// Archive with entries - use contrasting background so white boxes show clearly
if ( ctfw_has_loop_multiple() ) {
	echo ' class="' . esc_attr( saved_alternating_bg_class( 'contrast' ) ) . '"';
}

?>>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'saved-entry-full saved-event-full ' . $classes ); ?>>

	<?php
	// Load map section (also used on homepage and footer)
	// get_template_part( CTFW_THEME_PARTIAL_DIR . '/map-section' );
	get_template_part( invitely_include('assets/templates/saved/partials/map-section.php') );
	?>

	<header class="saved-entry-full-header">

		<?php
		// This is visible only to screenreaders.
		// Page title is shown in banner. This is for proper HTML5 and Outline
		if (ctfw_has_title()) :
		?>

			<h1 id="saved-main-title">
				<?php saved_title_paged(); ?>
			</h1>

		<?php endif; ?>

		<?php if ($show_meta) : ?>

			<ul class="saved-entry-meta saved-entry-full-meta">

				<?php if ($date) : ?>

					<li class="saved-entry-full-date saved-event-date">

						<?php echo esc_html( $date ); ?>

						<?php if ($recurrence_note) : // also includes excluded dates in tooltip. ?>

							<div class="saved-event-recurrence saved-entry-full-meta-second-line">

								<?php if (strlen( $recurrence_note ) !== strlen( $recurrence_note_short )) : ?>

									<a href="#" title="<?php echo esc_attr( $recurrence_note ); ?>">
										<?php echo $recurrence_note_short; ?>
									</a>

								<?php else : ?>
									<?php echo $recurrence_note_short; ?>
								<?php endif; ?>

							</div>

						<?php elseif ($excluded_dates_note) : // don't show if recurring, because that tooltip includes excluded dates. ?>

							<div class="saved-event-excluded-dates saved-entry-full-meta-second-line">

								<a href="#" title="<?php echo esc_attr( $excluded_dates_note ); ?>">
									<?php esc_html_e( 'Excluded Dates', 'saved' ); ?>
								</a>

							</div>

						<?php endif; ?>

					</li>

				<?php endif; ?>

				<?php if ($time_range_and_description) : ?>

					<li id="saved-event-time">
						<?php echo wptexturize( $time_range_and_description ); ?>
					</li>

				<?php endif; ?>

				<?php if ($address) : ?>

					<li id="saved-event-address">
						<?php echo nl2br( esc_html( wptexturize( $address ) ) ); ?>
					</li>

				<?php endif; ?>

				<?php if ($venue) : ?>

					<li id="saved-event-venue">
						<?php echo esc_html( wptexturize( $venue ) ); ?>
					</li>

				<?php endif; ?>

				<?php if ($categories) : ?>

					<li id="saved-event-category">
						<?php echo $categories; ?>
					</li>

				<?php endif; ?>

				<?php if ($show_buttons) : ?>

					<li class="saved-entry-full-meta-buttons" id="saved-event-buttons">

						<?php if ($directions_url) : ?>

							<a href="<?php echo esc_url( $directions_url ); ?>" target="_blank" rel="noopener noreferrer" id="saved-event-directions-button" class="saved-button">
								<?php echo esc_html( __( 'Directions', 'saved' ) ); ?>
							</a>

						<?php endif; ?>

						<?php if ($registration_url) : ?>

							<a href="<?php echo esc_url( $registration_url ); ?>" target="_blank" rel="noopener noreferrer" id="saved-event-registration-button" class="saved-button">
								<?php echo esc_html( _x( 'Register', 'event registration', 'saved' ) ); ?>
							</a>

						<?php endif; ?>

					</li>

				<?php endif; ?>

			</ul>

		<?php endif; ?>

	</header>

	<?php if (ctfw_has_content()) : // might not be any content, so let header sit flush with bottom ?>

		<div id="saved-event-content" class="saved-entry-content saved-entry-full-content saved-centered-small">

			<?php the_content(); ?>

			<?php do_action( 'saved_after_content' ); ?>

		</div>

	<?php endif; ?>

	<!-- get_template_part( CTFW_THEME_PARTIAL_DIR . '/content-footer-full' ); -->
	<?php get_template_part( invitely_include('assets/templates/saved/partials/content-footer-full.php') ); // multipage nav, term lists, "Edit" button, etc. ?>



	<div id="saved-event-content" class="saved-entry-content saved-entry-full-content saved-centered-small">
		<?php if ($post->register_button_text) : ?>
			<br><a href="<?php echo esc_url( $registration_url ); ?>" target="_blank" rel="noopener noreferrer" id="saved-event-directions-button" class="saved-button"><?php echo esc_html($post->register_button_text) ?></a><br><br>
		<?php endif; ?>

		<?php if ($post->event_button_2 && $post->event_button_2["register_button_2_url"]) : ?>
			<a href="<?php echo esc_url($post->event_button_2["register_button_2_url"]) ?>" target="_blank" rel="noopener noreferrer" id="saved-event-directions-button" class="saved-button"><?php echo esc_html($post->event_button_2["register_button_2_text"]) ?></a><br><br>
		<?php endif; ?>

		<?php if ($post->event_button_3 && $post->event_button_3["register_button_3_url"]) : ?>
		<a href="<?php echo esc_url($post->event_button_3["register_button_3_url"]) ?>" target="_blank" rel="noopener noreferrer" id="saved-event-directions-button" class="saved-button"><?php echo esc_html($post->event_button_3["register_button_3_text"]) ?></a><br><br>
		<?php endif; ?>

		<?php if ($post->event_button_4 && $post->event_button_4["register_button_4_url"]) : ?>
		<a href="<?php echo esc_url($post->event_button_4["register_button_4_url"]) ?>" target="_blank" rel="noopener noreferrer" id="saved-event-directions-button" class="saved-button"><?php echo esc_html($post->event_button_4["register_button_4_text"]) ?></a><br><br>
		<?php endif; ?>
	</div>

</article>

<?php
// loop-author.php shows bio below a blog post
// (loop-header.php shows the same at top of author archive)
get_template_part( CTFW_THEME_PARTIAL_DIR . '/loop-author' );
?>

<?php
// loop-navigation.php shows the appropriate navigation at bottom
get_template_part( CTFW_THEME_PARTIAL_DIR . '/loop-navigation' );
?>

<?php
// comments.php lists comments when enabled (single posts only)
comments_template();
?>

</div>

</main>

<?php     get_template_part( invitely_include('assets/templates/footer-full.php') ); ?>

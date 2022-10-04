<?php
/**
 * Footer Sticky
 *
 * This outputs HTML at bottom of footer.php for rendering fixed position sticky to show latest events, comments, etc.
 * This stays in place while the user scrolls.
 */

// No direct access
if (! defined( 'ABSPATH' )) exit;

global $post;

// What to show at top right
$bottom_sticky = ctfw_customization( 'bottom_sticky' );

// Showing sermons, events or posts at top-right?
$bottom_sticky_posts = array();
$show_bottom_sticky_posts = in_array( $bottom_sticky, array( 'sermons', 'events', 'posts' ) );
if ($show_bottom_sticky_posts) {

	// Get and sanitize limit
	$max_limit = 2;
	$limit = absint( ctfw_customization( 'bottom_sticky_items_limit' ) );
	if ($limit > $max_limit) {
		$limit = $max_limit;
	}

	// Today, tomorrow and yesterday in local time
	$today = date_i18n( 'Y-m-d' );
	$today_ts = strtotime( $today );
	$tomorrow =  date_i18n( 'Y-m-d', $today_ts + DAY_IN_SECONDS ); // add one day in seconds to today
	$yesterday =  date_i18n( 'Y-m-d', $today_ts - DAY_IN_SECONDS ); // subtract one day in seconds from today

	// Get sermons, events or posts
	if ('sermons' == $bottom_sticky) {

		$bottom_sticky_posts = get_posts( array(
			'post_type'       	=> 'ctc_sermon',
			'orderby'         	=> 'publish_date',
			'order'           	=> 'desc',
			'numberposts'     	=> $limit,
			'suppress_filters'	=> false // keep WPML from showing posts from all languages: http://bit.ly/I1JIlV + http://bit.ly/1f9GZ7D
		) );

	} elseif ('events' == $bottom_sticky) {

		$bottom_sticky_posts = ctfw_get_events( array(
			'timeframe'	=> 'upcoming',
			'limit'	=> $limit,
		) );

	} elseif ('posts' == $bottom_sticky) {

		$bottom_sticky_posts = get_posts( array(
			'post_type'       	=> 'post',
			'orderby'         	=> 'publish_date',
			'order'           	=> 'desc',
			'numberposts'     	=> $limit,
			'suppress_filters'	=> false // keep WPML from showing posts from all languages: http://bit.ly/I1JIlV + http://bit.ly/1f9GZ7D
		) );

	}

}

// Custom content for bottom-left
$custom_content = ctfw_customization( 'bottom_sticky_content' );

// Show comments link if comments are open or closed by with comments
$show_comments = false;
if (is_singular() && ( comments_open() || have_comments() )) {
	$show_comments = true;
}

// Can edit post?
$can_edit = false;
if (is_singular() && ctfw_can_edit_post() && ! is_front_page()) {
	$can_edit = true;
}

// Show bottom
$show_bottom_sticky = true;
if ('none' == $bottom_sticky || ( 'content' == $bottom_sticky && ! $custom_content ) || ( $show_bottom_sticky_posts && ! $bottom_sticky_posts )) {
	$show_bottom_sticky = false;
}

// Abbreviate date format (from settings)
// Withour args, abbreviates month and removes year from format in settings
$date_format = ctfw_abbreviate_date_format();

// Do not output container if no contents
if (! $show_bottom_sticky) {
	return;
}

?>

<aside id="saved-sticky" class="saved-sticky-content-type-<?php echo esc_attr( $bottom_sticky ); ?>">

	<div id="saved-sticky-inner">

		<div id="saved-sticky-content">

			<?php

			// Sermons, events or posts
			if ($bottom_sticky_posts) :

			?>

				<div id="saved-sticky-items">

					<?php

					// Loop posts
					$old_post = $post;
					foreach ($bottom_sticky_posts as $post) :

						// Make the_title() , the_permalink() and so on work
						setup_postdata( $post );

						// Prepare date
						$show_date = '';
						$publish_date = date_i18n( 'Y-m-d', strtotime( $post->post_date ) );
						if (in_array( $bottom_sticky, array( 'sermons', 'posts' ) )) { // sermon or post

							// Today, yesterday or date
							if ($today == $publish_date) {
								$show_date = _x( 'Today', 'bottom sticky items', 'saved' );
							} elseif ($yesterday == $publish_date) {
								$show_date = _x( 'Yesterday', 'bottom sticky items', 'saved' );
							} else {
								/* translators: see date formatting documentation: http://codex.wordpress.org/Formatting_Date_and_Time */
								$show_date = get_the_date( $date_format );
							}

						} elseif ('events' == $bottom_sticky) { // event

							// Get date range
							$start_date = get_post_meta( $post->ID , '_ctc_event_start_date' , true );
							$end_date = get_post_meta( $post->ID , '_ctc_event_end_date' , true );

							// Have a start date
							if ($start_date) {

								// Start and end dates as local timestamps
								$start_date_ts = strtotime( date_i18n( 'Y-m-d', strtotime( $start_date ) ) );
								$end_date_ts = strtotime( date_i18n( 'Y-m-d', strtotime( $end_date ) ) );

								// Today, tomorrow or date
								if ($today_ts >= $start_date_ts && $today_ts <= $end_date_ts) { // start date or any date in range is today
									$show_date = _x( 'Today', 'bottom sticky items', 'saved' );
								} elseif ($start_date == $tomorrow) {
									$show_date = _x( 'Tomorrow', 'bottom sticky items', 'saved' );
								} else {
									$show_date = date_i18n( $date_format, strtotime( $start_date ) );
								}

							}

						}

					?>

						<div class="saved-sticky-item">

							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">

								<?php if ($show_date) : ?>
									<span class="saved-sticky-item-date">
										<?php echo esc_html( $show_date ); ?>
									</span>
								<?php endif; ?>

								<span class="saved-sticky-item-title"><?php echo ctfw_shorten( get_the_title(), 28 ); // shorten title without truncating words ?></span>

							</a>

						</div>

					<?php

					// End Loop
					endforeach;

					// Restore $post global
					wp_reset_postdata();
					$post = $old_post; // wp_reset_postdata() isn't enough with this code

					?>

				</div>

			<?php

			// Custom Content
			elseif ('content' == $bottom_sticky && $custom_content) :

			?>

				<div id="saved-sticky-content-custom-content">
					<?php echo wptexturize( do_shortcode( $custom_content ) ); ?>
				</div>

			<?php endif; ?>

		</div>

		<div id="saved-sticky-dismiss">
			<a href="#" class="<?php saved_icon_class( 'sticky-dismiss' ); ?>" title="<?php esc_attr_e('Close', 'sticky notice', 'jubilee'); ?>"></a>
		</div>

	</div>

</aside>

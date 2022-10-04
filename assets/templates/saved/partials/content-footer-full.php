<?php
/**
 * Full Content Footer
 *
 * Multipage nav, taxonomy terms, admin Edit link, etc. for full display of different post types.
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Collect term lists (categories, tags, etc.) for specific post types
$term_lists = array();

// Blog Terms
if ( is_singular( 'post' ) ) {

	// Blog Tags
	/* translators: used between list items, there is a space after the comma */
	$list = get_the_tag_list( '', __( ', ', 'saved' ) );
	if ( $list ) {
		$term_lists[] = '<div>' . sprintf( __( 'Tagged with %s', 'saved' ), $list ) . '</div>';
	}

}

// Sermon Tags
elseif ( is_singular( 'ctc_sermon' ) ) {

	// Sermon Tags
	/* translators: used between list items, there is a space after the comma */
	$list = get_the_term_list( $post->ID, 'ctc_sermon_tag', '', __( ', ', 'saved' ) );
	if ( $list ) {
		$term_lists[] = '<div>' . sprintf( __( 'Tagged with %s', 'saved' ), $list ) . '</div>';
	}

}

?>

<?php
// Have footer content to show?
if ( ( ctfw_is_multipage() && ! post_password_required() ) || ! empty( $term_lists ) ) :
?>

	<footer class="saved-entry-full-footer saved-centered-medium saved-entry-full-footer-<?php echo $term_lists ? 'has' : 'no'; ?>-terms">

		<?php
		// "Pages: 1 2 3" when <!--nextpage--> used
		if ( ctfw_is_multipage() && ! post_password_required() ) :
		?>

			<?php

			$page_links = wp_link_pages( array(
				'before'			=> '<ul class="saved-buttons-list saved-entry-full-page-nav">',
				'after'				=> '</ul>',
				'next_or_number' 	=> 'next',
				'nextpagelink'     	=> sprintf(
										/* translators: %s is icon */
										__( 'Next Page %s', 'saved' ),
										'<span class="' . saved_get_icon_class( 'nav-button-right' ) . '"></span>'
									),
				'previouspagelink'     	=> sprintf(
										/* translators: %s is icon */
										__( '%s Previous Page', 'saved' ),
										'<span class="' . saved_get_icon_class( 'nav-button-left' ) . '"></span>'
									),
				'echo'				=> false,
			) );

			$page_links = str_replace( '<a', '<li><a', $page_links );
			$page_links = str_replace( '</a>', '</a></li>', $page_links );

			echo $page_links;

			?>

		<?php endif; ?>

		<?php
		// Term lists (categories, tags, etc.)
		if ( $term_lists ) :
		?>

			<div class="saved-entry-full-footer-item">

				<?php foreach ( $term_lists as $term_list ) : ?>
				<div class="saved-entry-full-footer-terms">
					<?php echo $term_list; ?>
				</div>
				<?php endforeach; ?>

			</div>

		<?php endif; ?>

	</footer>

<?php endif; ?>
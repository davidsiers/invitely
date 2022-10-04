<?php
/**
 * Header Logo
 *
 * This is loaded by header.php.
 */

// No direct access
if (! defined( 'ABSPATH' )) exit;

global $post;

?>

<div id="saved-logo">

	<div id="saved-logo-content">

		<?php

		// Text Logo
		if ('text' == ctfw_customization( 'logo_type' ) || ! ctfw_customization( 'logo_image' )) : // or no logo image specified

			// Prepare logo text
			$logo_text = ctfw_customization( 'logo_text' );
			$logo_text = wp_kses( $logo_text, array( 'span' => array() ) ); // "Church <span>Name</span>" can be used for dark portion, so force balance tags
			$logo_text = force_balance_tags( $logo_text );

		?>

			<div id="saved-logo-text" class="saved-logo-text-<?php echo esc_attr( ctfw_customization( 'logo_text_size' ) ); ?>">
				<div id="saved-logo-text-inner">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo nl2br( wptexturize( $logo_text ) ); ?>
					</a>
				</div>
			</div>

		<?php

		// Image Logo
		else :

		?>

			<?php

			// Get logo URL(s)
			$logo_url = ctfw_customization( 'logo_image' ); // uploaded logo
			$logo_hidpi_url = ctfw_customization( 'logo_hidpi' ); // Retina version, if uploaded
			$logo_width = '';
			$logo_height = '';

			// Ensure logo image is not displayed beyond max recommended dimensions
			// This keeps the height from being too tall on mobile or sticky scroll
			// It also solves some issues with mobile that CSS alone is insufficient for
			$logo_size_atts = '';
			if (ctfw_is_local_url( $logo_url )) {

				// Ensure logo URL uses https if site uses SSL
				// When a site switches to SSL, the saved logo URL is still often http://
				if (is_ssl()) {
					$logo_url = str_replace('http://', 'https://', $logo_url);
					$logo_hidpi_url = str_replace('http://', 'https://', $logo_hidpi_url);
				}

				// Get path to file
				$upload_dir = wp_upload_dir();
				$logo_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $logo_url ); // remove base URL

				// File exists
				if (file_exists( $logo_path )) {

					// Get logo size
					$logo_size = getimagesize( $logo_path );
					if ($logo_size) {

						$original_logo_width = $logo_size[0];
						$original_logo_height = $logo_size[1];
						$logo_width = $original_logo_width;
						$logo_height = $original_logo_height;

						$logo_max_width = saved_logo_size( 'max_width' ); // set in includes/images.php
						$logo_max_height = saved_logo_size( 'max_height' );

						// Width exceeds max, adjust width then height proportionately
						if ($logo_width > $logo_max_width) {

							$logo_width = $logo_max_width;

							// Adjust height proportionately
							$logo_height = round( $logo_height * $logo_width / $original_logo_width );

							// Set new "original" size in case new height exceeds max
							if ($logo_height > $logo_max_height) {
								$original_logo_width = $logo_width;
								$original_logo_height = $logo_height;
							}

						}

						// Height exceeds max, adjust height then width proportionately
						if ($logo_height > $logo_max_height) {

							$logo_height = $logo_max_height;

							// Adjust width proportionately
							$logo_width = round( $logo_width * $logo_height / $original_logo_height );

						}

						// Build attributes
						$logo_size_atts = 'width="' . esc_attr( $logo_width ) . '" height="' . esc_attr( $logo_height ) . '"';

					}

				}

			}

			?>

			<div id="saved-logo-image"<?php if ($logo_hidpi_url) : ?> class="saved-has-hidpi-logo"<?php endif; // tell stylesheet Retina logo exists ?>>

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="max-width:<?php echo esc_attr( $logo_width ); ?>px;max-height:<?php echo esc_attr( $logo_height ); ?>px">

					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="saved-logo-regular" <?php echo $logo_size_atts; ?>>

					<?php if ($logo_hidpi_url) : // Retina logo is optional ?>
						<img src="<?php echo esc_url( $logo_hidpi_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="saved-logo-hidpi" <?php echo $logo_size_atts; ?>>
					<?php endif; ?>

				</a>

			</div>

		<?php endif; ?>

	</div>

</div>

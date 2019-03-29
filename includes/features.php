<?php
/**
 * Foundational display functions for displaying links to articles in lists.
 */

/**
 * Returns a thumbnail <img> tag for the given post in a feature layout
 * that supports thumbnails.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @param string $thumbnail_size Thumbnail size to retrieve
 * @return string HTML <img> tag
 */
function today_get_feature_thumbnail( $post, $thumbnail_size='medium_large' ) {
	$thumbnail         = '';
	$thumbnail_class   = 'media-background object-fit-cover feature-thumbnail';
	$header_media_type = get_field( 'header_media_type', $post );

	switch ( $header_media_type ) {
		case 'video':
			// Get video url (prevent ACF oEmbed processing)
			$video_url           = get_field( 'post_header_video_url', $post, false );
			$video_thumbnail_w   = intval( get_option( "{$thumbnail_size}_size_w" ) );
			$video_thumbnail_h   = intval( get_option( "{$thumbnail_size}_size_h" ) );
			$video_thumbnail_url = today_get_oembed_thumbnail( $video_url, $video_thumbnail_w, $video_thumbnail_h );

			if ( $video_thumbnail_url ) {
				$thumbnail = '<img class="' . $thumbnail_class . '" src="' . $video_thumbnail_url . '" alt="">';
			}
			break;
		case 'image':
			$thumbnail_id = today_get_thumbnail_id( $post );

			if ( $thumbnail_id ) {
				$thumbnail = ucfwp_get_attachment_image( $thumbnail_id, $thumbnail_size, false, array(
					'class' => $thumbnail_class,
					'alt' => ''
				) );
			}
			break;
		default:
			break;
	}

	// Use a fallback if the user requested a thumbnail, but
	// one isn't available for the post
	if ( ! $thumbnail ) {
		$thumbnail = '<img class="' . $thumbnail_class . '" src="' . TODAY_THEME_IMG_URL . '/default-thumb.jpg" alt="">';
	}

	return $thumbnail;
}


/**
 * Returns subhead text for the given post in a feature layout
 * that supports subheads.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML markup for the inner subhead's contents
 */
function today_get_feature_subhead( $post ) {
	if ( ! $post instanceof WP_Post ) return;

	$subhead = '';

	if ( $post->post_type === 'ucf_resource_link' && function_exists( 'today_get_resource_link_source' ) ) {
		$subhead = today_get_resource_link_source( $post );
	}
	else {
		$subhead = get_the_date( get_option( 'date_format' ), $post );
	}

	return $subhead;
}


/**
 * Displays an article link in a horizontal orientation.
 * Optionally displays a thumbnail (enabled by default).
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @param array $args Additional arguments to modify the feature markup. Expects [ucf-post-list] attributes
 * @return string HTML markup
 */
function today_display_feature_horizontal( $post, $args=array() ) {
	if ( ! $post instanceof WP_Post ) return;

	$type          = isset( $args['layout__type'] ) ? $args['layout__type'] : 'secondary';
	$use_thumbnail = isset( $args['show_image'] ) ? filter_var( $args['show_image'], FILTER_VALIDATE_BOOLEAN ) : true;
	$use_excerpt   = isset( $args['show_excerpt'] ) ? filter_var( $args['show_excerpt'], FILTER_VALIDATE_BOOLEAN ) : true;
	$use_subhead   = isset( $args['show_subhead'] ) ? filter_var( $args['show_subhead'], FILTER_VALIDATE_BOOLEAN ) : false;

	$type_class     = 'feature-' . sanitize_html_class( $type );
	$permalink      = get_permalink( $post );
	$title          = wptexturize( $post->post_title );
	$excerpt_length = ( $type === 'secondary' ) ? TODAY_SHORT_EXCERPT_LENGTH : TODAY_DEFAULT_EXCERPT_LENGTH;
	$excerpt        = ( $use_excerpt ) ? today_get_excerpt( $post, $excerpt_length ) : '';
	$subhead        = ( $use_subhead ) ? today_get_feature_subhead( $post ) : '';
	$thumbnail      = '';
	$thumbnail_col_class = 'col-4 col-sm-3'; // classes for assumed default $type of 'secondary'

	if ( $use_thumbnail ) {
		$thumbnail = today_get_feature_thumbnail( $post );
	}

	switch ( $type ) {
		case 'primary':
			$thumbnail_col_class = 'col-md-6 col-xl-7 mb-3 mb-md-0';
			break;
		default:
			break;
	}

	ob_start();
?>
	<article class="feature feature-horizontal <?php echo $type_class; ?> mb-4">
		<a href="<?php echo $permalink; ?>" class="feature-link">
			<div class="row">
				<?php if ( $use_thumbnail ): ?>
				<div class="<?php echo $thumbnail_col_class; ?>">
					<div class="media-background-container feature-thumbnail-wrap">
						<?php echo $thumbnail; ?>
					</div>
				</div>
				<?php endif; ?>

				<div class="col">
					<h2 class="feature-title"><?php echo $title; ?></h2>

					<?php if ( $use_excerpt && $excerpt ): ?>
					<div class="feature-excerpt"><?php echo $excerpt; ?></div>
					<?php endif; ?>

					<?php if ( $use_subhead && $subhead ): ?>
					<div class="feature-subhead mt-2"><?php echo $subhead; ?></div>
					<?php endif; ?>
				</div>
			</div>
		</a>
	</article>
<?php
	return ob_get_clean();
}


/**
 * Displays an article link in a vertical orientation.
 * Always displays a thumbnail.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @param array $args Additional arguments to modify the feature markup. Expects [ucf-post-list] attributes
 * @return string HTML markup
 */
function today_display_feature_vertical( $post, $args=array() ) {
	if ( ! $post instanceof WP_Post ) return;

	$type        = isset( $args['layout__type'] ) ? $args['layout__type'] : 'secondary';
	$use_excerpt = isset( $args['show_excerpt'] ) ? filter_var( $args['show_excerpt'], FILTER_VALIDATE_BOOLEAN ) : true;
	$use_subhead = isset( $args['show_subhead'] ) ? filter_var( $args['show_subhead'], FILTER_VALIDATE_BOOLEAN ) : false;

	$type_class     = 'feature-' . sanitize_html_class( $type );
	$permalink      = get_permalink( $post );
	$title          = wptexturize( $post->post_title );
	$excerpt_length = ( $type === 'secondary' ) ? TODAY_SHORT_EXCERPT_LENGTH : TODAY_DEFAULT_EXCERPT_LENGTH;
	$excerpt        = ( $use_excerpt ) ? today_get_excerpt( $post, $excerpt_length ) : '';
	$subhead        = ( $use_subhead ) ? today_get_feature_subhead( $post ) : '';
	$thumbnail_size = ( $type === 'primary' ) ? 'large' : 'medium_large';
	$thumbnail      = today_get_feature_thumbnail( $post, $thumbnail_size );

	ob_start();
?>
	<article class="feature feature-vertical <?php echo $type_class; ?> mb-4">
		<a href="<?php echo $permalink; ?>" class="feature-link">
			<div class="media-background-container mb-3 feature-thumbnail-wrap">
				<?php echo $thumbnail; ?>
			</div>

			<h2 class="feature-title"><?php echo $title; ?></h2>

			<?php if ( $use_excerpt && $excerpt ): ?>
			<div class="feature-excerpt"><?php echo $excerpt; ?></div>
			<?php endif; ?>

			<?php if ( $use_subhead && $subhead ): ?>
			<div class="feature-subhead mt-2"><?php echo $subhead; ?></div>
			<?php endif; ?>
		</a>
	</article>
<?php
	return ob_get_clean();
}


/**
 * Displays a condensed, simplified article link.
 * Will not display a Resource Link without a valid permalink.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @param array $args Additional arguments to modify the feature markup. Expects [ucf-post-list] attributes
 * @return string HTML markup
 */
function today_display_feature_condensed( $post, $args=array() ) {
	if ( ! $post instanceof WP_Post ) return;

	$permalink = get_permalink( $post );
	if (
		$post->post_type === 'ucf_resource_link'
		&& function_exists( 'today_resource_link_permalink_is_valid' )
		&& ! today_resource_link_permalink_is_valid( $post )
	) {
		$permalink = null;
	}

	$title     = wptexturize( $post->post_title );
	$subhead   = today_get_feature_subhead( $post );

	ob_start();
	if ( $permalink ):
?>
	<article class="d-flex flex-column align-items-start feature feature-condensed mb-3">
		<a href="<?php echo $permalink; ?>" class="feature-link">
			<h2 class="feature-title mb-1"><?php echo $title; ?></h2>
		</a>

		<?php if ( $subhead ): ?>
		<div class="feature-subhead"><?php echo $subhead; ?></div>
		<?php endif; ?>
	</article>
<?php
	endif;
	return ob_get_clean();
}

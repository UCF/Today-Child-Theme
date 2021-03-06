<?php
/**
 * Functions for displaying content on the homepage
 */

/**
 * Returns latest posts markup for the homepage.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param bool $primary Whether this is pulling the primary row.
 * @return string HTML markup
 */
function today_get_homepage_latest( $primary=false ) {
	$posts = get_posts( array(
		'numberposts' => 10
	) );

	if ( count( $posts ) ) {
		$first_post = array_shift( $posts );
	}

	ob_start();
?>
	<?php if ( $first_post && $primary ): ?>
		<div class="pb-4">
			<?php echo today_display_feature_horizontal( $first_post, array( 'layout__type' => 'primary' ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $primary ) return ob_get_clean(); ?>

	<?php if ( $posts ): ?>
		<div class="row">
			<?php foreach ( $posts as $post ): ?>
				<div class="col-md-4 mb-4">
					<?php echo today_display_feature_vertical( $post ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( ! $first_post && ! $posts ): ?>
		<p>No results found.</p>
	<?php endif; ?>
<?php
	return ob_get_clean();
}

/**
 * Returns curated posts markup for the homepage.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param int $post_id ID of the homepage post
 * @param bool $primary If the content being returned is intended for the primary content area
 * @return string HTML markup
 */
function today_get_homepage_curated( $post_id, $primary=false ) {
	$field_name = $primary ? 'primary_content_curated_stories' : 'curated_stories';

	$markup = '';

	if ( have_rows( $field_name, $post_id ) ) {
		while ( have_rows( $field_name, $post_id ) ) : the_row();
			switch ( get_row_layout() ) {
				case 'primary_row' :
					$posts = array( get_sub_field( 'post' ) );
					$atts  = array_filter( array(
						'layout'         => get_sub_field( 'layout' ),
						'layout__type'   => 'primary',
						'show_image'     => get_sub_field( 'show_image' ),
						'show_excerpt'   => get_sub_field( 'show_excerpt' ),
						'show_subhead'   => get_sub_field( 'show_subhead' ),
						'posts_per_row'  => 1
					) );

					// Explicitly set excerpt length on primary rows when it's the first row.
					$atts['excerpt_length'] = $primary ? TODAY_DEFAULT_EXCERPT_LENGTH : TODAY_SHORT_EXCERPT_LENGTH;

					$markup .= today_post_list_display_feature( null, $posts, $atts );
					break;
				case 'secondary_row' :
					$posts = array();
					if ( have_rows( 'posts' ) ) {
						while ( have_rows( 'posts' ) ): the_row();
							$posts[] = get_sub_field( 'post' );
						endwhile;
					}

					$atts = array_filter( array(
						'layout'        => get_sub_field( 'layout' ),
						'layout__type'  => 'secondary',
						'show_image'    => get_sub_field( 'show_image' ),
						'show_excerpt'  => get_sub_field( 'show_excerpt' ),
						'show_subhead'  => get_sub_field( 'show_subhead' ),
						'posts_per_row' => get_sub_field( 'posts_per_row' ) ?: 3
					) );

					$markup .= today_post_list_display_feature( null, $posts, $atts );
					break;
				case 'condensed_row' :
					$posts = array();
					if ( have_rows( 'posts' ) ) {
						while ( have_rows( 'posts' ) ): the_row();
							$posts[] = get_sub_field( 'post' );
						endwhile;
					}

					$atts = array_filter( array(
						'layout'        => 'condensed',
						'posts_per_row' => 1
					) );

					$markup .= today_post_list_display_feature( null, $posts, $atts );
					break;
				case 'custom' :
					$markup .= get_sub_field( 'custom_content' );
					break;
				default :
					break;
			}
		endwhile;
	}

	return $markup;
}


/**
 * Returns markup for the homepage, depending on homepage settings.
 * Excludes sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param int $post_id ID of the homepage post
 * @param bool $primary If the content being returned is intended for the primary content area
 * @return string HTML markup
 */
function today_get_homepage_content( $post_id, $primary=false ) {
	$content      = '';
	$content_type = get_field( 'page_content_type', $post_id );
	$expiration   = get_field( 'curated_list_expiration', $post_id );
	$expiration   = $expiration ? new DateTime( $expiration ) : null;
	$today        = new DateTime( current_time( 'mysql' ) );

	// Update content type value and flush the existing
	// expiration date if a curated list is set, but has expired:
	if (
		$content_type === 'curated'
		&& $expiration
		&& $today > $expiration
	) {
		$content_type = 'latest';
		update_field( 'page_content_type', $content_type, $post_id );
		update_field( 'curated_list_expiration', '', $post_id );
	}

	switch ( $content_type ) {
		case 'latest':
			$content = today_get_homepage_latest( $primary );
			break;
		case 'curated':
			$content = today_get_homepage_curated( $post_id, $primary );
			break;
		case 'custom':
		default:
			if ( $primary ) return '';
			ob_start();
			the_content();
			$content = ob_get_clean();
			break;
	}

	return $content;
}

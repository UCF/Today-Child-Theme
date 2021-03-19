<?php
/**
 * Functions related to Pegasus stories and their
 * display on the frontend
 */


/**
 * TODO Returns the latest active Pegasus issue.
 *
 * @since TODO
 * @author TODO
 * @return TODO
 */
function today_get_pegasus_current_issue() {
	return true;
}


/**
 * Returns HTML for an inlined SVG Pegasus logo.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @return string HTML markup
 */
function today_get_pegasus_logo() {
	$svg      = '';
	$filename = TODAY_THEME_DIR . 'static/img/pegasus-logo.svg';

	if ( file_exists( $filename ) ) {
		$file_contents = file_get_contents( $filename );
		if ( $file_contents ) {
			// Strip XML header tag for inlining the SVG:
			$file_contents = str_ireplace( '<?xml version="1.0" encoding="utf-8"?>', '', $file_contents );
			$svg = '<div class="pegasus-inline-logo">' . $file_contents . '</div>';
		}
	}

	return $svg;
}


/**
 * Returns featured posts markup for the Pegasus homepage.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup
 */
function today_get_pegasus_home_featured( $post_id ) {
	return today_get_homepage_curated( $post_id, true );
}


/**
 * TODO Returns markup for the "In This Issue" portion
 * of the Pegasus homepage.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup
 */
function today_get_pegasus_home_in_this_issue( $post_id ) {
	$issue        = today_get_pegasus_current_issue();
	$content_type = get_field( 'issue_content_type', $post_id );
	$content      = '<div class="row">';

	if ( $issue ) {
		ob_start();
	?>
		<div class="col-8 offset-2 col-sm-6 offset-sm-3 col-md-3 offset-md-0 mb-5 mb-md-0 pr-md-4 pr-lg-5 text-center">
			<h2 class="h6 text-uppercase letter-spacing-2 mb-3">In This Issue</h2>
			<a href="#TODO">
				<img class="img-fluid" src="https://placehold.it/320x416/" alt="">
				<strong class="text-uppercase letter-spacing-2 text-secondary d-block mt-3" style="font-size: .8em;">TODO Issue Name Here</strong>
			</a>
			<hr class="hr-primary hr-3 w-25" role="presentation">
		</div>
		<div class="col-md-9">
	<?php
		$content .= ob_get_clean();
	} else {
		$content .= '<div class="col">';
	}

	switch ( $content_type ) {
		case 'curated':
			$content .= today_get_homepage_curated( $post_id, false );
			break;
		case 'latest':
		default:
			$posts = get_posts( array(
				// TODO filter by issue; remove numberposts
				'numberposts' => 12
			) );
			ob_start();
		?>
			<?php if ( $posts ): ?>
			<div class="row">
				<?php foreach ( $posts as $post ): ?>
					<div class="col-md-4 mb-4">
						<?php echo today_display_feature_vertical( $post ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php else: ?>
			<p>No results found.</p>
			<?php endif; ?>
		<?php
			$content .= ob_get_clean();
			break;
	}

	$content .= '</div></div>';

	return $content;
}


/**
 * Returns markup for "The Feed" portion of the Pegasus homepage.
 *
 * TODO this function needs to _exclude_ Pegasus stories
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup
 */
function today_get_pegasus_home_feed( $post_id ) {
	$feed_tags         = get_field( 'the_feed_tags', $post_id );
	$feed_tags_include = get_field( 'the_feed_tag_include', $post_id ) ?: 'tag__in';

	$args = array(
		'numberposts' => 10
	);
	if ( $feed_tags ) {
		$args[$feed_tags_include] = $feed_tags;
	}

	$posts = get_posts( $args );

	ob_start();
?>
	<?php if ( $posts ) : ?>
	<div class="pegasus-feed-row">
		<?php foreach ( $posts as $p ) : ?>
		<div class="pegasus-feed-col">
			<article aria-label="<?php echo esc_attr( $p->post_title ); ?>">
				<a href="<?php echo get_permalink( $p ); ?>">
					<time class="pegasus-feed-item-date" datetime="<?php echo $p->post_date; ?>">
						<?php echo date( 'm/d', strtotime( $p->post_date ) ); ?>
					</time>
					<strong class="pegasus-feed-item-title">
						<?php echo $p->post_title; ?>
					</strong>
				</a>
			</article>
		</div>
		<?php endforeach; ?>
	</div>
	<?php else: ?>
	<p>No stories found.</p>
	<?php endif; ?>
<?php
	return ob_get_clean();
}


/**
 * Modifies query args passed to oEmbed providers for the
 * What's Trending section on the Pegasus homepage.
 *
 * Intended for use in `today_get_pegasus_home_trending()` via
 * the `oembed_fetch_url` filter hook.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param string $provider URL of the oEmbed provider
 * @param string $url URL of the content to be embedded
 * @param array $args Additional arguments for retrieving embed HTML
 * @return string Modified provider URL
 */
function today_pegasus_trending_embed_args( $provider, $url, $args ) {
	// If this looks like a URL for a Twitter timeline,
	// add extra params to make it less ugly:
	if ( strpos( $url, 'https://twitter.com' ) !== false ) {
		$provider = add_query_arg( 'chrome', 'nofooter noborders', $provider );
	}

	return $provider;
}


/**
 * Returns What's Trending markup for the Pegasus homepage.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup
 */
function today_get_pegasus_home_trending( $post_id ) {
	add_filter( 'oembed_fetch_url', 'today_pegasus_trending_embed_args', 10, 3 );
	$trending = get_field( 'trending_content', $post_id );
	remove_filter( 'oembed_fetch_url', 'today_pegasus_trending_embed_args' );

	return $trending;
}


/**
 * Displays events markup for the Pegasus homepage.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup for the events list
 */
function today_get_pegasus_home_events( $post_id ) {
	$content   = '';
	$attrs     = array_filter( array(
		'feed_url' => get_field( 'events_feed_url', $post_id ),
		'layout'   => 'modern_date',
		'limit'    => get_field( 'events_number_of_posts', $post_id ) ?: 3
	) );
	$attr_str  = '';

	$attrs['title'] = '';

	foreach ( $attrs as $key => $val ) {
		$attr_str .= ' ' . $key . '="' . $val . '"';
	}

	$content = do_shortcode( '[ucf-events' . $attr_str . ']No events found.[/ucf-events]' );

	return $content;
}


/**
 * Displays Featured Gallery markup for the Pegasus homepage.
 *
 * @since 1.2.0
 * @author Jo Dickson
 * @param int $post_id ID of the Pegasus homepage post
 * @return string HTML markup for the featured gallery
 */
function today_get_pegasus_home_gallery( $post_id ) {
	$gallery  = get_field( 'featured_gallery', $post_id );

	ob_start();
?>
	<?php
	if ( $gallery ) :
		$category     = today_get_primary_category( $gallery );
		$thumbnail    = '';
		$thumbnail_id = today_get_thumbnail_id( $gallery, true );

		if ( $thumbnail_id ) {
			$thumbnail = ucfwp_get_attachment_image( $thumbnail_id, 'medium_large', false, array(
				'class' => 'img-fluid mt-3',
				'alt' => ''
			) );
		}

		// Use an absolute fallback if a thumbnail couldn't be retrieved
		// for the given post
		if ( ! $thumbnail ) {
			$thumbnail = '<img class="img-fluid mt-3" src="' . TODAY_THEME_IMG_URL . '/default-thumb.jpg" alt="">';
		}
	?>
	<div class="card border-0 bg-faded mx-auto">
		<div class="card-block p-4">
			<a href="<?php echo get_permalink( $gallery ); ?>">
				<h2 class="text-secondary"><?php echo $gallery->post_title; ?></h2>

				<?php if ( $category ) : ?>
				<span class="badge badge-primary"><?php echo $category->name; ?></span>
				<?php endif; ?>

				<?php echo $thumbnail; ?>
			</a>
		</div>
	</div>
	<?php endif; ?>
<?php
	return trim( ob_get_clean() );
}

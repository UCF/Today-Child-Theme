<?php
/**
 * Foundational display functions for displaying links to articles in lists.
 */

/**
 * Displays an article link in a horizontal orientation.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @param string $type A unique layout modifier name
 * @param bool $use_thumbnail Whether or not a thumbnail should be displayed
 * @return string HTML markup
 */
function today_display_feature_horizontal( $post, $type='secondary', $use_thumbnail=true ) {
	if ( ! $post instanceof WP_Post ) { return; }

	$type_class = 'feature-' . sanitize_html_class( $type );
	$permalink  = '#'; // TODO
	$title      = 'Lorem ipsum dolor sit amet'; // TODO
	$excerpt    = 'Consectetur adipiscing elit. Nunc eleifend, metus et sollicitudin convallis, nibh dui porta ipsum, sit amet accumsan dui erat ac mi.'; // TODO
	$thumbnail  = '';
	// TODO get srcset and add to <img>
	$thumbnail_col_class = 'col-4 col-sm-3'; // classes for assumed default $type of 'secondary'

	if ( $use_thumbnail ) {
		$thumbnail = 'https://unsplash.it/640/360/'; // TODO - if thumbnail is enabled and no img is available, use fallback
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
						<img class="media-background object-fit-cover feature-thumbnail" id="TODO" src="<?php echo $thumbnail; ?>" alt="">
					</div>
				</div>
				<?php endif; ?>

				<div class="col">
					<h2 class="feature-title"><?php echo $title; ?></h2>
					<div class="feature-excerpt"><?php echo $excerpt; ?></div>
				</div>
			</div>
		</a>
	</article>
<?php
	return ob_get_clean();
}


/**
 * Displays an article link in a vertical orientation.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @param string $type A unique layout modifier name
 * @return string HTML markup
 */
function today_display_feature_vertical( $post, $type='secondary' ) {
	if ( ! $post instanceof WP_Post ) { return; }

	$type_class = 'feature-' . sanitize_html_class( $type );
	$permalink  = '#'; // TODO
	$title      = 'Lorem ipsum dolor sit amet'; // TODO
	$excerpt    = 'Consectetur adipiscing elit. Nunc eleifend, metus et sollicitudin convallis, nibh dui porta ipsum, sit amet accumsan dui erat ac mi.'; // TODO
	$thumbnail  = 'https://unsplash.it/640/360/'; // TODO - always use thumbnail (display fallback if not set)
	// TODO get srcset and add to <img>

	ob_start();
?>
	<article class="feature feature-vertical <?php echo $type_class; ?> mb-4">
		<a href="<?php echo $permalink; ?>" class="feature-link">
			<div class="media-background-container mb-3 feature-thumbnail-wrap">
				<img class="media-background object-fit-cover feature-thumbnail" id="TODO" src="<?php echo $thumbnail; ?>" alt="">
			</div>

			<h2 class="feature-title"><?php echo $title; ?></h2>
			<div class="feature-excerpt"><?php echo $excerpt; ?></div>
		</a>
	</article>
<?php
	return ob_get_clean();
}


/**
 * Displays a condensed, simplified article link.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @return string HTML markup
 */
function today_display_feature_condensed( $post ) {
	if ( ! $post instanceof WP_Post ) { return; }

	$permalink = '#'; // TODO
	$title     = 'Lorem ipsum dolor sit amet'; // TODO
	$subhead   = 'Consectetur adipiscing'; // TODO

	ob_start();
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
	return ob_get_clean();
}

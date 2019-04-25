<?php
/**
 * Functions related to the display of single posts (stories).
 */

/**
 * Returns either an image or video to display at the top of a
 * story, depending on meta field settings.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML markup for the header media
 */
function today_get_post_header_media( $post ) {
	$media       = '';
	$header_type = get_field( 'header_media_type', $post ) ?: 'image';

	switch ( $header_type ) {
		case 'video':
			$video = get_field( 'post_header_video_url', $post );
			if ( $video ) {
				ob_start();
?>
				<div class="mb-4 mb-md-5">
					<?php echo $video; ?>
				</div>
<?php
				$media = ob_get_clean();
			}
			break;
		case 'image':
		default:
			$img         = get_field( 'post_header_image', $post );
			$thumb_size  = get_page_template_slug( $post ) === '' ? 'large' : 'medium_large';
			$img_html    = '';
			$min_width   = 730;  // Minimum acceptable, non-fluid width of a <figure>.
								     // Loosely based on maximum size of post content column in default and two-col templates.
								     // Should be a width that comfortably fits one or more lines of an image caption.
			$max_width   = 1140; // Default max-width value for <figure>
			$thumb_width = 0;    // Default calculated width of the thumbnail at $thumb_size.
			$caption     = '';

			if ( $img ) {
				$img_html  = ucfwp_get_attachment_image( $img['ID'], $thumb_size, false, array(
					'class' => 'img-fluid post-header-image'
				) );

				// Calculate a max-width for the <figure> here so that
				// an included image caption doesn't exceed the width
				// of the image.
				//
				// Allow larger images (those above the $min_width threshold)
				// to be centered in the story without a visible gray
				// background.
				// For smaller images, display them centered within a
				// full-width div with a gray bg.
				if ( isset( $img['sizes'][$thumb_size . '-width'] ) ) {
					$thumb_width = intval( $img['sizes'][$thumb_size . '-width'] );
				}
				if ( $thumb_width >= $min_width ) {
					$max_width = $thumb_width;
				}
			}

			if ( $img_html ) {
				ob_start();
				$caption = wptexturize( $img['caption'] );
?>
				<figure class="figure d-block mb-4 mb-md-5 mx-auto" style="max-width: <?php echo $max_width; ?>px;">
					<div class="bg-faded text-center">
						<?php echo $img_html; ?>
					</div>

					<?php if ( $caption ): ?>
					<figcaption class="figure-caption mt-2">
						<?php echo $caption; ?>
					</figcaption>
					<?php endif; ?>
				</figure>
<?php
				$media = ob_get_clean();
			}
			break;
	}

	return $media;
}


/**
 * Returns markup for a single post's metadata (author, publish date, etc.)
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML markup for the metadata content
 */
function today_get_post_meta_info( $post ) {
	$date_format   = 'F j, Y';
	$byline        = get_field( 'post_author_byline', $post ) ?: wptexturize( get_the_author() );
	$updated_date  = date( $date_format, strtotime( $post->post_date ) );
	$orig_date_val = get_field( 'post_header_publish_date', $post );
	$original_date = isset( $orig_date_val ) ? date( $date_format, strtotime( $orig_date_val ) ) : $updated_date;

	ob_start();
?>
	<div class="small text-uppercase letter-spacing-3">
		<p class="mb-0">
			<span>By <?php echo $byline; ?></span>
			<span class="hidden-xs-down px-1" aria-hidden="true">|</span>
			<span class="d-block d-sm-inline"><?php echo $original_date; ?></span>
		</p>

		<?php if ( $updated_date !== $original_date ) : ?>
		<p class="mt-1 mb-0">
			<strong>Updated</strong> <?php echo date( $date_format, strtotime( $updated_date ) ); ?>
		</p>
		<?php endif; ?>
	</div>
<?php
	$html = ob_get_clean();
	return $html;
}


/**
 * Returns markup for a story's author bio.
 *
 * Adapted from Today-Bootstrap
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param object $post The WP_Post object
 * @return string Author bio markup
 */
function today_get_post_author_bio( $post ) {
	$author_title = get_field( 'post_author_title', $post );

	$author_byline = trim( get_field( 'post_author_byline', $post ) );
	$author_byline = ( $author_byline !== '' ) ? $author_byline : get_the_author();

	$author_bio = trim( get_field( 'post_author_bio', $post ) );

	ob_start();
	if ( $author_bio ) :
?>
	<address class="text-default-aw">
		<span class="d-block font-weight-bold">
			<?php echo $author_byline; ?>
		</span>

		<?php if ( $author_title ) : ?>
		<span class="d-block">
			<?php echo $author_title; ?>
		</span>
		<?php endif; ?>

		<div class="font-italic mt-3">
			<?php echo $author_bio; ?>
		</div>
	</address>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Returns source markup for the given post.
 *
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML markup for the source content
 */
function today_get_post_source( $post ) {
	$source = get_field( 'post_source', $post );

	ob_start();
	if ( $source ):
?>
	<div class="small my-4 my-md-5">
		<?php echo $source; ?>
	</div>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Returns a stylized list of related stories for a given post.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML for the related posts list
 */
function today_get_post_related( $post ) {
	$primary_tag = today_get_primary_tag( $post );
	if ( ! $primary_tag ) return;

	$posts = get_posts( array(
		'numberposts'  => 8,
		'post__not_in' => array( $post->ID ),
		'tag_id'       => $primary_tag->term_id
	) );

	ob_start();
	if ( $posts ):
?>
	<h2 class="text-center h6 text-uppercase mb-4 pb-2">
		Related Stories
	</h2>
	<div class="row">
	<?php foreach ( $posts as $p ): ?>
		<div class="col-md-6 col-lg-3">
			<?php echo today_display_feature_vertical( $p, array( 'show_excerpt' => false ) ); ?>
		</div>
	<?php endforeach; ?>
	</div>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Returns a stylized list of additional headlines, excluding
 * the given post.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML for the headlines list
 */
function today_get_post_more_headlines( $post ) {
	$posts = get_posts( array(
		'numberposts'  => 4,
		'post__not_in' => array( $post->ID )
	) );

	ob_start();
	if ( $posts ):
?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">More Headlines</h2>
	<?php
	foreach ( $posts as $p ) {
		echo today_display_feature_condensed( $p );
	}
	?>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Returns a stylized list of additional stories with the
 * primary category assigned to the given post.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML for the posts list
 */
function today_get_post_cat_headlines( $post ) {
	$primary_cat = today_get_primary_category( $post );
	if ( ! $primary_cat ) return;

	$posts = get_posts( array(
		'numberposts'  => 3,
		'post__not_in' => array( $post->ID ),
		'cat'          => $primary_cat->term_id
	) );

	ob_start();
	if ( $posts ):
?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">
		More About <?php echo wptexturize( $primary_cat->name ); ?>
	</h2>
	<?php
	foreach ( $posts as $p ) {
		echo today_display_feature_condensed( $p );
	}
	?>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Returns a stylized list of additional stories with the
 * primary tag assigned to the given post.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return string HTML for the posts list
 */
function today_get_post_tag_headlines( $post ) {
	$primary_tag = today_get_primary_tag( $post );
	if ( ! $primary_tag ) return;

	$posts = get_posts( array(
		'numberposts'  => 3,
		'post__not_in' => array( $post->ID ),
		'tag_id'       => $primary_tag->term_id
	) );

	ob_start();
	if ( $posts ):
?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">
		More About <?php echo wptexturize( $primary_tag->name ); ?>
	</h2>
	<?php
	foreach ( $posts as $p ) {
		echo today_display_feature_condensed( $p );
	}
	?>
<?php
	endif;
	return ob_get_clean();
}

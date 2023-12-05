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
	// Use custom author byline or Author term name, or fall back to original publisher
	$author_data   = today_get_post_author_data( $post, true );
	$byline        = $author_data['name'] ?? '';

	$date_format   = 'F j, Y';
	$updated_date  = date( $date_format, strtotime( $post->post_date ) );
	$orig_date_val = get_field( 'post_header_publish_date', $post );
	$original_date = ! empty( $orig_date_val ) ? date( $date_format, strtotime( $orig_date_val ) ) : $updated_date;

	ob_start();
?>
	<div class="small letter-spacing-3">
		<p class="mb-0">
			<?php if ( $byline ) : ?>
			<span>By <?php echo $byline; ?></span>
			<span class="hidden-xs-down px-1" aria-hidden="true">|</span>
			<?php endif; ?>

			<?php if ( $original_date === $updated_date ) : ?>
			<span class="d-block d-sm-inline"><?php echo $original_date; ?></span>
			<?php else : ?>
			<span class="d-block d-sm-inline"><?php echo date( $date_format, strtotime( $updated_date ) ); ?></span>
			<?php endif; ?>
		</p>

		<?php if ( $original_date !== $updated_date ) : ?>
		<p class="mt-1 mb-0">
			<strong>Originally Published</strong> <?php echo $original_date; ?>
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
	$author_data = today_get_post_author_data( $post );
	$author_bio  = $author_data['bio'] ?? null;

	ob_start();
	if ( $author_bio ) :
		$author_byline     = $author_data['name'] ?? null;
		$author_title      = $author_data['title'] ?? null;
		$author_photo_data = $author_data['photo'] ?? null;
		$author_photo      = $author_photo_data['sizes']['medium'] ?? null;
		$author_photo_w    = $author_photo_data['sizes']['medium-width'] ?? null;
		$author_photo_h    = $author_photo_data['sizes']['medium-height'] ?? null;
		$author_photo_dims = '';
		if ( $author_photo_w ) {
			$author_photo_dims .= 'width="' . $author_photo_w . '" ';
		}
		if ( $author_photo_h ) {
			$author_photo_dims .= 'height="' . $author_photo_h . '"';
		}
?>
		<address>
			<div class="row">
				<?php if ( $author_photo ) : ?>
				<div class="col-auto" style="max-width: 25%;">
					<img class="img-fluid" src="<?php echo $author_photo; ?>" alt="" <?php echo $author_photo_dims; ?>>
				</div>
				<?php endif; ?>

				<div class="col pl-2 pl-md-3">
					<?php if ( $author_byline ): ?>
					<span class="d-block font-weight-bold">
						<?php echo $author_byline; ?>
					</span>
					<?php endif; ?>

					<?php if ( $author_title ) : ?>
					<span class="d-block">
						<?php echo $author_title; ?>
					</span>
					<?php endif; ?>

					<div class="font-size-sm mt-3">
						<?php echo $author_bio; ?>
					</div>
				</div>
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
 * Returns an array of post objects to use when displaying
 * a single post's additional headlines.
 *
 * @since 1.0.6
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return array list of WP_Post objects
 */
function today_get_post_more_headlines_posts( $post ) {
	return get_posts( array(
		'numberposts'  => 4,
		'post__not_in' => array( $post->ID )
	) );
}


/**
 * Returns an array of post objects to use when displaying
 * a single post's related posts by primary category.
 *
 * @since 1.0.6
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @param array $exclude array of post IDs to exclude from the returned list
 * @return array list of WP_Post objects
 */
function today_get_post_cat_headlines_posts( $post, $exclude=array() ) {
	$primary_cat = today_get_primary_category( $post );
	if ( ! $primary_cat ) return array();

	$exclude = array_merge( array( $post->ID ), $exclude );

	return get_posts( array(
		'numberposts'  => 3,
		'post__not_in' => $exclude,
		'cat'          => $primary_cat->term_id
	) );
}


/**
 * Returns an array of post objects to use when displaying
 * a single post's related posts by primary tag.
 *
 * @since 1.0.6
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @param array $exclude array of post IDs to exclude from the returned list
 * @return array list of WP_Post objects
 */
function today_get_post_tag_headlines_posts( $post, $exclude=array() ) {
	$primary_tag = today_get_primary_tag( $post );
	if ( ! $primary_tag ) return;

	$exclude = array_merge( array( $post->ID ), $exclude );

	return get_posts( array(
		'numberposts'  => 3,
		'post__not_in' => $exclude,
		'tag_id'       => $primary_tag->term_id
	) );
}


/**
 * Returns a stylized list of additional headlines for the given post.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @param array $headlines Custom list of WP_Post objects to use as headlines
 * @return string HTML for the headlines list
 */
function today_get_post_more_headlines( $post, $headlines=array() ) {
	$headlines = $headlines ?: today_get_post_more_headlines_posts( $post );

	ob_start();
	if ( $headlines ):
?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">More Headlines</h2>
	<?php
	foreach ( $headlines as $h ) {
		echo today_display_feature_condensed( $h );
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
 * @param array $headlines Custom list of WP_Post objects to use as headlines
 * @return string HTML for the posts list
 */
function today_get_post_cat_headlines( $post, $headlines=array() ) {
	$primary_cat = today_get_primary_category( $post );
	if ( ! $primary_cat ) return;

	$headlines = $headlines ?: today_get_post_cat_headlines_posts( $post );

	ob_start();
	if ( $headlines ):
?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">
		More About <?php echo wptexturize( $primary_cat->name ); ?>
	</h2>
	<?php
	foreach ( $headlines as $h ) {
		echo today_display_feature_condensed( $h );
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
 * @param array $headlines Custom list of WP_Post objects to use as headlines
 * @return string HTML for the posts list
 */
function today_get_post_tag_headlines( $post, $headlines=array() ) {
	$primary_tag = today_get_primary_tag( $post );
	if ( ! $primary_tag ) return;

	$headlines = $headlines ?: today_get_post_tag_headlines_posts( $post );

	ob_start();
	if ( $headlines ):
?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">
		More About <?php echo wptexturize( $primary_tag->name ); ?>
	</h2>
	<?php
	foreach ( $headlines as $h ) {
		echo today_display_feature_condensed( $h );
	}
	?>
<?php
	endif;
	return ob_get_clean();
}

function today_add_tags_to_data_layer() {
	// If this isn't a single post, return.
	if ( ! is_singular( 'post' ) ) return;

	global $post;

	$gtm_id = get_theme_mod( 'gtm_id' );
	if ( $gtm_id ) :
		$terms = wp_get_post_terms( $post->ID, 'post_tag', array( 'fields' => 'names') );
?>
<script>
<?php foreach( $terms as $term ) : ?>
window.dataLayer.push({
	'event': 'tagPushed',
	'tag': <?php echo json_encode( $term ); ?>
});
<?php endforeach; ?>
</script>
<?php
	endif;
}

add_action( 'wp_head', 'today_add_tags_to_data_layer', 3 );


/**
 * Adds the ACF Post Custom Fields field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_post_custom_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Blurb underneath the related stories
		$fields[] = array(
			array(
				'key' => 'field_656f49bff52a33',
				'label' => 'Related blurb',
				'name' => 'rel_st_blurb',
				'type' => 'textarea',
			),
		);

		// Adds Header Content tab
		$fields[] = array(
			'key'               => 'field_5c813914b0cd8',
			'label'             => 'Header Contents',
			'type'              => 'tab',
		);

		$fields[] = array(
			'key'               => 'field_5c813914b0cd8',
			'label'             => 'Header Contents',
			'type'              => 'tab',
		);

		// Adds Original Publish Date read only field
		$fields[] = array(
			'key'               => 'field_5c813a34c81af',
			'label'             => 'Original Publish Date',
			'name'              => 'post_header_publish_date',
			'type'              => 'read_only',
			'instructions'      => 'The date the post was originally published. Changing the <a href="#submitdiv">\'Published on\' date</a> above will update the dates listed in the story’s header and bump this story to the top of lists where it\'s referenced.',
			'display_type'      => 'text',
		);

		// Adds Deck field
		$fields[] = array(
			'key'               => 'field_5c813eaac81b1',
			'label'             => 'Deck',
			'name'              => 'post_header_deck',
			'type'              => 'wysiwyg',
			'instructions'      => 'Appears below the title on a single post.	Is also used as excerpt text within lists of posts.',
			'toolbar'           => 'inline_text',
			'media_upload'      => 0,
		);

		// Adds Header Media tab
		$fields[] = array(
			'key'               => 'field_5c81401dc81ba',
			'label'             => 'Header Media',
			'type'              => 'tab',
		);

		// Adds Header Media Type field
		$fields[] = array(
			'key'               => 'field_5c813fb7c81b9',
			'label'             => 'Header Media Type',
			'name'              => 'header_media_type',
			'type'              => 'radio',
			'instructions'      => 'Select the type of header for this story.',
			'choices'           => array(
				'image' => 'Image',
				'video' => 'Video',
			),
			'default_value'     => 'image',
		);

		// Adds Header Video field
		$fields[] = array(
			'key'               => 'field_5c814048c81bb',
			'label'             => 'Header Video',
			'name'              => 'post_header_video_url',
			'type'              => 'oembed',
			'instructions'      => 'Paste in a video URL from YouTube, Vimeo, etc. to display in place of a header image.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5c813fb7c81b9',
						'operator' => '==',
						'value'    => 'video',
					),
				),
			),
		);

		// Adds Header/Thumbnail Image field
		$fields[] = array(
			'key'               => 'field_5c813f8ac81b8',
			'label'             => 'Header/Thumbnail Image',
			'name'              => 'post_header_image',
			'type'              => 'image',
			'instructions'      => 'Select or upload an image with dimensions of 1200x800 for this story. The image file size must be less than 800KB.<br><br>When the "Header Media Type" value is set to "Image", this image will be used as the header image on the story, as well as the thumbnail image when this story is displayed in lists of stories.<br><br>When the "Header Media Type" value is set to "Video", if an image is provided, the image will be used as the thumbnail when this story is displayed in lists of stories.	If no image is provided, WordPress will attempt to fetch and use a poster image dynamically based on the "Header Video" URL provided.',
			'max_width'         => 1200,
			'max_height'        => 800,
			'mime_types'        => 'jpg, jpeg',
		);

		// Adds Author tab
		$fields[] = array(
			'key'               => 'field_5c813f04c81b3',
			'label'             => 'Author',
			'type'              => 'tab',
		);

		// Adds Author Type field
		$fields[] = array(
			'key'               => 'field_60351738e9e93',
			'label'             => 'Author Type',
			'name'              => 'post_author_type',
			'type'              => 'select',
			'instructions'      => 'Choose whether to reference an existing Author\'s information, or define one-off custom information for this post.',
			'choices'           => array(
				'custom' => 'Custom',
				'term'   => 'Existing Author',
			),
			'default_value'     => false,
		);

		// Adds Author Byline field
		$fields[] = array(
			'key'               => 'field_5c813f0fc81b4',
			'label'             => 'Author Byline',
			'name'              => 'post_author_byline',
			'type'              => 'text',
			'instructions'      => 'Appears in place of post author\'s name.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Adds Author Title field
		$fields[] = array(
			'key'               => 'field_5c813ebec81b2',
			'label'             => 'Author Title',
			'name'              => 'post_author_title',
			'type'              => 'text',
			'instructions'      => 'Appears under the author\'s name/byline below the story\'s content, <b>only if the Author Bio is set</b>.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Adds Author Photo field
		$fields[] = array(
			'key'               => 'field_602fef6061511',
			'label'             => 'Author Photo',
			'name'              => 'post_author_photo',
			'type'              => 'image',
			'instructions'      => 'Appears below the story\'s content.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'wrapper'           => array(
				'width' => '35',
			),
		);

		// Adds Author Bio field
		$fields[] = array(
			'key'               => 'field_5c813f22c81b5',
			'label'             => 'Author Bio',
			'name'              => 'post_author_bio',
			'type'              => 'wysiwyg',
			'instructions'      => 'Appears below the story\'s content.<br><br>This field must be set in order to display any author information (Author Name, Title, Bio, Photo) below the story\'s content.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'wrapper'           => array(
				'width' => '65',
			),
			'tabs'              => 'text',
			'media_upload'      => 0,
		);

		// Adds Existing Author field
		$fields[] = array(
			'key'               => 'field_6035185de9e94',
			'label'             => 'Existing Author',
			'name'              => 'post_author_term',
			'type'              => 'taxonomy',
			'instructions'      => 'Choose an existing Author to assign to this post.	Name, title, photo and bio information will be referenced from the Author that\'s selected.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'term',
					),
				),
			),
			'taxonomy'          => 'tu_author',
			'field_type'        => 'select',
			'save_terms'        => 1,
			'load_terms'        => 1,
		);

		// Adds Primary Tag tab
		$fields[] = array(
			'key'               => 'field_5c814082c81bc',
			'label'             => 'Primary Tag',
			'type'              => 'tab',
		);

		// Adds Primary Tag field
		$fields[] = array(
			'key'               => 'field_5c8140a1c81bd',
			'label'             => 'Primary Tag',
			'name'              => 'post_primary_tag',
			'type'              => 'taxonomy',
			'instructions'      => 'Select the primary tag that will be used to populate the Related Stories section.',
			'taxonomy'          => 'post_tag',
			'field_type'        => 'select',
			'add_term'          => 0,
			'allow_null'        => 1,
			'return_format'     => 'object',
		);

		// Adds Source tab
		$fields[] = array(
			'key'               => 'field_5c8140dec81be',
			'label'             => 'Source',
			'type'              => 'tab',
		);

		// Adds Source field
		$fields[] = array(
			'key'               => 'field_5c8140e7c81bf',
			'label'             => 'Source',
			'name'              => 'post_source',
			'type'              => 'textarea',
			'instructions'      => 'Appears below the story content (and below the Author Bio if set).',
		);

		// Adds Tag Cloud tab
		$fields[] = array(
			'key'               => 'field_5c98fd5aeb16b',
			'label'             => 'Tag Cloud',
			'type'              => 'tab',
		);


		// Adds Display Tag Cloud field
		$fields[] = array(
			'key'               => 'field_5c98fd68eb16c',
			'label'             => 'Display Tag Cloud',
			'name'              => 'post_display_tag_cloud',
			'type'              => 'true_false',
			'instructions'      => 'Display a tag cloud under the post\'s content or source. Defaults to true.',
			'default_value'     => 1,
			'ui'                => 1,
		);

		// Adds Tag Cloud Count field
		$fields[] = array(
			'key'               => 'field_5c98fdc5eb16d',
			'label'             => 'Tag Cloud Count',
			'name'              => 'post_tag_cloud_count',
			'type'              => 'number',
			'instructions'      => 'The number of tags to show in the tag cloud. Defaults to 5.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5c98fd68eb16c',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'default_value'     => 5,
			'min'               => 1,
			'step'              => 1,
		);

		// Defines Post Custom Fields field group
		$field_group = array(
			'key'                   => 'group_5c813326f2f21',
			'title'                 => 'Post Custom Fields',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
				),
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_post_custom_fields' );


/**
 * Adds the ACF Main Site News field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_main_site_news_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Promote on Main Site field
		$fields[] = array(
			'key'               => 'field_5c9e1c1c15df3',
			'label'             => 'Promote on Main Site',
			'name'              => 'post_main_site_story',
			'type'              => 'true_false',
			'instructions'      => 'When checked, the story will appear in the news feed on UCF.edu.',
			'ui'                => 1,
		);

		// Defines Main Site News field group
		$field_group = array(
			'key'                   => 'group_5c9e1c1417520',
			'title'                 => 'Main Site News',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
					array(
						'param'    => 'current_user_role',
						'operator' => '==',
						'value'    => 'administrator',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
					array(
						'param'    => 'current_user_role',
						'operator' => '==',
						'value'    => 'super_admin',
					),
				),
			),
			'position'              => 'side',
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_main_site_news_fields' );

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


/**
 * Adds the ACF Pegasus Homepage Content field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_pegasus_homepage_content_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Featured Stories tab
		$fields[] = array(
			'key'               => 'field_604a706de57a9',
			'label'             => 'Featured Stories',
			'type'              => 'tab',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_604905e816f87',
						'operator' => '==',
						'value'    => 'curated',
					),
				),
			),
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604a7155e57ad',
			'type' => 'message',
			'message' => '<em>Content block displayed at the top of the page.</em>',
		);

		// Adds Featured Stories clone field
		$fields[] = array(
			'key' => 'field_604905e816f9d',
			'label' => 'Featured Stories',
			'name' => 'primary_content',
			'type' => 'clone',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_604905e816f87',
						'operator' => '==',
						'value' => 'curated',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'clone' => array(
				0 => 'field_5cac9fb846af9',
			),
			'display' => 'seamless',
			'layout' => 'block',
			'prefix_label' => 0,
			'prefix_name' => 1,
		);

		// Adds The Feed tab
		$fields[] = array(
			'key' => 'field_604f8975b6ad2',
			'label' => 'The Feed',
			'type' => 'tab',
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604f8bd0b6ad8',
			'type' => 'message',
			'message' => '<em>Section that contains a compact list of UCF Today stories.</em>',
		);

		// Adds Filter Posts by Tag
		$fields[] = array(
			'key' => 'field_60510dfe21be1',
			'label' => 'Filter Posts by Tag',
			'name' => 'the_feed_tags',
			'type' => 'taxonomy',
			'instructions' => 'Specify one or more tags to filter posts in The Feed section by.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'post_tag',
			'field_type' => 'multi_select',
			'allow_null' => 0,
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'multiple' => 0,
		);

		// Adds Tag Filtering By field
		$fields[] = array(
			'key' => 'field_60510ea621be2',
			'label' => 'Tag Filtering By',
			'name' => 'the_feed_tag_include',
			'type' => 'select',
			'instructions' => 'Posts included in The Feed section must have:',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_60510dfe21be1',
						'operator' => '!=empty',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'tag__in' => 'Any of these tags',
				'tag__and' => 'All of these tags',
			),
			'default_value' => false,
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		);

		// Adds What's Trending tab
		$fields[] = array(
			'key' => 'field_604924a5f3b33',
			'label' => 'What\'s Trending',
			'type' => 'tab',
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_604905e816f87',
						'operator' => '==',
						'value' => 'curated',
					),
				),
			),
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604a7131e57ac',
			'type' => 'message',
			'message' => '<em>Section for promoting trending social content.</em>',
		);

		// Adds Content field
		$fields[] = array(
			'key' => 'field_60493cc0f3b34',
			'label' => 'Content',
			'name' => 'trending_content',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'all',
			'toolbar' => 'basic',
			'media_upload' => 1,
			'delay' => 1,
		);

		// Adds In This Issue tab
		$fields[] = array(
			'key' => 'field_604a70a8e57aa',
			'label' => 'In This Issue',
			'type' => 'tab',
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_604905e816f87',
						'operator' => '==',
						'value' => 'curated',
					),
				),
			),
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604a7102e57ab',
			'type' => 'message',
			'message' => '<em>Section that displays a link to the latest active issue and a list of stories in the issue.</em>',
		);

		// Adds issue_content_type field
		$fields[] = array(
			'key' => 'field_60493d35f3b35',
			'label' => 'How should these stories be displayed?',
			'name' => 'issue_content_type',
			'type' => 'radio',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'latest' => 'Latest stories in the current active issue',
				'curated' => 'Curated story list',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'default_value' => 'latest',
			'layout' => 'vertical',
			'return_format' => 'value',
			'save_other_choice' => 0,
		);

		// Adds curated stories field
		$fields[] = array(
			'key'               => 'field_604905e816fa5',
			'label'             => 'Curated Stories',
			'name'              => 'curated_stories',
			'type'              => 'flexible_content',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60493d35f3b35',
						'operator' => '==',
						'value'    => 'curated',
					),
				),
			),
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'layouts'           => array(
				'5cac9fc2da26b' => array(
					'key'        => '5cac9fc2da26b',
					'name'       => 'primary_row',
					'label'      => 'Primary Story',
					'display'    => 'block',
					'sub_fields' => array(
						array(
							'key'               => 'field_604905e827c8d',
							'label'             => 'Select a Story',
							'name'              => 'post',
							'type'              => 'post_object',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'post_type' => array(
								0 => 'post',
							),
							'taxonomy' => array(
							),
							'allow_null'    => 0,
							'multiple'      => 0,
							'return_format' => 'object',
							'ui'            => 1,
						),
						array(
							'key'               => 'field_604905e827c97',
							'label'             => 'Orientation',
							'name'              => 'layout',
							'type'              => 'select',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '40',
								'class' => '',
								'id'    => '',
							),
							'choices' => array(
								'horizontal' => 'Horizontal',
								'vertical'   => 'Vertical',
							),
							'default_value' => 'vertical',
							'allow_null'    => 0,
							'multiple'      => 0,
							'ui'            => 0,
							'ajax'          => 0,
							'return_format' => 'value',
							'placeholder'   => '',
						),
						array(
							'key'               => 'field_604905e827c9f',
							'label'             => 'Show Thumbnail',
							'name'              => 'show_image',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '20',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_604905e827ca7',
							'label'             => 'Show Excerpt',
							'name'              => 'show_excerpt',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '20',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_604905e827cae',
							'label'             => 'Show Subhead',
							'name'              => 'show_subhead',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '20',
							),
						),
					),
					'min' => '',
					'max' => '',
				),
				'5caca77046afc' => array(
					'key'        => '5caca77046afc',
					'name'       => 'secondary_row',
					'label'      => 'Secondary Stories',
					'display'    => 'block',
					'sub_fields' => array(
						array(
							'key'               => 'field_604905e827cb5',
							'label'             => 'Select Stories',
							'name'              => 'posts',
							'type'              => 'repeater',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'collapsed'    => 'field_5caca86a46b00',
							'min'          => 1,
							'max'          => 0,
							'layout'       => 'block',
							'button_label' => 'Add Story',
							'sub_fields'   => array(
								array(
									'key'               => 'field_604905e841734',
									'label'             => 'Story',
									'name'              => 'post',
									'type'              => 'post_object',
									'instructions'      => '',
									'required'          => 1,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'post_type' => array(
										0 => 'post',
									),
									'taxonomy' => array(
									),
									'allow_null'    => 0,
									'multiple'      => 0,
									'return_format' => 'object',
									'ui'            => 1,
								),
							),
						),
						array(
							'key'               => 'field_604905e827cbc',
							'label'             => 'Orientation',
							'name'              => 'layout',
							'type'              => 'select',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '40',
								'class' => '',
								'id'    => '',
							),
							'choices' => array(
								'horizontal' => 'Horizontal',
								'vertical'   => 'Vertical',
							),
							'default_value' => 'vertical',
							'allow_null'    => 0,
							'multiple'      => 0,
							'ui'            => 0,
							'ajax'          => 0,
							'return_format' => 'value',
							'placeholder'   => '',
						),
						array(
							'key'               => 'field_604905e827cc3',
							'label'             => 'Show Thumbnails',
							'name'              => 'show_thumbnail',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '15',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_604905e827ccb',
							'label'             => 'Show Excerpts',
							'name'              => 'show_excerpt',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '15',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_604905e827cd2',
							'label'             => 'Show Subheads',
							'name'              => 'show_subhead',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '15',
							),
							'default_value' => 0,
						),
						array(
							'key'               => 'field_604905e827cd9',
							'label'             => 'Posts per Row',
							'name'              => 'posts_per_row',
							'type'              => 'select',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '15',
								'class' => '',
								'id'    => '',
							),
							'choices' => array(
								1 => '1',
								2 => '2',
								3 => '3',
								4 => '4',
								6 => '6',
							),
							'default_value' => 3,
							'allow_null'    => 0,
							'multiple'      => 0,
							'ui'            => 0,
							'return_format' => 'value',
							'ajax'          => 0,
							'placeholder'   => '',
						),
					),
					'min' => '',
					'max' => '',
				),
				'5caca9ae46b03' => array(
					'key'        => '5caca9ae46b03',
					'name'       => 'condensed_row',
					'label'      => 'Condensed Stories',
					'display'    => 'block',
					'sub_fields' => array(
						array(
							'key'               => 'field_604905e827ce1',
							'label'             => 'Select Stories',
							'name'              => 'posts',
							'type'              => 'repeater',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'collapsed'    => '',
							'min'          => 1,
							'max'          => 0,
							'layout'       => 'block',
							'button_label' => 'Add Story',
							'sub_fields'   => array(
								array(
									'key'               => 'field_604905e863171',
									'label'             => 'Story',
									'name'              => 'post',
									'type'              => 'post_object',
									'instructions'      => '',
									'required'          => 1,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'post_type' => array(
										0 => 'post',
									),
									'taxonomy' => array(
									),
									'allow_null'    => 0,
									'multiple'      => 0,
									'return_format' => 'object',
									'ui'            => 1,
								),
							),
						),
					),
					'min' => '',
					'max' => '',
				),
				'5caca93746b01' => array(
					'key'        => '5caca93746b01',
					'name'       => 'custom',
					'label'      => 'Custom',
					'display'    => 'block',
					'sub_fields' => array(
						array(
							'key'               => 'field_604905e827ce8',
							'label'             => 'Custom Content',
							'name'              => 'custom_content',
							'type'              => 'wysiwyg',
							'instructions'      => 'Specify arbitrary, custom content to add to this row.',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value' => '',
							'tabs'          => 'all',
							'toolbar'       => 'full',
							'media_upload'  => 1,
							'delay'         => 0,
						),
					),
					'min' => '',
					'max' => '',
				),
			),
			'button_label'      => 'Add Row',
			'min'               => '',
			'max'               => '',
		);

		// Adds Banner Ad tab
		$fields[] = array(
			'key' => 'field_604f8cb0eb43b',
			'label' => 'Banner Ad',
			'type' => 'tab',
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604f8ce5eb43c',
			'type' => 'message',
			'message' => '<em>Optional block of content displayed immediately underneath the In This Issue section. Can be used for promotional items/banner ads, or other arbitrary content.</em>',
		);

		// Adds Content field
		$fields[] = array(
			'key' => 'field_604f8d15eb43d',
			'label' => 'Content',
			'name' => 'banner_content',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'all',
			'toolbar' => 'basic',
			'media_upload' => 0,
			'delay' => 1,
		);

		// Adds Events tab
		$fields[] = array(
			'key' => 'field_604f898fb6ad3',
			'label' => 'Events',
			'type' => 'tab',
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604f8baab6ad7',
			'type' => 'message',
			'message' => '<em>Block of content that contains a stylized list of events from the UCF Events system.</em>',
		);

		// Adds Feed URL field
		$fields[] = array(
			'key' => 'field_6053695300fdd',
			'label' => 'Feed URL',
			'name' => 'events_feed_url',
			'type' => 'url',
			'instructions' => 'UCF Events feed URL. Defaults to the "UCF Events JSON Feed URL" value in the UCF Events plugin.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '65',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
		);

		// Adds Number of Events field
		$fields[] = array(
			'key' => 'field_6053696000fde',
			'label' => 'Number of Events',
			'name' => 'events_number_of_posts',
			'type' => 'number',
			'instructions' => 'The number of events to be displayed. Defaults to 3.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '35',
				'class' => '',
				'id' => '',
			),
			'default_value' => 3,
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'min' => 1,
			'max' => '',
			'step' => 1,
		);

		// Adds Featured Gallery tab
		$fields[] = array(
			'key' => 'field_604f8998b6ad4',
			'label' => 'Featured Gallery',
			'type' => 'tab',
		);

		// Adds message field
		$fields[] = array(
			'key' => 'field_604f8b88b6ad6',
			'type' => 'message',
			'message' => '<em>Section that displays a link to a featured gallery story.</em>',
		);

		// Adds Featured Gallery field
		$fields[] = array(
			'key' => 'field_604f8a20b6ad5',
			'label' => 'Featured Gallery',
			'name' => 'featured_gallery',
			'type' => 'post_object',
			'instructions' => 'Choose a post that should be displayed in this section.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'post',
			),
			'taxonomy' => '',
			'allow_null' => 0,
			'multiple' => 0,
			'return_format' => 'object',
			'ui' => 1,
		);

		// Defines Pegasus Homepage Content field group
		$field_group = array(
			'key'                   => 'group_604905e80fbff',
			'title'                 => 'Pegasus Homepage Content',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
					array(
						'param'    => 'current_user_role',
						'operator' => '==',
						'value'    => 'administrator',
					),
					array(
						'param'    => 'post_template',
						'operator' => '==',
						'value'    => 'template-pegasus_home.php',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
					array(
						'param'    => 'current_user_role',
						'operator' => '==',
						'value'    => 'super_admin',
					),
					array(
						'param'    => 'post_template',
						'operator' => '==',
						'value'    => 'template-pegasus_home.php',
					),
				),
			),
			'hide_on_screen'        => array(
				0 => 'the_content',
				1 => 'excerpt',
				2 => 'featured_image',
				3 => 'categories',
				4 => 'tags',
				5 => 'send-trackbacks',
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_pegasus_homepage_content_fields' );

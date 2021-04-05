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
			$content = get_the_content( null, false, $post_id );
			break;
	}

	return $content;
}


/**
 * Adds the ACF Homepage Fields field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_homepage_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds type of content field
		$fields[] = array(
			'key'               => 'field_5cabb1174f264',
			'label'             => 'What type of content should be displayed on the homepage?',
			'name'              => 'page_content_type',
			'type'              => 'radio',
			'instructions'      => '',
			'required'          => 1,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'choices'           => array(
				'latest'  => 'Latest stories',
				'curated' => 'Curated story list',
				'custom'  => 'Custom page content',
			),
			'allow_null'        => 0,
			'other_choice'      => 0,
			'save_other_choice' => 0,
			'default_value'     => '',
			'layout'            => 'vertical',
			'return_format'     => 'value',
		);

		// Adds Curated List Expiration field
		$fields[] = array(
			'key'               => 'field_5cacb6511b75e',
			'label'             => 'Curated List Expiration',
			'name'              => 'curated_list_expiration',
			'type'              => 'date_time_picker',
			'instructions'      => 'Specify how long this curated list should remain on the homepage.	Leave blank to use this curated list indefinitely.<br>When a curated list expires, the homepage will revert to a list of latest stories.',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5cabb1174f264',
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
			'display_format'    => 'm/d/Y g:i a',
			'return_format'     => 'm/d/Y g:i a',
			'first_day'         => 1,
		);

		// Adds Primary Content field
		$fields[] = array(
			'key'               => 'field_5cdd8f8744eaf',
			'label'             => 'Primary Content',
			'name'              => 'primary_content',
			'type'              => 'clone',
			'instructions'      => 'Content block that appears full width above the two column layout when the sidebar is enabled. Place the primary story here.',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5cabb1174f264',
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
			'clone'             => array(
				0 => 'field_5cac9fb846af9',
			),
			'display'           => 'group',
			'layout'            => 'block',
			'prefix_label'      => 1,
			'prefix_name'       => 1,
		);

		// Adds Curated Stories fields
		$fields[] = array(
			'key'               => 'field_5cac9fb846af9',
			'label'             => 'Curated Stories',
			'name'              => 'curated_stories',
			'type'              => 'flexible_content',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5cabb1174f264',
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
							'key'               => 'field_5caca22246afa',
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
							'key'               => 'field_5caca72a46afb',
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
							'key'               => 'field_5cacaa2646b07',
							'label'             => 'Show Thumbnail',
							'name'              => 'show_image',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '20',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_5cacaa8246b08',
							'label'             => 'Show Excerpt',
							'name'              => 'show_excerpt',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '20',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_5cacaabf46b09',
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
							'key'               => 'field_5caca77046afd',
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
									'key'               => 'field_5caca86a46b00',
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
							'key'               => 'field_5caca77046afe',
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
							'key'               => 'field_5cacac028674d',
							'label'             => 'Show Thumbnails',
							'name'              => 'show_thumbnail',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '15',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_5cacac178674e',
							'label'             => 'Show Excerpts',
							'name'              => 'show_excerpt',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '15',
							),
							'default_value' => 1,
						),
						array(
							'key'               => 'field_5cacac278674f',
							'label'             => 'Show Subheads',
							'name'              => 'show_subhead',
							'type'              => 'true_false',
							'wrapper'           => array(
								'width' => '15',
							),
						),
						array(
							'key'               => 'field_60301395830a5',
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
							'key'               => 'field_5caca9ae46b04',
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
									'key'               => 'field_5caca9ae46b05',
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
							'key'               => 'field_5caca94346b02',
							'label'             => 'Custom Content',
							'name'              => 'custom_content',
							'type'              => 'wysiwyg',
							'instructions'      => 'Specify arbitrary, custom content to add to this row.',
							'required'          => 1,
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

		// Adds Custom Page Content message field
		$fields[] = array(
			'key'               => 'field_5cac9ecc97b7c',
			'label'             => 'Custom Page Content',
			'type'              => 'message',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5cabb1174f264',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Defines Homepage Fields field group
		$field_group = array(
			'key'                   => 'group_5cabb0229b4e9',
			'title'                 => 'Homepage Fields',
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
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
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
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'hide_on_screen'        => array(
				0 => 'excerpt',
				1 => 'page_attributes',
				2 => 'featured_image',
				3 => 'categories',
				4 => 'tags',
				5 => 'send-trackbacks',
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_homepage_fields' );

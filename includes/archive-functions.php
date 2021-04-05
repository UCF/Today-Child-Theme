<?php
/**
 * Provides functions specifically for archive pages
 */
if ( ! function_exists( 'today_archive_pagination' ) ) {
	/**
	 * Returns archive pagination, inserting title if it exists
	 */
	function today_archive_pagination( $title='' ) {
		if ( ! is_date() ) return;

		global $wp_query;

		$current_year  = isset( $wp_query->query['year'] ) ? $wp_query->query['year'] : null;
		$current_month = isset( $wp_query->query['monthnum'] ) ? $wp_query->query['monthnum'] : null;

		$current_date = date_create_from_format( 'm/d/Y', "$current_month/1/$current_year" );

		$prev_month = $current_date->sub( new DateInterval( 'P1M' ) );
		$prev_url = get_month_link( $prev_month->format('Y'), $prev_month->format('m') );

		$args = array(
			'monthnum' => $prev_month->format('m'),
			'year'     => $prev_month->format('Y')
		);

		$posts = new WP_Query( $args );
		$prev_have_posts = $posts->post_count > 0 ? true : false;

		// Reset $current_date as it's effected by ->sub
		$current_date = date_create_from_format( 'm/d/Y', "$current_month/1/$current_year" );

		$next_month = $current_date->add( new DateInterval( 'P1M' ) );
		$next_url = get_month_link( $next_month->format('Y'), $next_month->format('m') );

		$args = array(
			'monthnum' => $next_month->format('m'),
			'year'     => $next_month->format('Y')
		);

		$posts = new WP_Query( $args );
		$next_have_posts = $posts->post_count > 0 ? true : false;

		ob_start();
	?>
		<div class="bg-faded my-4 p-3">
			<div class="row justify-content-between align-items-center">
				<?php if ( $title ) : ?>
				<div class="col-sm-auto col-12 mb-3 mb-sm-0 text-center">
					<?php echo $title; ?>
				</div>
				<?php endif; ?>
				<?php if ( $prev_have_posts ) : ?>
				<div class="col-sm-auto col flex-sm-first text-left">
					<a href="<?php echo $prev_url; ?>" class="btn btn-primary btn-sm"><span class="fa fa-arrow-left" aria-hidden="true"></span> <?php echo $prev_month->format( 'M Y' ); ?></a>
				</div>
				<?php endif; ?>
				<?php if ( $next_have_posts ) : ?>
				<div class="col-sm-auto col text-right">
					<a href="<?php echo $next_url; ?>" class="btn btn-primary btn-sm"><?php echo $next_month->format( 'M Y' ); ?> <span class="fa fa-arrow-right" aria-hidden="true"></span> </a>
				</div>
				<?php endif; ?>
			</div>
		</div>
	<?php
		return ob_get_clean();
	}
}


/**
 * Adds the ACF Category Fields field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_category_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Page Headline field
		$fields[] = array(
			'key'               => 'field_605a2f972160a',
			'label'             => 'Page Headline',
			'name'              => 'category_page_headline',
			'type'              => 'text',
			'instructions'      => 'The H1 headline to be used for this category page. Defaults to the category name + "News" (e.g. for a category named "Community" the default headline would be "Community News").',
		);

		// Adds Page Content field
		$fields[] = array(
			'key'               => 'field_605a366fcfa31',
			'label'             => 'Page Content',
			'name'              => '',
			'type'              => 'tab',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'placement'         => 'top',
			'endpoint'          => 0,
		);

		// Adds Page Content Info field
		$fields[] = array(
			'key'               => 'field_605a38a791640',
			'label'             => 'Page Content Info',
			'name'              => '',
			'type'              => 'message',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'message'           => 'By default, the category page\'s content consists of a single story displayed in the primary vertical layout at the top, followed by 9 posts displayed 3 per-row in the vertical layout. With the sidebar enabled, this content is displayed in a <code>.col-lg-8</code>. If the sidebar is disabled, the content will be displayed in a <code>.col-12</code>.',
			'new_lines'         => 'wpautop',
			'esc_html'          => 0,
		);

		// Adds Customize Page Content field
		$fields[] = array(
			'key'               => 'field_605a36f2cfa32',
			'label'             => 'Customize Page Content',
			'name'              => 'category_customize_page_content',
			'type'              => 'true_false',
			'instructions'      => 'Turn on in order to customize this page\'s content.',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'message'           => '',
			'default_value'     => 0,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		);

		// Adds Custom Page Content field
		$fields[] = array(
			'key'               => 'field_605a37e3cfa33',
			'label'             => 'Custom Page Content',
			'name'              => 'category_custom_page_content',
			'type'              => 'wysiwyg',
			'instructions'      => 'Custom content for this category page.',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_605a36f2cfa32',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'     => '',
			'tabs'              => 'all',
			'toolbar'           => 'full',
			'media_upload'      => 1,
			'delay'             => 0,
		);

		// Adds Sidebar field
		$fields[] = array(
			'key'               => 'field_605a34c3cfa2f',
			'label'             => 'Sidebar',
			'name'              => '',
			'type'              => 'tab',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'placement'         => 'top',
			'endpoint'          => 0,
		);

		// Adds Enable Sidebar Field
		$fields[] = array(
			'key'               => 'field_605a35f5cfa30',
			'label'             => 'Enable Sidebar',
			'name'              => 'category_enable_sidebar',
			'type'              => 'true_false',
			'instructions'      => 'Enables a sidebar on this category page with default sidebar content (UCF In The News, Events at UCF, Resources menu). Options are also available below to customize sidebar contents.',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'message'           => '',
			'default_value'     => 1,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		);

		// Adds Customize Sidebar field
		$fields[] = array(
			'key'               => 'field_605a3333e53f0',
			'label'             => 'Customize Sidebar',
			'name'              => 'category_customize_sidebar',
			'type'              => 'true_false',
			'instructions'      => 'Turn on in order to customize the content and sections in the sidebar.',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_605a35f5cfa30',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'message'           => '',
			'default_value'     => 0,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		);

		// Adds Sidebar Fields clone field
		$fields[] = array(
			'key'               => 'field_605a328ea7992',
			'label'             => 'Sidebar Fields',
			'name'              => 'category_sidebar_fields',
			'type'              => 'clone',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_605a3333e53f0',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'wrapper'           => array(
				'width' => '100',
				'class' => '',
				'id'    => '',
			),
			'clone'             => array(
				0 => 'field_5c9cdf7861887',
			),
			'display'           => 'group',
			'layout'            => 'table',
			'prefix_label'      => 0,
			'prefix_name'       => 0,
		);

		// Defines Category Fields field group
		$field_group = array(
			'key'                   => 'group_605a2f2f7ff27',
			'title'                 => 'Category Fields',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'category',
					),
				),
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_category_fields' );

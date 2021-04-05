<?php
/**
 * Functions related to content within sidebars in templates
 */

/**
 * Generic function for displaying any type of sidebar content.
 * Creates a consistent set of heading and content markup.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $title Optional heading text above the sidebar content
 * @param string $content Content to display in the sidebar
 * @return string Formatted sidebar contents
 */
function today_display_sidebar_content( $title, $content ) {
	ob_start();
	if ( $content ):
?>
<div class="mb-5">
	<?php if ( $title ): ?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">
		<?php echo $title; ?>
	</h2>
	<?php endif; ?>

	<?php echo $content; ?>
</div>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Displays a set of events suitable for inclusion within a sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Associative array of expected arguments + their values
 * @return string HTML markup for the events list
 */
function today_display_sidebar_events( $args=array() ) {
	$title     = isset( $args['title'] ) ? $args['title'] : 'Events at UCF';
	$feed_url  = isset( $args['feed_url'] ) ? $args['feed_url'] : '';
	$layout    = isset( $args['layout'] ) ? $args['layout'] : 'classic';
	$limit     = isset( $args['limit'] ) ? $args['limit'] : '';
	$more_url  = '';
	$more_text = isset( $args['more_text'] ) ? $args['more_text'] : 'View All Events';
	$content   = '';

	if ( isset( $args['more_url'] ) && !empty( $args['more_url'] ) ) {
		$more_url = $args['more_url'];
	}
	else if ( class_exists( 'UCF_Events_Config' ) ) {
		$more_url = str_replace( 'feed.json', '', UCF_Events_Config::get_option_or_default( 'feed_url' ) );
	}

	// Remove empty values from $sc_attr, allowing shortcode defaults
	// to take effect when an attr isn't present
	$sc_attr     = array_filter( array(
		'feed_url' => $feed_url,
		'layout'   => $layout,
		'limit'    => $limit
	) );
	$sc_attr_str = ' title=""';

	foreach ( $sc_attr as $key => $val ) {
		$sc_attr_str .= ' ' . $key . '="' . $val . '"';
	}

	$content = do_shortcode( '[ucf-events' . $sc_attr_str . ']' );

	if ( $more_url && $more_text ) {
		$content .= '<div class="text-right"><a href="' . $more_url . '" target="_blank">' . $more_text . '</a></div>';
	}

	return today_display_sidebar_content( $title, $content );
}


/**
 * Displays a list of external stories (Resource Links)
 * suitable for inclusion within a sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Associative array of expected arguments + their values
 * @return string HTML markup for the external stories list
 */
function today_display_sidebar_external_stories( $args=array() ) {
	$title    = isset( $args['title'] ) ? $args['title'] : 'UCF in the News';
	$layout   = isset( $args['layout'] ) ? $args['layout'] : 'condensed';
	$limit    = isset( $args['limit'] ) ? $args['limit'] : 4;
	$more_url = isset( $args['more_url'] ) ? $args['more_url'] : today_get_external_stories_url();
	$content  = '';

	// Remove empty values from $sc_attr, allowing shortcode defaults
	// to take effect when an attr isn't present
	$sc_attr     = array_filter( array(
		'layout'      => $layout,
		'numberposts' => $limit,
		'post_type'   => 'ucf_resource_link',
		'tax_resource_link_types'        => 'external-story',
		'tax_resource_link_types__field' => 'slug'
	) );
	$sc_attr_str = '';

	foreach ( $sc_attr as $key => $val ) {
		$sc_attr_str .= ' ' . $key . '="' . $val . '"';
	}

	$content = do_shortcode( '[ucf-post-list' . $sc_attr_str . ']' );

	if ( $more_url ) {
		$content .= '<div class="text-right"><a href="' . $more_url . '" target="_blank">View All<span class="sr-only"> Stories about UCF</span></a></div>';
	}

	return today_display_sidebar_content( $title, $content );
}


/**
 * Displays a menu.  Suitable for inclusion within a sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Associative array of expected arguments + their values
 * @return string HTML markup for the menu
 */
function today_display_sidebar_menu( $args=array() ) {
	$title   = isset( $args['title'] ) ? $args['title'] : 'Resources';
	$menu    = isset( $args['menu'] ) ? $args['menu'] : 'Resources';
	$content = '';

	// If we don't have a menu to return, back out early:
	if ( ! $menu ) return;

	$content = wp_nav_menu( array(
		'menu'       => $menu,
		'menu_class' => 'list-unstyled',
		'echo'       => false
	) );

	return today_display_sidebar_content( $title, $content );
}


/**
 * Returns the sidebar markup for pages that utilize a customizable sidebar.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param integer $post_id WP Post ID
 * @return string HTML markup for the sidebar
 **/
function today_get_sidebar_markup( $post_id ) {
	$markup = '';

	if ( have_rows( 'sidebar_content', $post_id ) ) {
		while ( have_rows( 'sidebar_content', $post_id ) ) : the_row();
			switch ( get_row_layout() ) {
				case 'sidebar_events' :
					$feed_url  = get_sub_field( 'events_feed_url' );
					$layout    = get_sub_field( 'events_layout' ) ?: 'classic';
					$num_posts = get_sub_field( 'events_number_of_posts' ) ?: 4;
					$view_link = get_sub_field( 'events_view_all_link' );

					$markup .= today_display_sidebar_events( array(
						'feed_url' => $feed_url,
						'layout'   => $layout,
						'limit'    => $num_posts,
						'more_url' => $view_link
					) );
					break;
				case 'sidebar_in_the_news' :
					$layout    = get_sub_field( 'news_layout' ) ?: 'condensed';
					$num_posts = get_sub_field( 'news_number_of_posts' ) ?: 4;

					$markup .= today_display_sidebar_external_stories( array(
						'layout' => $layout,
						'limit'  => $num_posts
					) );
					break;
				case 'sidebar_resources_menu' :
					$menu = get_sub_field( 'resources_menu' ) ?: 'Resources';

					$markup .= today_display_sidebar_menu( array(
						'menu' => $menu
					) );
					break;
				case 'sidebar_spotlight' :
					if ( $spotlight = get_sub_field( 'spotlight_object' ) ) {
						$title = '';
						$content = do_shortcode( '[ucf-spotlight slug="' . $spotlight->post_name . '"]' );

						$markup .= today_display_sidebar_content( $title, $content );
					}
					break;
				case 'sidebar_custom_content' :
					if ( $custom_content = get_sub_field( 'custom_content' ) ) {
						$markup .= today_display_sidebar_content( null, $custom_content );
					}
					break;
				default :
					break;
			}
		endwhile;
	}

	return $markup;
}


/**
 * Adds the ACF Sidebar Fields field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_sidebar_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Enable Sidebar field
		$fields[] = array(
			'key'               => 'field_5c9cdf1d61885',
			'label'             => 'Enable Sidebar',
			'name'              => 'enable_sidebar',
			'type'              => 'true_false',
			'instructions'      => 'Enables a sidebar on this page with customizable drag-and-drop contents.',
			'wrapper'           => array(
				'width' => '30',
			),
			'default_value'     => 1,
			'ui'                => 1,
		);

		// Adds Sidebar Content fields
		$fields[] = array(
			'key'               => 'field_5c9cdf7861887',
			'label'             => 'Sidebar Content',
			'name'              => 'sidebar_content',
			'type'              => 'flexible_content',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5c9cdf1d61885',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'wrapper'           => array(
				'width' => '70',
				'class' => '',
				'id'    => '',
			),
			'layouts'           => array(
				'5c9ce0103b79e' => array(
					'key' => '5c9ce0103b79e',
					'name' => 'sidebar_events',
					'label' => 'Events',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_5c9e1777455c7',
							'label' => 'Feed URL',
							'name' => 'events_feed_url',
							'type' => 'url',
							'instructions' => 'UCF Events feed URL. Defaults to the "UCF Events JSON Feed URL" value in the UCF Events plugin.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
						),
						array(
							'key' => 'field_5c9e1793455c8',
							'label' => 'Layout',
							'name' => 'events_layout',
							'type' => 'text',
							'instructions' => 'Layout that will be used for displaying the events. Defaults to classic.',
							'wrapper' => array(
								'width' => '50',
							),
							'default_value' => 'classic',
						),
						array(
							'key' => 'field_5c9e17cb455c9',
							'label' => 'Number of Events',
							'name' => 'events_number_of_posts',
							'type' => 'number',
							'instructions' => 'The number of events to be displayed. Defaults to 4.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => 4,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'min' => 1,
							'max' => '',
							'step' => 1,
						),
						array(
							'key' => 'field_5c9e180b455ca',
							'label' => '\'View All\' Link',
							'name' => 'events_view_all_link',
							'type' => 'url',
							'instructions' => 'The URL assigned to the \'View All Events\' link. Defaults to a front-end view URL based on the "UCF Events JSON Feed URL" value in the UCF Events plugin.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
						),
					),
					'min' => '',
					'max' => '1',
				),
				'layout_5c9ce14d63fdc' => array(
					'key' => 'layout_5c9ce14d63fdc',
					'name' => 'sidebar_in_the_news',
					'label' => 'In The News',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_5c9e1884455cb',
							'label' => 'Layout',
							'name' => 'news_layout',
							'type' => 'text',
							'instructions' => 'Layout that will be used to display the news stories. Defaults to condensed.',
							'wrapper' => array(
								'width' => '50',
							),
							'default_value' => 'condensed',
						),
						array(
							'key' => 'field_5c9e1900455cc',
							'label' => 'Number of Posts',
							'name' => 'news_number_of_posts',
							'type' => 'number',
							'instructions' => 'The number of news stories to be displayed. Defaults to 4.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
							),
							'default_value' => 4,
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'min' => '',
							'max' => '',
							'step' => '',
						),
					),
					'min' => '',
					'max' => '1',
				),
				'layout_5c9ce13363fdb' => array(
					'key' => 'layout_5c9ce13363fdb',
					'name' => 'sidebar_resources_menu',
					'label' => 'Resources Menu',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_5c9e1b14455cf',
							'label' => 'Resources Menu',
							'name' => 'resources_menu',
							'type' => 'nav_menu',
							'instructions' => 'Select the menu to be displayed. Can be verified by checking the Appearance > Menus page. Defaults to \'Resources\'.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'save_format' => 'id',
							'container' => 'div',
							'allow_null' => 1,
						),
					),
					'min' => '',
					'max' => '1',
				),
				'layout_5c9ce1de68521' => array(
					'key' => 'layout_5c9ce1de68521',
					'name' => 'sidebar_spotlight',
					'label' => 'Spotlight',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_5c9ce23ed7f46',
							'label' => 'Select Spotlight',
							'name' => 'spotlight_object',
							'type' => 'post_object',
							'instructions' => 'Select a spotlight to display in the sidebar.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'post_type' => array(
								0 => 'ucf_spotlight',
							),
							'taxonomy' => array(
							),
							'allow_null' => 0,
							'multiple' => 0,
							'return_format' => 'object',
							'ui' => 1,
						),
					),
					'min' => '',
					'max' => '1',
				),
				'layout_5c9ce16b63fdd' => array(
					'key' => 'layout_5c9ce16b63fdd',
					'name' => 'sidebar_custom_content',
					'label' => 'Custom Content',
					'display' => 'block',
					'sub_fields' => array(
						array(
							'key' => 'field_5c9ce0516188e',
							'label' => 'Custom Content',
							'name' => 'custom_content',
							'type' => 'wysiwyg',
							'instructions' => 'Insert custom content to display in the sidebar.',
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

		// Defines Sidebar Fields field group
		$field_group = array(
			'key'                   => '',
			'title'                 => 'Sidebar Fields',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_sidebar_fields' );

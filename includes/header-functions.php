<?php
/**
 * Header Related Functions
 **/

/**
 * Modifies the h1 text for the given object.
 *
 * Adds "News" to the end of tag titles.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $title The object's determined title
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string The modified title
 */
function today_get_header_title_after( $title, $obj ) {
	if ( is_tag() ) {
		$title .= ' News';
	}

	return $title;
}

add_filter( 'ucfwp_get_header_title_after', 'today_get_header_title_after', 10, 2 );


/**
 * Modifies header markup for the given object.
 *
 * Returns an empty string on the homepage, which
 * prevents a <header> tag from being printed entirely.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $markup Determined header markup
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string Modified header markup
 */
function today_get_header_markup( $markup, $obj ) {
	if ( is_front_page() ) {
		$markup = '';
	}

	return $markup;
}

add_filter( 'ucfwp_get_header_markup', 'today_get_header_markup', 10, 2 );


/**
 * Modifies what header content type is returned for a given object.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $content_type The determined header content type
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string The determined header content type
 */
function today_get_header_content_type( $content_type, $obj ) {
	if ( $obj instanceof WP_Post ) {
		$post_type     = $obj->post_type;
		$post_template = get_page_template_slug( $obj->ID );

		if ( in_array( $post_type, array( 'post', 'ucf_statement' ) ) ) {
			$content_type = 'post';
		} elseif ( $post_type === 'page' ) {
			switch ( $post_template ) {
				case 'template-pegasus_home.php':
					$content_type = 'pegasus_home';
					break;
				default:
					break;
			}
		}
	}

	if ( is_archive() ) {
		$content_type = 'archive';
	}

	if ( is_category() ) {
		$content_type = 'category';
	}

	return $content_type;
}

add_filter( 'ucfwp_get_header_content_type', 'today_get_header_content_type', 11, 2 );


/**
 * Adds the ACF Page Header Fields field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_page_header_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Header Content field
		$fields[] = array(
			'key'               => 'field_590ca423f6654',
			'label'             => 'Header Content',
			'type'              => 'tab',
		);

		// Adds Type of Content field
		$fields[] = array(
			'key'               => 'field_59aed971c187c',
			'label'             => 'Header Content - Type of Content',
			'name'              => 'page_header_content_type',
			'type'              => 'radio',
			'instructions'      => 'Specify the type of content that should be displayed within the header.	Choose "Title and subtitle" to display a styled page title and optional subtitle, or choose "Custom content" to add any arbitrary content.	If "Custom content" is selected, a page title and subtitle are NOT included by default and should be added manually.',
			'required'          => 1,
			'choices'           => array(
				'title_subtitle' => 'Title and subtitle',
				'custom'         => 'Custom content',
			),
			'default_value'     => 'title_subtitle',
		);

		// Adds Header Title Text field
		$fields[] = array(
			'key'               => 'field_58fe096728bcc',
			'label'             => 'Header Title Text',
			'name'              => 'page_header_title',
			'type'              => 'text',
			'instructions'      => 'Overrides the page title.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_59aed971c187c',
						'operator' => '==',
						'value'    => 'title_subtitle',
					),
				),
			),
		);

		// Adds Header Subtitle Text field
		$fields[] = array(
			'key'               => 'field_58fe097f28bcd',
			'label'             => 'Header Subtitle Text',
			'name'              => 'page_header_subtitle',
			'type'              => 'text',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_59aed971c187c',
						'operator' => '==',
						'value'    => 'title_subtitle',
					),
				),
			),
		);

		// Adds Page h1 field
		$fields[] = array(
			'key'               => 'field_5a0e009ff592e',
			'label'             => 'Page h1',
			'name'              => 'page_header_h1',
			'type'              => 'radio',
			'instructions'      => 'Specify which part of the page title to use as the h1 for the page.	Styling of the title/subtitle will not be affected by this choice.',
			'required'          => 1,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_59aed971c187c',
						'operator' => '==',
						'value'    => 'title_subtitle',
					),
				),
			),
			'choices'           => array(
				'title'    => 'Title Text',
				'subtitle' => 'Subtitle Text',
			),
			'default_value'     => 'title',
		);

		// Adds Header Custom Contents field
		$fields[] = array(
			'key'               => 'field_59aed93dc187b',
			'label'             => 'Header Custom Contents',
			'name'              => 'page_header_content',
			'type'              => 'wysiwyg',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_59aed971c187c',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Adds Navigation tab
		$fields[] = array(
			'key'               => 'field_5a564fecfb51d',
			'label'             => 'Navigation',
			'type'              => 'tab',
		);

		// Adds Include Subnavigation field
		$fields[] = array(
			'key'               => 'field_5a56501afb51e',
			'label'             => 'Include Subnavigation',
			'name'              => 'page_header_include_subnav',
			'type'              => 'true_false',
			'instructions'      => 'Enable this setting to display an affixed subnavigation bar below the page header.	Requires the Automatic Sections Menu plugin to be activated, and for at least one section within the page\'s content to be configured to appear in the navbar.',
			'message'           => 'Include subnavigation',
		);

		// Defines Page Header Fields field group
		$field_group = array(
			'key'                   => 'group_58f7a73f5fecc',
			'title'                 => 'Page Header Fields',
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
						'operator' => '!=',
						'value'    => 'front_page',
					),
				),
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_page_header_fields' );

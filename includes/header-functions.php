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

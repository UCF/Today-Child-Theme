<?php
/**
 * Header Related Functions
 **/

/**
 * Modifies what header type is returned for a given object.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $header_type The determined header type
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string The determined header type
 */
function today_get_header_type( $header_type, $obj ) {
	if ( $obj instanceof WP_Post && $obj->post_type === 'post' ) {
		$header_type = 'post';
	}

	return $header_type;
}

add_filter( 'ucfwp_get_header_type', 'today_get_header_type', 11, 2 );


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
	if ( $obj instanceof WP_Post && $obj->post_type === 'post' ) {
		$content_type = 'post';
	}

	return $content_type;
}

add_filter( 'ucfwp_get_header_content_type', 'today_get_header_content_type', 11, 2 );
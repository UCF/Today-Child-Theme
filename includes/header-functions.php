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
		switch ( get_post_meta( $obj->ID, '_wp_page_template', true ) ) {
			case 'template-featured.php':
				$header_type = 'featured';
				break;
			case 'default':
			default:
				$header_type = 'post';
				break;
		}
	}

	return $header_type;
}

add_filter( 'ucfwp_get_header_type', 'today_get_header_type', 11, 2 );

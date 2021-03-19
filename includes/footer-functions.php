<?php
/**
 * Footer Related Functions
 **/

/**
 * Modifies what footer content type is returned for a given object.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $content_type The determined footer content type
 * @param mixed $obj A queried object (e.g. WP_Post, WP_Term), or null
 * @return string The determined footer content type
 */
function today_get_footer_type( $content_type, $obj ) {
	if ( $obj instanceof WP_Post ) {
		$post_type     = $obj->post_type;
		$post_template = get_page_template_slug( $obj->ID );

		if ( $post_type === 'page' && $post_template === 'template-pegasus_home.php' ) {
			$content_type = 'pegasus_home';
		}
	}

	return $content_type;
}

add_filter( 'ucfwp_get_footer_type', 'today_get_footer_type', 11, 2 );

<?php
/**
 * Header Related Functions
 **/

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

		if ( $post_type === 'post' ) {
			$content_type = 'post';
		} elseif ( $post_type === 'page' &&	$post_template === 'template-category.php' ) {
			$content_type = 'category';
		}
	}

	if ( is_archive() ) {
		$content_type = 'archive';
	}

	return $content_type;
}

add_filter( 'ucfwp_get_header_content_type', 'today_get_header_content_type', 11, 2 );

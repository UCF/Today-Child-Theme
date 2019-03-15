<?php
/**
 * General utilities unique to the Today Child Theme
 */

/**
 * Returns an attachment ID for the desired thumbnail
 * image of a given post.  Returns a fallback if no image
 * is available.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return mixed Attachment ID (int) or null on failure
 */
function today_get_thumbnail_id( $post ) {
	if ( ! $post instanceof WP_Post ) return null;

	$attachment_id = null;

	// Return the post's header image on posts
	if ( $post->post_type === 'post' ) {
		$attachment = get_field( 'post_header_image', $post );
		$attachment_id = isset( $attachment['id'] ) ? $attachment['id'] : null;
	}
	// Use standard thumbnails for everything else
	else {
		$attachment_id = get_post_thumbnail_id( $post );
	}

	// Get fallback if necessary
	if ( ! $attachment_id ) {
		// Use the UCF Post List Shortcode plugin's
		// fallback thumbnail, if one is available
		if ( method_exists( 'UCF_Post_List_Config', 'get_option_or_default' ) ) {
			$attachment_id = UCF_Post_List_Config::get_option_or_default( 'ucf_post_list_fallback_image' );
			$attachment_id = is_numeric( $attachment_id ) ? intval( $attachment_id ) : null;
		}
	}

	return $attachment_id;
}


/**
 * Returns a single, "primary" category assigned to the given post.
 * Supports Yoast SEO's primary term feature.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return mixed WP_Term object, or null on failure
 */
function today_get_primary_category( $post ) {
	if ( ! $post instanceof WP_Post ) return null;

	$primary = null;
	$cats    = wp_get_post_categories( $post->ID, array(
		'fields' => 'all'
	) );

	if ( is_wp_error( $cats ) || ! $cats ) return null;

	foreach ( $cats as $cat ) {
		if ( intval( get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true ) ) === intval( $cat->term_id ) ) {
			$primary = $cat;
			break;
		}
	}

	if ( ! $primary ) {
		$primary = $cats[0];
	}

	return $primary;
}


/**
 * Returns a single, "primary" tag assigned to the given post.
 * Supports the Primary Tag ACF field for posts.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return mixed WP_Term object, or null on failure
 */
function today_get_primary_tag( $post ) {
	if ( ! $post instanceof WP_Post ) return null;

	$primary = null;

	if ( $custom_primary = get_field( 'post_primary_tag', $post ) ) {
		$primary = $custom_primary;
	}

	if ( ! $primary ) {
		$tags = wp_get_post_tags( $post->ID, array(
			'fields' => 'all',
			'number' => 1
		) );
		if ( ! is_wp_error( $tags ) && isset( $tags[0] ) ) {
			$primary = $tags[0];
		}
	}

	return $primary;
}


/**
 * Adds a class to the body element for pages that
 * return disable_md_nav_toggle() as true.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param array $classes Array of class names
 * @return array $classes Array of class names
 **/
function today_navbar_body_class( $classes ) {
	if ( disable_md_nav_toggle() ) {
		$classes[] = 'disable-md-navbar-toggle';
    }

    return $classes;
}

add_filter( 'body_class','today_navbar_body_class' );

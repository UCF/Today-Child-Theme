<?php
/**
 * Functions related to the UCF Resource Search Plugin
 * (used for External Stories in this site)
 */

/**
 * Returns a source for the given Resource Link post.
 * TODO how are we storing 'source's?
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object for the Resource Link
 * @return string The source value for the Resource Link
 */
function today_get_resource_link_source( $post ) {
	return '';
}


/**
 * Modifies the permalink for Resource Links to always
 * return the "Website URL" value.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $post_link The post's permalink.
 * @param object $post WP_Post object.
 * @param bool $leavename Whether to keep the post name.
 * @param bool $sample Is it a sample permalink.
 * @return string The permalink value for the Resource Link
 */
function today_get_resource_link_permalink( $post_link, $post, $leavename, $sample ) {
	if ( $post->post_type === 'ucf_resource_link' ) {
		$external_url = get_post_meta( $post->ID, 'ucf_resource_link_url', true );
		if ( $external_url ) {
			$post_link = $external_url;
		}
	}

	return $post_link;
}

add_filter( 'post_type_link', 'today_get_resource_link_permalink', 10, 4 );


/**
 * Utility function that determines if a Resource Link
 * has a valid permalink (links to an external document).
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object for the Resource Link
 * @return boolean Whether or not the Resource Link has a valid permalink
 */
function today_resource_link_permalink_is_valid( $post ) {
	return get_permalink( $post ) === get_post_meta( $post->ID, 'ucf_resource_link_url', true );
}


/**
 * Adds a redirect for any Resource Link that doesn't have
 * a Website URL to go back to the site homepage.
 */
function today_resource_link_redirect() {
	global $wp_query, $post;

	if (
		$post
		&& $post->post_type === 'ucf_resource_link'
		&& ! today_resource_link_permalink_is_valid( $post )
	) {
		wp_redirect( home_url() );
		exit();
	}
}

add_filter( 'template_redirect', 'today_resource_link_redirect' );

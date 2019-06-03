<?php
/**
 * Opinionated overrides and functions related to post excerpts.
 */

/**
 * Returns the post excerpt. Handles necessary postdata setup.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post A WP_Post object
 * @param int $length Specify a custom length for the excerpt.
 * @return string Sanitized post excerpt
 */
function today_get_excerpt( $post, $length=TODAY_DEFAULT_EXCERPT_LENGTH ) {
	if ( ! ( $post instanceof WP_Post ) ) return '';

	$excerpt = '';

	if ( $deck = get_field( 'post_header_deck', $post ) ) {
		$excerpt = wp_strip_all_tags( $deck );
	}
	else if ( $resource_link_desc = get_field( 'ucf_resource_link_description', $post ) ) {
		$excerpt = wp_strip_all_tags( $resource_link_desc );
	}
	else {
		$excerpt = ucfwp_get_excerpt( $post, $length );
	}

	return $excerpt;
}


/**
 * Shortens the length of excerpts to 30 words.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_default_excerpt_length( $length ) {
	return TODAY_DEFAULT_EXCERPT_LENGTH;
}

add_filter( 'excerpt_length', 'today_default_excerpt_length', 98 );

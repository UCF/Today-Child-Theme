<?php
/**
 * Opinionated overrides and functions related to post excerpts.
 */

/**
 * Overrides the returned value for post excerpts retrieved using
 * `get_the_excerpt()`.
 *
 * @since 1.0.7
 * @author Jo Dickson
 * @param string $excerpt The post excerpt
 * @param object $post WP_Post object
 * @return string Modified excerpt
 */
function today_get_the_excerpt( $excerpt, $post ) {
	if ( $deck = get_field( 'post_header_deck', $post ) ) {
		$excerpt = $deck;
	}
	else if ( $resource_link_desc = get_field( 'ucf_resource_link_description', $post ) ) {
		$excerpt = $resource_link_desc;
	}

	return $excerpt;
}

add_filter( 'get_the_excerpt', 'today_get_the_excerpt', 10, 2 );


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

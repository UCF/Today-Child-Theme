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
	$custom_filter = function( $l ) use ( $length ) {
		return $length;
	};

	setup_postdata( $post );

	add_filter( 'excerpt_length', $custom_filter, 99 );
	$excerpt = wp_strip_all_tags( get_the_excerpt( $post ) );
	remove_filter( 'excerpt_length', $custom_filter, 99 );

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


/**
 * Modifies the string printed at the end of excerpts.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_excerpt_more( $more ) {
	return '&hellip;';
}

add_filter( 'excerpt_more', 'today_excerpt_more' );

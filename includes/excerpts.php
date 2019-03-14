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
 * @return string Sanitized post excerpt
 */
function today_get_excerpt( $post ) {
	if ( ! ( $post instanceof WP_Post ) ) return '';

	setup_postdata( $post );
	return wp_strip_all_tags( get_the_excerpt( $post ) );
}


/**
 * Shortens the length of excerpts to 30 words.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_excerpt_length( $length ) {
	return 30;
}

add_filter( 'excerpt_length', 'today_excerpt_length', 999 );


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

<?php
/**
 * Handle all theme configuration here
 **/

define( 'TODAY_THEME_URL', get_stylesheet_directory_uri() );
define( 'TODAY_THEME_STATIC_URL', TODAY_THEME_URL . '/static' );
define( 'TODAY_THEME_CSS_URL', TODAY_THEME_STATIC_URL . '/css' );
define( 'TODAY_THEME_JS_URL', TODAY_THEME_STATIC_URL . '/js' );
define( 'TODAY_THEME_IMG_URL', TODAY_THEME_STATIC_URL . '/img' );
define( 'TODAY_THEME_CUSTOMIZER_PREFIX', 'today_' );


/**
 * Removes the UCF WP Theme's disabling of comments, trackbacks, and pingbacks.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_reenable_comments() {
    remove_action( 'init', 'ucfwp_kill_comments' );
}

add_action( 'after_setup_theme', 'today_reenable_comments' );


/**
 * Kill trackbacks and pingbacks only.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_kill_link_notifications() {
	// Remove the X-Pingback HTTP header, if present.
	add_filter( 'wp_headers', function( $headers ) {
		if ( isset( $headers['X-Pingback'] ) ) {
			unset( $headers['X-Pingback'] );
		}
		return $headers;
	} );

	// Remove native post type support for trackbacks on all
	// public-facing post types.
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	foreach ( $post_types as $pt ) {
		if ( post_type_supports( $pt, 'trackbacks' ) ) {
			remove_post_type_support( $pt, 'trackbacks' );
		}
	}

	// Disable pingbacks on new posts (these are the primary
	// default discussion settings under Settings > Discussion)
	add_filter( 'option_default_pingback_flag', '__return_zero' );
	add_filter( 'option_default_ping_status', '__return_zero' );

	// Close ability to add new pingbacks on existing posts.
	add_filter( 'pings_open', '__return_false' );
}

add_action( 'init', 'today_kill_link_notifications' );


/**
 * Kill comments on attachments.
 */
function today_kill_attachment_comments() {
	// Remove post type support.
	if ( post_type_supports( 'attachment', 'comments' ) ) {
		remove_post_type_support( 'attachment', 'comments' );
	}

	// Make sure comments_open() always returns false for attachments,
	// new or existing.
	add_filter( 'comments_open', function( $open, $post_id ) {
		$post = get_post( $post_id );
		if ( $post && $post->post_type === 'attachment' ) {
			return false;
		}
		return $open;
	}, 10, 2 );
}

add_action( 'init', 'today_kill_attachment_comments' );

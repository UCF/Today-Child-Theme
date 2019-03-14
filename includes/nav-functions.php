<?php
/**
 * Overrides the UCF WordPress Theme function for returning a site nav
 * type to always return the site-specific nav (the main site nav should
 * never be used in this theme.)
 *
 * @author Jo Dickson
 * @since 1.0.0
 * @return string The nav type name
 */
function today_get_nav_type() {
	return '';
}

add_filter( 'ucfwp_get_nav_type', 'today_get_nav_type' );


/**
 * Displays the primary site navigation for UCF Today.
 *
 * NOTE: This function intentionally echoes its output, rather than
 * returning a string, because we register this function as an action on the
 * `after_body_open` hook.
 *
 * @author Jo Dickson
 * @since 1.0.0
 * @return void
 **/
function today_nav_markup() {
	echo ucfwp_get_nav_markup( false );
}

add_action( 'after_body_open', 'today_nav_markup', 10, 0 );


/**
 * Determine whether the site's expandable nav toggle should be disabled
 * at the -md breakpoint (and force the site's primary navigation to be
 * visible) depending on the current view.
 *
 * Adapted from Today-Bootstrap
 *
 * @since 1.0.0
 * @return bool
 */
function disable_md_nav_toggle() {
	return is_home() || is_front_page() || is_category() || is_tag() || is_page();
}

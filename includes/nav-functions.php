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

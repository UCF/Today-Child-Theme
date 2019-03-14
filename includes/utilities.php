<?php
/**
 * General utilities
 **/

/**
 * Adds a class to the body element to pages that
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

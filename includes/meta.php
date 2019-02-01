<?php
/**
 * Includes functions that handle registration/enqueuing of meta tags, styles,
 * and scripts in the document head and footer.
 **/

/**
 * Enqueue front-end css and js.
 *
 * @since 1.0.0
 * @author Jo Dickson
 **/
function today_enqueue_frontend_assets() {
	$theme = wp_get_theme();
	$theme_version = $theme->get( 'Version' );

	wp_enqueue_style( 'style-child', TODAY_THEME_CSS_URL . '/style.min.css', array( 'style' ), $theme_version );
}

add_action( 'wp_enqueue_scripts', 'today_enqueue_frontend_assets', 11 );

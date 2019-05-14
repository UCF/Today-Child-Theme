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

	wp_enqueue_script( 'script-child', TODAY_THEME_JS_URL . '/script.min.js', array( 'jquery', 'script' ), $theme_version, true );
}

add_action( 'wp_enqueue_scripts', 'today_enqueue_frontend_assets', 11 );


/**
 * Enqueue admin styles and scripts
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_enqueue_admin_assets() {
	// get_current_screen() returns null on this hook,
	// so sniff the request URI instead when is_admin() is true
	if ( is_admin() ) {

		// Enqueue assets on New Post screen
		if ( stristr( $_SERVER['REQUEST_URI'], 'post-new.php' ) !== false ) {
			// Enqueue global editor styles
			add_editor_style( TODAY_THEME_CSS_URL . '/editor.min.css' );

			// Enqueue post-specific editor styles
			if ( ! isset( $_GET['post_type'] ) ) {
				add_editor_style( TODAY_THEME_CSS_URL . '/editor-post.min.css' );
			}
		}
		// Enqueue assets on Edit Post screen
		else if ( stristr( $_SERVER['REQUEST_URI'], 'post.php' ) !== false ) {
			// Enqueue global editor styles
			add_editor_style( TODAY_THEME_CSS_URL . '/editor.min.css' );

			// Enqueue post-specific editor styles
			global $post;
			if ( is_object( $post ) && get_post_type( $post->ID ) === 'post' ) {
				add_editor_style( TODAY_THEME_CSS_URL . '/editor-post.min.css' );
			}
		}

	}
}

add_action( 'init', 'today_enqueue_admin_assets', 99 ); // Enqueue late to ensure styles are enqueued after Athena SC Plugin's styles
add_action( 'pre_get_posts', 'today_enqueue_admin_assets' ); // Also register on this hook for Edit Post view, so that $post is defined at the correct time

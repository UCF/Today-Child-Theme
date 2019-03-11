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


// TODO update 'large' thumbnail dimensions to 1140px wide


/**
 * Defines sections used in the WordPress Customizer.
 *
 * @author Jo Dickson
 * @since 1.0.0
 */
function today_define_customizer_sections( $wp_customize ) {
	// Remove Navigation Settings section from UCF WP Theme since we don't
	// utilize the fallback Main Site navigation in this theme
	if ( defined( 'UCFWP_THEME_CUSTOMIZER_PREFIX' ) ) {
		$wp_customize->remove_section( UCFWP_THEME_CUSTOMIZER_PREFIX . 'nav_settings' );
	}
}

add_action( 'customize_register', 'today_define_customizer_sections', 11 );


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
 *
 * @since 1.0.0
 * @author Jo Dickson
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


/**
 * Disable the UCF WP Theme's template redirect overrides so that we can
 * define our own in this theme.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_reenable_templates() {
    remove_action( 'template_redirect', 'ucfwp_kill_unused_templates' );
}
add_action( 'after_setup_theme', 'today_reenable_templates' );


/**
 * Kill unused templates in this theme.  Redirect to the homepage if
 * an unused template is requested.
 *
 * Partially ported over from Today-Bootstrap
 *
 * @since 1.0.0
 * @author Jo Dickson
 **/
function today_kill_unused_templates() {
	global $wp_query, $post;

	// NOTE: we only disable day-specific date archives (is_day()).
	// Month + year archives are still enabled.
	if ( is_author() || is_attachment() || is_day() || is_search() || is_comment_feed() ) {
		wp_redirect( home_url() );
		exit();
	}

	// Disable author, attachment, and day-specific feeds.
	if ( is_feed() ) {
		$author     = get_query_var( 'author_name' );
		$attachment = get_query_var( 'attachment' );
		$attachment = ( empty( $attachment ) ) ? get_query_var( 'attachment_id' ) : $attachment;
		$day        = get_query_var( 'day' );

		if ( ! empty( $author ) || ! empty( $attachment ) || ! empty( $day ) ) {
			wp_redirect( home_url() );
			$wp_query->is_feed = false;
			exit();
		}
	}
}

add_action( 'template_redirect', 'today_kill_unused_templates' );


/**
 * Prevent Wordpress from trying to redirect to a "loose match" post when
 * an invalid URL is requested. WordPress will redirect to 404.php instead.
 *
 * Implemented to prevent some print views from redirecting to random
 * attachments.
 *
 * See http://wordpress.stackexchange.com/questions/3326/301-redirect-instead-of-404-when-url-is-a-prefix-of-a-post-or-page-name
 *
 * Ported from Today-Bootstrap
 *
 * @since 1.0.0
 * @author Jo Dickson
 **/
function today_no_redirect_on_404( $redirect_url ) {
    if ( is_404() ) {
        return false;
    }
    return $redirect_url;
}

add_filter( 'redirect_canonical', 'today_no_redirect_on_404' );


/**
 * Returns legacy rewrite rules for the transition from
 * pre-Today theme to Today theme (https://github.com/UCF/Today)
 *
 * Retained for backward compatibility
 *
 * Ported from Today-Bootstrap
 *
 * @since 1.0.0
 * @author Chris Conover
 * @return array
 **/
function today_get_legacy_rewrite_rules() {
	global $wp_rewrite;

	$cats = array(
		'music'                           => 'music',
		'theatre'                         => 'theatre',
		'visual-arts'                     => 'visual-arts',
		'arts-humanities'                 => 'arts-humanities',
		'education'                       => 'education',
		'engineering-computer-science'    => 'engineering-computer-science',
		'graduate-studies'                => 'graduate-studies',
		'health-public-affairs'           => 'health-public-affairs',
		'honors'                          => 'honors',
		'hospitality-managment'           => 'hospitality-management',
		'medicine-colleges'               => 'medicine-colleges',
		'nursing-colleges'                => 'ucf-college-of-nursing',
		'optics-photonics'                => 'optics-photonics',
		'sciences'                        => 'sciences',
		'main-site-stories'               => 'main-site-stories',
		'on-campus'                       => 'on-campus',
		'events'                          => 'events',
		'research'                        => 'research'
	);

	$custom = array();

	foreach ( $cats as $before => $after ) {
		// Rewrite category pages
		$custom['section/(?:[^/]+/)?' . $before.'/?$'] = 'index.php?tag=' . $after;
		$custom['category/(?:[^/]+/)?' . $before.'/?$'] = 'index.php?tag=' . $after;

		// Rewrite feed pages
		$custom['section/(?:[^/]+/)?' . $before.'/feed/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=' . $after . '&feed=$matches[1]';
		$custom['section/(?:[^/]+/)?' . $before.'/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=' . $after . '&feed=$matches[1]';
		$custom['category/(?:[^/]+/)?' . $before.'/feed/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=' . $after . '&feed=$matches[1]';
		$custom['category/(?:[^/]+/)?' . $before.'/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=' . $after . '&feed=$matches[1]';
	}

	// Rewrite old category and tag pages
	$custom['category/(?:[^/]+/)?(.+?)/feed/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
	$custom['category/(?:[^/]+/)?(.+?)/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
	$custom['category/(?:[^/]+/)?(.+?)/?$'] = 'index.php?category_name=$matches[1]';

	$custom['section/(?:[^/]+/)?(.+?)/feed/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
	$custom['section/(?:[^/]+/)?(.+?)/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
	$custom['section/(?:[^/]+/)?(.+?)/?$'] = 'index.php?category_name=$matches[1]';

	$custom['tag/(?:[^/]+/)?(.+?)/feed/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=$matches[1]&feed=$matches[2]';
	$custom['tag/(?:[^/]+/)?(.+?)/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=$matches[1]&feed=$matches[2]';
	$custom['tag/(?:[^/]+/)?(.+?)/?$'] = 'index.php?tag=$matches[1]';

	$custom['topic/(?:[^/]+/)?(.+?)/feed/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=$matches[1]&feed=$matches[2]';
	$custom['topic/(?:[^/]+/)?(.+?)/(feed|rdf|rss|rss2|atom|json)/?$'] = 'index.php?tag=$matches[1]&feed=$matches[2]';
	$custom['topic/(?:[^/]+/)?(.+?)/?$'] = 'index.php?tag=$matches[1]';

	return $custom;
}


/**
 * Applies all custom rewrite rules for this theme.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_rewrite_rules( $rules ) {
	$legacy = today_get_legacy_rewrite_rules();
	return $legacy + $rules;
}

add_filter( 'rewrite_rules_array', 'today_rewrite_rules' );


/**
 * Removed undesired templates for the generic 'post' post type
 * inherited from the UCF WordPress Theme.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $post_templates Array of templates. Keys are filenames, values are translated names.
 * @param object $theme_obj WP_Theme object
 * @param mixed $post The post being edited, provided for context, or null.
 * @param string $post_type Post type to get the templates for.
 * @return array Modified array of available templates
 */
function today_available_ucfwp_post_templates( $post_templates, $theme_obj, $post, $post_type ) {
	$unused = array(
		'template-fullscreen.php',
		'template-narrow.php'
	);

	foreach ( $unused as $template_name ) {
		if ( isset( $post_templates[$template_name] ) ) {
			unset( $post_templates[$template_name] );
		}
	}

	return $post_templates;
}

add_filter( 'theme_post_templates', 'today_available_ucfwp_post_templates', 10, 4 );

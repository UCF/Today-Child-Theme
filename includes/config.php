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
define( 'TODAY_DEFAULT_EXCERPT_LENGTH', 30 );
define( 'TODAY_SHORT_EXCERPT_LENGTH', 25 );


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
 * Defines custom settings and controls used in the WordPress Customizer.
 *
 * @author Cadie Brown
 * @since 1.0.0
 */
function today_define_customizer_fields( $wp_customize ) {
	// Site Subtitle
	$wp_customize->add_setting(
		'site_subtitle'
	);

	$wp_customize->add_control(
		'site_subtitle',
		array(
			'type'        => 'text',
			'label'       => 'Site Subtitle',
			'description' => 'Descriptive text to display next to the UCF Today logo in the site header.',
			'section'     => 'title_tagline'
		)
	);
}

add_action( 'customize_register', 'today_define_customizer_fields' );


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

	// NOTE: we only disable day-specific date and year archives
	// (is_day(), is_year()).
	// Month archives are still enabled.
	if ( is_author() || is_attachment() || is_day() || is_year() || is_search() || is_comment_feed() ) {
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


/**
 * Modifies the dimensions for WordPress's default image sizes.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
add_filter( 'pre_option_large_size_w', function( $value ) {
	return 1200;
} );

add_filter( 'pre_option_large_size_h', function( $value ) {
	return 800;
} );

add_filter( 'pre_option_medium_large_size_h', function( $value ) {
	return 512;
} );


/**
 * Hides the featured image metabox for standard posts in the WordPress admin.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_remove_post_thumbnail_box() {
    remove_meta_box( 'postimagediv', 'post', 'side' );
}

add_action( 'do_meta_boxes', 'today_remove_post_thumbnail_box' );


/**
 * Adds a custom ACF WYSIWYG toolbar called 'Inline Text' that only includes
 * simple inline text formatting tools and link insertion/deletion.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $toolbars Array of toolbar information from ACF
 * @return array
 */
function today_acf_inline_text_toolbar( $toolbars ) {
	$toolbars['Inline Text'] = array();
	$toolbars['Inline Text'][1] = array( 'bold', 'italic', 'link', 'unlink', 'undo', 'redo' );

	return $toolbars;
}

add_filter( 'acf/fields/wysiwyg/toolbars', 'today_acf_inline_text_toolbar' );

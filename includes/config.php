<?php
/**
 * Handle all theme configuration here
 **/

define( 'TODAY_THEME_URL', get_stylesheet_directory_uri() );
define( 'TODAY_THEME_STATIC_URL', TODAY_THEME_URL . '/static' );
define( 'TODAY_THEME_CSS_URL', TODAY_THEME_STATIC_URL . '/css' );
define( 'TODAY_THEME_JS_URL', TODAY_THEME_STATIC_URL . '/js' );
define( 'TODAY_THEME_IMG_URL', TODAY_THEME_STATIC_URL . '/img' );
define( 'TODAY_DEFAULT_EXCERPT_LENGTH', 60 );
define( 'TODAY_SHORT_EXCERPT_LENGTH', 25 );
define( 'TODAY_THEME_CUSTOMIZER_PREFIX', 'today_' );
define( 'TODAY_THEME_CUSTOMIZER_DEFAULTS', serialize( array(
	'pegasus_subtitle'     => 'The magazine of <br class="hidden-lg-up">the University of Central Florida',
	'pegasus_archives_url' => 'https://www.ucf.edu/pegasus/archives/',
	'pegasus_about_url'    => 'https://www.ucf.edu/pegasus/about/'
) ) );


/**
 * Initialization functions to be fired early when WordPress loads the theme.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_init() {
	// Remove page header image sizes, since the UCF WP Theme's
	// media header logic isn't utilized in this theme.
	remove_image_size( 'header-img' );
	remove_image_size( 'header-img-sm' );
	remove_image_size( 'header-img-md' );
	remove_image_size( 'header-img-lg' );
	remove_image_size( 'header-img-xl' );
	remove_image_size( 'bg-img' );
	remove_image_size( 'bg-img-sm' );
	remove_image_size( 'bg-img-md' );
	remove_image_size( 'bg-img-lg' );
	remove_image_size( 'bg-img-xl' );

	// Register sidebars for Pegasus homepage footer
	register_sidebar( array(
		'name'          => __( 'Pegasus Homepage Footer - Column 1' ),
		'id'            => 'pegasus_home-footer-col-1',
		'description'   => 'First column in the Pegasus homepage footer.',
		'before_widget' => '<div id="%1$s" class="widget mb-5 %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="h6 heading-underline letter-spacing-3">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Pegasus Homepage Footer - Column 2' ),
		'id'            => 'pegasus_home-footer-col-2',
		'description'   => 'Second column in the Pegasus homepage footer.',
		'before_widget' => '<div id="%1$s" class="widget mb-5 %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="h6 heading-underline letter-spacing-3">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Pegasus Homepage Footer - Column 3' ),
		'id'            => 'pegasus_home-footer-col-3',
		'description'   => 'Third column in the Pegasus homepage footer.',
		'before_widget' => '<div id="%1$s" class="widget mb-5 %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="h6 heading-underline letter-spacing-3">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Pegasus Homepage Footer - Column 4' ),
		'id'            => 'pegasus_home-footer-col-4',
		'description'   => 'Last column in the Pegasus homepage footer.',
		'before_widget' => '<div id="%1$s" class="widget mb-5 %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="h6 heading-underline letter-spacing-3">',
		'after_title'   => '</h2>',
	) );
}

add_action( 'after_setup_theme', 'today_init', 11 );


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

	$wp_customize->add_section(
		TODAY_THEME_CUSTOMIZER_PREFIX . 'pegasus',
		array(
			'title' => 'Pegasus Magazine'
		)
	);
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

	// Pegasus Magazine
	$wp_customize->add_setting(
		'pegasus_subtitle',
		array(
			'default' => today_get_theme_mod_default( 'pegasus_subtitle' )
		)
	);

	$wp_customize->add_control(
		'pegasus_subtitle',
		array(
			'type'        => 'text',
			'label'       => 'Magazine Subtitle',
			'description' => 'Short descriptive text that describes the magazine. Displayed on the Pegasus homepage underneath the logo in the header.',
			'section'     => TODAY_THEME_CUSTOMIZER_PREFIX . 'pegasus'
		)
	);

	$wp_customize->add_setting(
		'pegasus_archives_url',
		array(
			'default' => today_get_theme_mod_default( 'pegasus_archives_url' )
		)
	);

	$wp_customize->add_control(
		'pegasus_archives_url',
		array(
			'type'        => 'url',
			'label'       => 'Archives URL',
			'description' => 'Full URL to the Pegasus story archives.',
			'section'     => TODAY_THEME_CUSTOMIZER_PREFIX . 'pegasus'
		)
	);

	$wp_customize->add_setting(
		'pegasus_about_url',
		array(
			'default' => today_get_theme_mod_default( 'pegasus_about_url' )
		)
	);

	$wp_customize->add_control(
		'pegasus_about_url',
		array(
			'type'        => 'url',
			'label'       => 'About Page URL',
			'description' => 'Full URL to the About page for Pegasus.',
			'section'     => TODAY_THEME_CUSTOMIZER_PREFIX . 'pegasus'
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
 * Remove old blogroll Links admin menu item.
 *
 * @since 1.0.0
 * @author Jo Dickson
 **/
function today_kill_blogroll_links() {
	remove_menu_page( 'link-manager.php' );
}

add_action( 'admin_menu', 'today_kill_blogroll_links' );


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


/**
 * Moves the page WYSIWYG editor to a placeholder field within the
 * Homepage Fields group.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_acf_reposition_wysiwyg() {
?>
<script type="text/javascript">
	(function($) {
		$(document).ready(function(){
			// field_5cac9ecc97b7c = "Custom Page Content" Message field (placeholder) on Homepage Fields group
			$('.acf-field-5cac9ecc97b7c .acf-input')
				.append( $('#postdivrich') );
		});
	})(jQuery);
</script>
<style type="text/css">
	.acf-field #wp-content-editor-tools {
		background: transparent;
		padding-top: 0;
	}
</style>
<?php
}

add_action( 'acf/input/admin_head', 'today_acf_reposition_wysiwyg' );


/**
 * Sets a post's original publish date meta value when the post is published.
 *
 * @since 1.0.0
 * @author Jim Barnes
 */
function today_post_insert_override( $post_id, $post, $update ) {
	if ( $post->post_type !== 'post'
		|| wp_is_post_revision( $post_id )
		|| $post->post_status !== 'publish' ) {
		return;
	}

	// Get post meta
	$publish_date = get_post_meta( $post_id, 'post_header_publish_date', true );

	if ( ! $publish_date ) {
		update_post_meta( $post_id, 'post_header_publish_date', current_time( 'Y-m-d', false ) );
	}
}

add_action( 'wp_insert_post', 'today_post_insert_override', 10, 3 );


/**
 * Sets a Resource Link's default Resource Link Type
 * when the post is published, if a unique Resource Link Type
 * wasn't provided.
 *
 * @since 1.0.2
 * @author Jo Dickson
 */
function today_default_resource_link_type( $post_id, $post, $update ) {
	if ( $post->post_type !== 'ucf_resource_link' ) {
		return;
	}

	$terms = wp_get_post_terms( $post_id, 'resource_link_types' );
	if ( empty( $terms ) ) {
		wp_set_object_terms( $post_id, 'external-story', 'resource_link_types' );
	}
}

add_action( 'wp_insert_post', 'today_default_resource_link_type', 10, 3 );

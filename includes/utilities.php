<?php
/**
 * General utilities unique to the Today Child Theme
 */

/**
 * Returns an attachment ID for the desired thumbnail
 * image of a given post.  Optionally returns a fallback
 * if no image is available.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param mixed $post WP_Post object or post ID
 * @param bool $use_fallback Whether or not a fallback image should be returned if a thumbnail isn't set
 * @return mixed Attachment ID (int) or null on failure
 */
function today_get_thumbnail_id( $post, $use_fallback=true ) {
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}
	if ( ! $post instanceof WP_Post ) return null;

	$attachment_id = null;

	// Return the post's header image on posts
	if ( $post->post_type === 'post' && get_field( 'header_media_type', $post ) === 'image' ) {
		$attachment    = get_field( 'post_header_image', $post );
		$attachment_id = isset( $attachment['id'] ) ? $attachment['id'] : null;
	}
	// Use standard thumbnails for everything else
	else {
		$attachment_id = get_post_thumbnail_id( $post );
	}

	// Get fallback if necessary
	if ( ! $attachment_id && $use_fallback ) {
		// Use the UCF Post List Shortcode plugin's
		// fallback thumbnail, if one is available
		if ( method_exists( 'UCF_Post_List_Config', 'get_option_or_default' ) ) {
			$attachment_id = UCF_Post_List_Config::get_option_or_default( 'ucf_post_list_fallback_image' );
			$attachment_id = is_numeric( $attachment_id ) ? intval( $attachment_id ) : null;
		}
	}

	return $attachment_id;
}


/**
 * Returns a URL for the desired thumbnail + size
 * of a given post.  Optionally returns a fallback
 * if no image is available.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param mixed $post WP_Post object or post ID
 * @param string $thumbnail_size Image size to retrieve
 * @param bool $use_fallback Whether or not a fallback image should be returned if a thumbnail isn't set
 * @return mixed Attachment ID (int) or null on failure
 */
function today_get_thumbnail_url( $post, $thumbnail_size='medium', $use_fallback=true ) {
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}
	if ( ! $post instanceof WP_Post ) return null;

	$thumbnail_url     = '';
	$header_media_type = get_field( 'header_media_type', $post );

	switch ( $header_media_type ) {
		case 'video':
			// Get video url (prevent ACF oEmbed processing)
			$video_url           = get_field( 'post_header_video_url', $post, false );
			$video_thumbnail_w   = intval( get_option( "{$thumbnail_size}_size_w" ) );
			$video_thumbnail_h   = intval( get_option( "{$thumbnail_size}_size_h" ) );
			$video_thumbnail_url = today_get_oembed_thumbnail( $video_url, $video_thumbnail_w, $video_thumbnail_h );

			if ( $video_thumbnail_url ) {
				$thumbnail_url = $video_thumbnail_url;
			}
			break;
		case 'image':
			$thumbnail_id = today_get_thumbnail_id( $post, $use_fallback );

			if ( $thumbnail_id ) {
				$thumbnail_url = ucfwp_get_attachment_src_by_size( $thumbnail_id, $thumbnail_size );
			}
			break;
		default:
			break;
	}

	// Use a fallback if the user requested a thumbnail, but
	// one isn't available for the post
	if ( ! $thumbnail_url && $use_fallback ) {
		$thumbnail_url = TODAY_THEME_IMG_URL . '/default-thumb.jpg';
	}

	return $thumbnail_url;
}


/**
 * Returns the 'poster' or thumbnail image for a given embed
 * URL, such as a YouTube or Vimeo URL, if available.
 *
 * Thumbnail values are stored as transients based on
 * the given embed URL.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $embed_url URL of the video/embed
 * @param int $max_width Maximum width desired for the returned thumbnail
 * @param int $max_height Maximum height desired for the returned thumbnail
 * @return mixed URL of the thumbnail (string), or null
 */
function today_get_oembed_thumbnail( $embed_url, $max_width=null, $max_height=null ) {
	if ( ! $embed_url ) return null;

	$retval        = null;
	$max_width     = ( $max_width && is_numeric( $max_width ) ) ? intval( $max_width ) : 0;
	$max_height    = ( $max_height && is_numeric( $max_height ) ) ? intval( $max_height ) : 0;
	$transient_key = 'today_oembed_thumb_' . md5( $embed_url . $max_width . $max_height );
	$transient     = get_transient( $transient_key );

	if ( $transient !== false ) {
		$retval = $transient;
	}
	else {
		$oembed          = new WP_oEmbed();
		$oembed_data     = null;
		$oembed_provider = $oembed->get_provider( $embed_url );
		$oembed_thumb    = null;
		$oembed_args     = array();

		// Not all oembed providers will support this, but try
		// to define a desired set of thumbnail dimensions:
		if ( $max_width > 0 ) {
			$oembed_args['width'] = $max_width;
		}
		if ( $max_height > 0 ) {
			$oembed_args['height'] = $max_height;
		}

		if ( $oembed_provider ) {
			$oembed_data = $oembed->fetch( $oembed_provider, $embed_url, $oembed_args );
		}

		// `thumbnail_url` is an optional property per the oembed spec,
		// so make sure it's set before attempting to access it
		if ( $oembed_data && property_exists( $oembed_data, 'thumbnail_url' ) ) {
			$oembed_thumb = $oembed_data->thumbnail_url;
		}

		$oembed_thumb = ( $oembed_thumb ) ?: null;

		// Store returned data as a transient for future use.
		// Result is cached for 24 hours.
		set_transient( $transient_key, $oembed_thumb, DAY_IN_SECONDS );

		$retval = $oembed_thumb;
	}

	return $retval;
}


/**
 * Returns a single, "primary" category assigned to the given post.
 * Supports Yoast SEO's primary term feature.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return mixed WP_Term object, or null on failure
 */
function today_get_primary_category( $post ) {
	if ( ! $post instanceof WP_Post ) return null;

	$primary = null;
	$cats    = wp_get_post_categories( $post->ID, array(
		'fields' => 'all'
	) );

	if ( is_wp_error( $cats ) || ! $cats ) return null;

	foreach ( $cats as $cat ) {
		if ( intval( get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true ) ) === intval( $cat->term_id ) ) {
			$primary = $cat;
			break;
		}
	}

	if ( ! $primary ) {
		$primary = $cats[0];
	}

	return $primary;
}


/**
 * Returns a single, "primary" tag assigned to the given post.
 * Supports the Primary Tag ACF field for posts.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return mixed WP_Term object, or null on failure
 */
function today_get_primary_tag( $post ) {
	if ( ! $post instanceof WP_Post ) return null;

	$primary = null;

	if ( $custom_primary = get_field( 'post_primary_tag', $post ) ) {
		$primary = $custom_primary;
	}

	if ( ! $primary ) {
		$tags = wp_get_post_tags( $post->ID, array(
			'fields' => 'all',
			'number' => 1
		) );
		if ( ! is_wp_error( $tags ) && isset( $tags[0] ) ) {
			$primary = $tags[0];
		}
	}

	return $primary;
}


/**
 * Adds a class to the body element for pages that
 * return today_disable_md_nav_toggle() as true.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param array $classes Array of class names
 * @return array $classes Array of class names
 **/
function today_navbar_body_class( $classes ) {
	if ( today_disable_md_nav_toggle() ) {
		$classes[] = 'disable-md-navbar-toggle';
	}

	return $classes;
}

add_filter( 'body_class', 'today_navbar_body_class' );


/**
 * Updates oEmbed markup generated by WordPress.
 * oEmbed videos are wrapped in a responsive wrapper div.
 * All oEmbeds are force-centered.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $html The returned oEmbed HTML.
 * @param object $data A data object result from an oEmbed provider.
 * @param string $url The URL of the content to be embedded.
 * @return mixed Modified oEmbed HTML string
 */
function today_responsive_embeds( $html, $data, $url ) {
	$oembed_class = '';
	if ( $data->type ) {
		$oembed_class = 'oembed-' . $data->type;
	}

	if ( $data->type === 'video' ) {
		$html = '<div class="embed-responsive embed-responsive-16by9">' . $html . '</div>';
	}

	return '<div class="embed oembed ' . $oembed_class . ' d-flex flex-column align-items-center">' . $html . '</div>';
}

add_filter( 'oembed_dataparse', 'today_responsive_embeds', 10, 3 );


/**
 * Updates classes applied to the wrapper element of
 * WordPress's [video] shortcode markup.
 * Force-centers all video embeds.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $output Video shortcode HTML output.
 * @param array $atts Array of video shortcode attributes.
 * @param string $video Video file.
 * @param int $post_id Post ID.
 * @param string $library Media library used for the video shortcode.
 */
function today_responsive_videos( $output, $atts, $video, $post_id, $library ) {
	$output = str_replace( 'class="wp-video"', 'class="wp-video embed embed-video mx-auto"', $output );
	return $output;
}

add_filter( 'wp_video_shortcode', 'today_responsive_videos', 10, 5 );


/**
 * Returns the URL of the UCF In The News page, or false.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @return mixed URL string, or false upon failure
 */
function today_get_external_stories_url() {
	$page = get_page_by_title( 'UCF in the News' );
	return ( $page ) ? get_permalink( $page ) : false;
}

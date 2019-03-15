<?php
/**
 * General utilities unique to the Today Child Theme
 */

/**
 * Returns an attachment ID for the desired thumbnail
 * image of a given post.  Returns a fallback if no image
 * is available.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return mixed Attachment ID (int) or null on failure
 */
function today_get_thumbnail_id( $post ) {
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
	if ( ! $attachment_id ) {
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
	$transient_key = 'today_oembed_thumb_' . md5( $embed_url );
	$transient     = get_transient( $transient_key );

	if ( $transient !== false ) {
		$retval = $transient;
	}
	else {
		$max_width       = ( $max_width && is_numeric( $max_width ) ) ? intval( $max_width ) : null;
		$max_height      = ( $max_height && is_numeric( $max_height ) ) ? intval( $max_height ) : null;
		$oembed          = new WP_oEmbed();
		$oembed_data     = null;
		$oembed_provider = $oembed->get_provider( $embed_url );
		$oembed_thumb    = null;
		$oembed_args     = array();

		// Not all oembed providers will support this, but try
		// to define a desired set of thumbnail dimensions:
		if ( $max_width && $max_width > 0 ) {
			$oembed_args['width'] = $max_width;
		}
		if ( $max_height && $max_height > 0 ) {
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

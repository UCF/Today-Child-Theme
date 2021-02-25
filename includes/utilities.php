<?php
/**
 * General utilities unique to the Today Child Theme
 */

/**
 * Filters requests for WordPress's built-in `get_post_thumbnail_id()`
 * to return the Header Image/Thumbnail field for posts.
 *
 * Useful for third-party plugins that reference thumbnails.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param mixed $value A determined thumbnail ID, or null
 * @param int $object_id Object ID (in this case, a post object ID)
 * @param string $meta_key The meta key name
 * @param bool $single Whether to return only the first value of the specified $meta_key
 * @return mixed The thumbnail ID, or null
 */
function today_filter_thumbnail_ids( $value, $object_id, $meta_key, $single ) {
    if ( $meta_key === '_thumbnail_id' ) {
		$post = get_post( $object_id );
		if ( $post instanceof WP_Post && $post->post_type === 'post' ) {
			$attachment = get_field( 'post_header_image', $post );
			$value      = isset( $attachment['id'] ) ? $attachment['id'] : null;
		}
    }

    return $value;
}

add_filter( 'get_post_metadata', 'today_filter_thumbnail_ids', 20, 4 );


/**
 * Returns existing author term information, or custom
 * author data (for posts, if set).
 *
 * @since 1.1.0
 * @author Jo Dickson
 * @param object $post WP_Post object
 * @return array Array of author data; expected format:
 *                 (
 *                     'term'  => {}|null,
 *                     'name'  => '',
 *                     'title' => '',
 *                     'photo' => []|null,
 *                     'bio'   => ''
 *                 )
 */
function today_get_post_author_data( $post ) {
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}
	if ( ! $post || ! in_array( $post->post_type, array( 'post', 'ucf_statement' ) ) ) return array();
	$author_data = array();

	if ( get_field( 'post_author_type', $post ) !== 'term' ) {
		$custom_author_name = get_field( 'post_author_byline', $post );
		// Require at least a name to proceed
		if ( $custom_author_name ) {
			$author_data['term']  = null;
			$author_data['name']  = wptexturize( $custom_author_name );
			$author_data['title'] = wptexturize( get_field( 'post_author_title', $post ) );
			$author_data['photo'] = get_field( 'post_author_photo', $post );
			$author_data['bio']   = get_field( 'post_author_bio', $post );
		}
	} else {
		$author_terms = wp_get_post_terms( $post->ID, 'tu_author' );
		if ( ! is_wp_error( $author_terms ) ) {
			$author_term = $author_terms[0] ?? null;
			if ( $author_term && $author_term->name ) {
				$author_data['term']  = $author_term;
				$author_data['name']  = wptexturize( $author_term->name );
				$author_data['title'] = wptexturize( get_field( 'author_title', $author_term ) );
				$author_data['photo'] = get_field( 'author_photo', $author_term );
				$author_data['bio']   = get_field( 'author_bio', $author_term );
			}
		}
	}

	return $author_data;
}


/**
 * Filters requests for WordPress's built-in author name retrieval functions.
 *
 * Useful for feeds and third-party plugins that reference basic post
 * author information.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_filter_post_author_name( $author_name ) {
	if ( ! is_admin() ) {
		global $post;
		if ( $post && in_array( $post->post_type, array( 'post', 'ucf_statement' ) ) ) {
			$author_data = today_get_post_author_data( $post );
			if ( isset( $author_data['name'] ) ) {
				$author_name = $author_data['name'];
			}
		}
	}

	return $author_name;
}

add_filter( 'the_author', 'today_filter_post_author_name' );
add_filter( 'the_author_display_name', 'today_filter_post_author_name' );


/**
 * Modifies the post's determined enclosure for use in RSS/Atom feeds.
 * Ensures post thumbnails in feeds are accurate with what's displayed
 * on the frontend.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_filter_post_feed_enclosure( $value, $object_id, $meta_key, $single ) {
	if ( is_feed() ) {
		// The `get_post_metadata` hook doesn't give us access to the
		// current metadata value. Fetch it manually here, making sure to
		// de-register this hook in the process.
		if ( ! $value && $meta_key === '' ) {
			remove_filter( 'get_post_metadata', 'today_filter_post_feed_enclosure', 99 );
        	$value = get_post_meta( $object_id, $meta_key, $single );
			add_filter( 'get_post_metadata', 'today_filter_post_feed_enclosure', 99, 4 );
		}

		// If get_post_meta() is fetching *all* of a post's metadata,
		// OR, if *just* the 'enclosure' value is being fetched:
		if (
			( is_array( $value ) && $meta_key === '' )
			|| $meta_key === 'enclosure'
		) {
			$enclosure_url = today_get_thumbnail_url( $object_id );

			// Perform a HEAD request to fetch headers for the thumbnail URL.
			// Enclosure tags require 'length' and 'type' attributes.
			$response = wp_remote_head( $enclosure_url );
			$enclosure_size = wp_remote_retrieve_header( $response, 'content-length' ) ?: 0;
			$enclosure_mime = wp_remote_retrieve_header( $response, 'content-type' ) ?: '';

			// This is the format expected by WP's built-in RSS templates *shrug*
			$enclosure = implode( "\n", array(
				$enclosure_url,
				$enclosure_size,
				$enclosure_mime
			) );

			if ( $meta_key === 'enclosure' ) {
				$value = $enclosure;
			}
			else if ( is_array( $value ) ) {
				$value['enclosure'] = $enclosure;
			}
		}
	}

	return $value;
}

add_filter( 'get_post_metadata', 'today_filter_post_feed_enclosure', 99, 4 );


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
	if ( $post->post_type === 'post' ) {
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
			// Get a thumbnail ID without a fallback.
			// Try to use a video poster as a fallback if possible
			$thumbnail_id = today_get_thumbnail_id( $post, false );

			if ( $thumbnail_id ) {
				$thumbnail_url = ucfwp_get_attachment_src_by_size( $thumbnail_id, $thumbnail_size );
			}
			else {
				// Get video url (prevent ACF oEmbed processing)
				$video_url           = get_field( 'post_header_video_url', $post, false );
				$video_thumbnail_w   = intval( get_option( "{$thumbnail_size}_size_w" ) );
				$video_thumbnail_h   = intval( get_option( "{$thumbnail_size}_size_h" ) );
				$video_thumbnail_url = today_get_oembed_thumbnail( $video_url, $video_thumbnail_w, $video_thumbnail_h );

				if ( $video_thumbnail_url ) {
					$thumbnail_url = $video_thumbnail_url;
				}
			}

			// If we still don't have anything at this point,
			// go grab the UCF Post List plugin's fallback image
			if ( ! $thumbnail_url ) {
				// Use the UCF Post List Shortcode plugin's
				// fallback thumbnail, if one is available
				if ( method_exists( 'UCF_Post_List_Config', 'get_option_or_default' ) ) {
					$thumbnail_id = UCF_Post_List_Config::get_option_or_default( 'ucf_post_list_fallback_image' );
					$thumbnail_id = is_numeric( $thumbnail_id ) ? intval( $thumbnail_id ) : null;

					if ( $thumbnail_id ) {
						$thumbnail_url = ucfwp_get_attachment_src_by_size( $thumbnail_id, $thumbnail_size );
					}
				}
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
 * Returns the aspect ratio of an assumed embed,
 * given width and height dimensions.
 *
 * @since 1.0.5
 * @author Jo Dickson
 * @param mixed $width String/int width dimension
 * @param mixed $height String/int height dimension
 * @return float Aspect ratio
 */
function today_get_embed_aspect_ratio( $width, $height ) {
	$width  = floatval( $width );
	$height = floatval( $height );

	// If we're missing a valid dimension, just assume
	// a default of 16 x 9:
	if ( ! $width || ! $height ) {
		return 1.77; // 16by9
	}

	return round( ( $width / $height ), 2 );
}


/**
 * Given a set of dimensions for an embed, this function
 * returns an appropriate Athena Framework `.embed-responsive-`
 * CSS class.
 *
 * @since 1.0.5
 * @author Jo Dickson
 * @param mixed $width String/int width dimension
 * @param mixed $height String/int height dimension
 * @return string CSS class
 */
function today_get_embed_responsive_class( $width, $height ) {
	$class = 'embed-responsive-16by9';

	$aspect_ratio = today_get_embed_aspect_ratio( $width, $height );

	switch ( $aspect_ratio ) {
		case $aspect_ratio <= 1:
			$class = 'embed-responsive-1by1';
			break;
		case $aspect_ratio > 1 && $aspect_ratio <= round( ( 4 / 3 ), 2 ):
			$class = 'embed-responsive-4by3';
			break;
		case $aspect_ratio > round( ( 16 / 9 ), 2 ):
			$class = 'embed-responsive-21by9';
			break;
		default:
			break;
	}

	return $class;
}


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

	$width  = property_exists( $data, 'width' ) ? $data->width : 0;
	$height = property_exists( $data, 'height' ) ? $data->height : 0;

	// Add embed-responsive wrapper around all (expected) video embed types,
	// except for Facebook videos, which don't play nicely with it:
	if ( $data->type === 'video' && $data->provider_name !== 'Facebook' ) {
		$embed_responsive_class = today_get_embed_responsive_class( $width, $height );
		$html = '<div class="embed-responsive ' . $embed_responsive_class . '">' . $html . '</div>';
	}

	// Add custom support for Juxtapose embeds.
	// NOTE: Unfortunately we have to assume that these types
	// of embeds should always be horizontal, because their
	// oEmbed dimension data is not accurate:
	if ( $data->provider_name === 'Knight Lab' && strpos( $html, '/juxtapose/' ) !== false ) {
		$html = str_replace( 'width=\'500\'', 'width=\'728\'', str_replace( 'height=\'500\'', 'height=\'410\'', $html ) );
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

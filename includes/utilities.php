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
	if ( $post->post_type === 'post' ) {
		$attachment = get_field( 'post_header_image', $post );
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

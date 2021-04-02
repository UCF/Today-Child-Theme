<?php
/**
 * Functions related to the UCF Resource Search Plugin
 * (used for External Stories in this site)
 */

/**
 * Returns a source for the given Resource Link post.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object for the Resource Link
 * @return string The source value for the Resource Link
 */
function today_get_resource_link_source( $post ) {
	$source = '';
	$sources = wp_get_post_terms( $post->ID, 'sources' );

	if ( ! empty( $sources ) ) {
		$source = wptexturize( $sources[0]->name );
	}

	return $source;
}


/**
 * Modifies the permalink for Resource Links to always
 * return the "Website URL" value.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $post_link The post's permalink.
 * @param object $post WP_Post object.
 * @param bool $leavename Whether to keep the post name.
 * @param bool $sample Is it a sample permalink.
 * @return string The permalink value for the Resource Link
 */
function today_get_resource_link_permalink( $post_link, $post, $leavename, $sample ) {
	if ( $post->post_type === 'ucf_resource_link' ) {
		$external_url = get_post_meta( $post->ID, 'ucf_resource_link_url', true );
		if ( $external_url ) {
			$post_link = $external_url;
		}
	}

	return $post_link;
}

add_filter( 'post_type_link', 'today_get_resource_link_permalink', 10, 4 );


/**
 * Utility function that determines if a Resource Link
 * has a valid permalink (links to an external document).
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param object $post WP_Post object for the Resource Link
 * @return boolean Whether or not the Resource Link has a valid permalink
 */
function today_resource_link_permalink_is_valid( $post ) {
	return get_permalink( $post ) === get_post_meta( $post->ID, 'ucf_resource_link_url', true );
}


/**
 * Adds a redirect for any Resource Link that doesn't have
 * a Website URL to go back to the site homepage.
 */
function today_resource_link_redirect() {
	global $wp_query, $post;

	if (
		$post
		&& $post->post_type === 'ucf_resource_link'
		&& ! today_resource_link_permalink_is_valid( $post )
	) {
		wp_redirect( home_url() );
		exit();
	}
}

add_filter( 'template_redirect', 'today_resource_link_redirect' );


/**
 * Modifies post type registration arguments for the UCF Resource Link CPT.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Array of post type registration arguments
 * @return array Array of post type registration arguments
 */
function today_resource_link_post_type_args( $args ) {
	$args['has_archive'] = false;

	return $args;
}

add_filter( 'ucf_resource_link_post_type_args', 'today_resource_link_post_type_args' );


/**
 * Adds the ACF Resource Link Custom Fields field group
 * and associated fields.
 *
 * @since 1.3.0
 * @author Cadie Stockman
 */
function today_add_resource_link_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Link Description field
		$fields[] = array(
			'key'               => 'field_5c9d1e7a9ec0c',
			'label'             => 'Link Description',
			'name'              => 'ucf_resource_link_description',
			'type'              => 'textarea',
			'instructions'      => 'A brief summary of the content being linked to.',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'     => '',
			'placeholder'       => '',
			'maxlength'         => '',
			'rows'              => 3,
			'new_lines'         => '',
		);

		// Defines Resource Link Custom Fields field group
		$field_group = array(
			'key'                   => 'group_5c9d1e65c699f',
			'title'                 => 'Resource Link Custom Fields',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'ucf_resource_link',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'today_add_resource_link_fields' );

<?php
/**
 * Functions related to the customized display of a tag cloud.
 */

/**
 * Removes the inline styles generated for tag cloud links.
 *
 * @author Cadie Brown
 * @since 1.0.0
 * @param string HTML tag cloud markup
 * @return string HTML tag cloud markup without inline styles
 **/
function today_remove_tag_cloud_inline_style( $tag_markup ){
	return preg_replace( '/ style=("|\')(.*?)("|\')/', '' , $tag_markup );
}
add_filter( 'wp_generate_tag_cloud', 'today_remove_tag_cloud_inline_style', 10, 1 );

/**
 * Formats and displays the wp_tag_cloud. We intentionally exclude the
 * 'Main Site Stories' tag from displaying.
 *
 * @author Cadie Brown
 * @since 1.0.0
 * @param object $post WP_Post object
 * @param string $classes Classes for the wrapping div tag
 * @return string HTML markup for the tag cloud
 **/
function today_get_tag_cloud( $post, $classes = '' ) {
	$display_tag_cloud = get_field( 'post_display_tag_cloud', $post );

	$tag_cloud_count      = get_field( 'post_tag_cloud_count', $post ) ?: 5;
	$main_site_stories_tag_id = get_term_by( 'slug', 'main-site-stories', 'post_tag' )->term_id;
	$post_tag_ids         = wp_get_post_tags( $post->ID, array(
		'fields'  => 'ids',
		'exclude' => $main_site_stories_tag_id,
	) );

	$args = array(
		'format'  => 'flat',
		'orderby' => 'count',
		'order'   => 'DESC',
		'number'  => $tag_cloud_count,
		'include' => $post_tag_ids,
		'echo'    => 0,
	);

	$tag_cloud_markup = wp_tag_cloud( $args );

	ob_start();
	if ( $display_tag_cloud && !empty( $tag_cloud_markup ) ) :
?>
	<div class="today-tag-cloud <?php echo $classes; ?>">
		<span class="text-uppercase font-weight-bold d-block mb-3 text-default-aw">More Topics</span>
		<?php echo $tag_cloud_markup; ?>
	</div>
<?php
	endif;
	return ob_get_clean();
}

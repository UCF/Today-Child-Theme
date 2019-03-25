<?php
/**
 * Functions related to the customized display of a tag cloud.
 */

/**
 * Removes the inline styles generated for tag cloud links.
 *
 * @author Cadie Brown
 * @since 1.0.0
 * @param string generated HTML tag cloud output
 * @return string generated HTML tag cloud output without inline styles
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
 * @see today_display_tag_cloud()
 * @param
 * @return string HTML markup for the tag cloud
 **/
function today_get_tag_cloud( $post, $classes = '' ) {
	$display_tag_cloud = get_field( 'post_display_tag_cloud', $post );

	$tag_cloud_count      = get_field( 'post_tag_cloud_count', $post ) ?: 5;
	$post_tag_ids         = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
	$main_site_stories_id = get_term_by( 'slug', 'main-site-stories', 'post_tag' )->term_id;
	var_dump( $main_site_stories_id );

	$args = array(
		'format'  => 'flat',
		'orderby' => 'count',
		'order'   => 'DESC',
		'number'  => $tag_cloud_count,
		'echo'    => 0,
		'include' => $post_tag_ids,
		'exclude' => $main_site_stories_id,
	);

	$tag_cloud_markup = wp_tag_cloud( $args );

	ob_start();
	if ( $display_tag_cloud ) :
?>
	<div class="today-tag-cloud <?php echo $classes; ?>">
		<?php echo $tag_cloud_markup; ?>
	</div>
<?php
	endif;
	return ob_get_clean();
}

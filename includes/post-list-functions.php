<?php
/**
 * Custom layouts for the UCF Post List Shortcode plugin for this theme.
 */

/**
 * Adds custom layouts for the UCF Post List plugin.
 *
 * @author Jo Dickson
 * @since 1.0.0
 */
function today_post_list_layouts( $layouts ) {
	$layouts['feature_horizontal'] = 'Horizontal Feature Layout';
	$layouts['feature_vertical']   = 'Vertical Feature Layout';
	$layouts['feature_condensed']  = 'Condensed Feature Layout';

	return $layouts;
}

add_filter( 'ucf_post_list_get_layouts', 'today_post_list_layouts' );


/**
 * Adds custom attributes per layout for the UCF Post List plugin.
 *
 * @author Jo Dickson
 * @since 1.0.0
 */
function today_post_list_sc_atts( $atts, $layout ) {
	if ( in_array( $layout, array( 'feature_horizontal', 'feature_vertical' ) ) ) {
		$atts['feature_layout__type'] = 'secondary';
	}

	return $atts;
}

add_filter( 'ucf_post_list_get_sc_atts', 'today_post_list_sc_atts', 10, 2 );


/**
 * Defines a new "feature_horizontal" layout for the [ucf-post-list] shortcode
 *
 * @since 1.0.0
 * @author Jo Dickson
 */

function today_post_list_display_feature_horizontal_before( $content, $posts, $atts ) {
	ob_start();
?>
<div class="ucf-post-list ucf-post-list-feature_horizontal" id="post-list-<?php echo $atts['list_id']; ?>">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_feature_horizontal_before', 'today_post_list_display_feature_horizontal_before', 10, 3 );


/**
 * Defines a new "feature_vertical" layout for the [ucf-post-list] shortcode
 *
 * @since 1.0.0
 * @author Jo Dickson
 */

function today_post_list_display_feature_vertical_before( $content, $posts, $atts ) {
	ob_start();
?>
<div class="ucf-post-list ucf-post-list-feature_vertical" id="post-list-<?php echo $atts['list_id']; ?>">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_feature_vertical_before', 'today_post_list_display_feature_vertical_before', 10, 3 );


/**
 * Defines a new "feature_condensed" layout for the [ucf-post-list] shortcode
 *
 * @since 1.0.0
 * @author Jo Dickson
 */

function today_post_list_display_feature_condensed_before( $content, $posts, $atts ) {
	ob_start();
?>
<div class="ucf-post-list ucf-post-list-feature_condensed" id="post-list-<?php echo $atts['list_id']; ?>">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_feature_condensed_before', 'today_post_list_display_feature_condensed_before', 10, 3 );


/**
 * Main post list display function for all 'feature' layouts.
 *
 * @since 1.0.0
 * @author Jo Dickson
 */
function today_post_list_display_feature( $content, $posts, $atts ) {
	if ( $posts && ! is_array( $posts ) ) { $posts = array( $posts ); }

	$item_col = 'col-lg';
	if ( $atts['posts_per_row'] > 0 && ( 12 % $atts['posts_per_row'] ) === 0 ) {
		// Use specific column size class if posts_per_row equates
		// to a valid grid size
		$item_col .= '-' . 12 / $atts['posts_per_row'];
	}

	ob_start();
?>
	<?php if ( $posts ): ?>
		<div class="row">

		<?php
		foreach ( $posts as $index => $item ) {
			if ( $atts['posts_per_row'] > 0 && $index !== 0 && ( $index % $atts['posts_per_row'] ) === 0 ) {
				echo '</div><div class="row">';
			}

			switch ( $atts['layout'] ) {
				case 'feature_horizontal':
					echo '<div class="' . $item_col . ' mb-4">';
					echo today_display_feature_horizontal( $item, $atts['feature_layout__type'], $atts['show_image'] );
					echo '</div>';

					break;
				case 'feature_vertical':
					echo '<div class="' . $item_col . ' mb-4">';
					echo today_display_feature_vertical( $item, $atts['feature_layout__type'] );
					echo '</div>';

					break;
				case 'feature_condensed':
				default:
					echo '<div class="' . $item_col . ' mb-3">';
					echo today_display_feature_condensed( $item );
					echo '</div>';
					break;
			}
		}
		?>

		</div>

	<?php else: ?>
		<div class="ucf-post-list-error">No results found.</div>
	<?php endif;

	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_feature_horizontal', 'today_post_list_display_feature', 10, 3 );
add_filter( 'ucf_post_list_display_feature_vertical', 'today_post_list_display_feature', 10, 3 );
add_filter( 'ucf_post_list_display_feature_condensed', 'today_post_list_display_feature', 10, 3 );

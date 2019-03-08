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
	$layouts['horizontal'] = 'Horizontal Feature Layout';
	$layouts['vertical']   = 'Vertical Feature Layout';
	$layouts['condensed']  = 'Condensed Feature Layout';

	return $layouts;
}

add_filter( 'ucf_post_list_get_layouts', 'today_post_list_layouts' );


/**
 * Adds custom attributes and modify default attribute values
 * per layout for the UCF Post List plugin.
 *
 * @author Jo Dickson
 * @since 1.0.0
 */
function today_post_list_sc_atts( $atts, $layout ) {
	// Create new `layout__type` attribute for horizontal and
	// vertical feature layouts to specify primary/secondary types
	if ( in_array( $layout, array( 'horizontal', 'vertical' ) ) ) {
		$atts['layout__type'] = 'secondary';
	}

	// Force thumbnail to display by default for horizontal features
	if ( $layout === 'horizontal' ) {
		$atts['show_image'] = true;
	}

	// Assign default `posts_per_row` for sane display
	// of extensive feature lists
	if ( in_array( $layout, array( 'horizontal', 'vertical', 'condensed' ) ) ) {
		$atts['posts_per_row'] = 1;
	}


	return $atts;
}

add_filter( 'ucf_post_list_get_sc_atts', 'today_post_list_sc_atts', 10, 2 );


/**
 * Defines a new "horizontal" layout for the [ucf-post-list] shortcode
 *
 * @since 1.0.0
 * @author Jo Dickson
 */

function today_post_list_display_horizontal_before( $content, $posts, $atts ) {
	ob_start();
?>
<div class="ucf-post-list ucf-post-list-horizontal" id="post-list-<?php echo $atts['list_id']; ?>">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_horizontal_before', 'today_post_list_display_horizontal_before', 10, 3 );


/**
 * Defines a new "vertical" layout for the [ucf-post-list] shortcode
 *
 * @since 1.0.0
 * @author Jo Dickson
 */

function today_post_list_display_vertical_before( $content, $posts, $atts ) {
	ob_start();
?>
<div class="ucf-post-list ucf-post-list-vertical" id="post-list-<?php echo $atts['list_id']; ?>">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_vertical_before', 'today_post_list_display_vertical_before', 10, 3 );


/**
 * Defines a new "condensed" layout for the [ucf-post-list] shortcode
 *
 * @since 1.0.0
 * @author Jo Dickson
 */

function today_post_list_display_condensed_before( $content, $posts, $atts ) {
	ob_start();
?>
<div class="ucf-post-list ucf-post-list-condensed" id="post-list-<?php echo $atts['list_id']; ?>">
<?php
	return ob_get_clean();
}

add_filter( 'ucf_post_list_display_condensed_before', 'today_post_list_display_condensed_before', 10, 3 );


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
				case 'horizontal':
					echo '<div class="' . $item_col . ' mb-4">';
					echo today_display_feature_horizontal( $item, $atts );
					echo '</div>';

					break;
				case 'vertical':
					echo '<div class="' . $item_col . ' mb-4">';
					echo today_display_feature_vertical( $item, $atts );
					echo '</div>';

					break;
				case 'condensed':
				default:
					echo '<div class="' . $item_col . ' mb-3">';
					echo today_display_feature_condensed( $item, $atts );
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

add_filter( 'ucf_post_list_display_horizontal', 'today_post_list_display_feature', 10, 3 );
add_filter( 'ucf_post_list_display_vertical', 'today_post_list_display_feature', 10, 3 );
add_filter( 'ucf_post_list_display_condensed', 'today_post_list_display_feature', 10, 3 );

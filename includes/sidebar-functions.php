<?php
/**
 * Functions related to content within sidebars in templates
 */

/**
 * Generic function for displaying any type of sidebar content.
 * Creates a consistent set of heading and content markup.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param string $title Optional heading text above the sidebar content
 * @param string $content Content to display in the sidebar
 * @return string Formatted sidebar contents
 */
function today_display_sidebar_content( $title, $content ) {
	ob_start();
	if ( $content ):
?>
<div class="mb-5">
	<?php if ( $title ): ?>
	<h2 class="h6 text-uppercase text-default-aw mb-4">
		<?php echo $title; ?>
	</h2>
	<?php endif; ?>

	<?php echo $content; ?>
</div>
<?php
	endif;
	return ob_get_clean();
}


/**
 * Displays a set of events suitable for inclusion within a sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Associative array of expected arguments + their values
 * @return string HTML markup for the events list
 */
function today_display_sidebar_events( $args=array() ) {
	$title     = isset( $args['title'] ) ? $args['title'] : 'Events at UCF';
	$feed_url  = isset( $args['feed_url'] ) ? $args['feed_url'] : '';
	$layout    = isset( $args['layout'] ) ? $args['layout'] : 'classic';
	$limit     = isset( $args['limit'] ) ? $args['limit'] : '';
	$more_url  = '';
	$more_text = isset( $args['more_text'] ) ? $args['more_text'] : 'View All Events';
	$content   = '';

	if ( isset( $args['more_url'] ) && !empty( $args['more_url'] ) ) {
		$more_url = $args['more_url'];
	}
	else if ( class_exists( 'UCF_Events_Config' ) ) {
		$more_url = str_replace( 'feed.json', '', UCF_Events_Config::get_option_or_default( 'feed_url' ) );
	}

	// Remove empty values from $sc_attr, allowing shortcode defaults
	// to take effect when an attr isn't present
	$sc_attr     = array_filter( array(
		'feed_url' => $feed_url,
		'layout'   => $layout,
		'limit'    => $limit
	) );
	$sc_attr_str = ' title=""';

	foreach ( $sc_attr as $key => $val ) {
		$sc_attr_str .= ' ' . $key . '="' . $val . '"';
	}

	$content = do_shortcode( '[ucf-events' . $sc_attr_str . ']' );

	if ( $more_url && $more_text ) {
		$content .= '<div class="text-right"><a href="' . $more_url . '" target="_blank">' . $more_text . '</a></div>';
	}

	return today_display_sidebar_content( $title, $content );
}


/**
 * Displays a list of external stories (Resource Links)
 * suitable for inclusion within a sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Associative array of expected arguments + their values
 * @return string HTML markup for the external stories list
 */
function today_display_sidebar_external_stories( $args=array() ) {
	$title    = isset( $args['title'] ) ? $args['title'] : 'UCF in the News';
	$layout   = isset( $args['layout'] ) ? $args['layout'] : 'condensed';
	$limit    = isset( $args['limit'] ) ? $args['limit'] : 4;
	$more_url = isset( $args['more_url'] ) ? $args['more_url'] : today_get_external_stories_url();
	$content  = '';

	// Remove empty values from $sc_attr, allowing shortcode defaults
	// to take effect when an attr isn't present
	$sc_attr     = array_filter( array(
		'layout'      => $layout,
		'numberposts' => $limit,
		'post_type'   => 'ucf_resource_link',
		'tax_resource_link_types'        => 'external-story',
		'tax_resource_link_types__field' => 'slug'
	) );
	$sc_attr_str = '';

	foreach ( $sc_attr as $key => $val ) {
		$sc_attr_str .= ' ' . $key . '="' . $val . '"';
	}

	$content = do_shortcode( '[ucf-post-list' . $sc_attr_str . ']' );

	if ( $more_url ) {
		$content .= '<div class="text-right"><a href="' . $more_url . '" target="_blank">View All<span class="sr-only"> Stories about UCF</span></a></div>';
	}

	return today_display_sidebar_content( $title, $content );
}


/**
 * Displays a menu.  Suitable for inclusion within a sidebar.
 *
 * @since 1.0.0
 * @author Jo Dickson
 * @param array $args Associative array of expected arguments + their values
 * @return string HTML markup for the menu
 */
function today_display_sidebar_menu( $args=array() ) {
	$title   = isset( $args['title'] ) ? $args['title'] : 'Resources';
	$menu    = isset( $args['menu'] ) ? $args['menu'] : 'Resources';
	$content = '';

	// If we don't have a menu to return, back out early:
	if ( ! $menu ) return;

	$content = wp_nav_menu( array(
		'menu'       => $menu,
		'menu_class' => 'list-unstyled',
		'echo'       => false
	) );

	return today_display_sidebar_content( $title, $content );
}


/**
 * Returns the sidebar markup for pages that utilize a customizable sidebar.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param integer $post_id WP Post ID
 * @return string HTML markup for the sidebar
 **/
function today_get_sidebar_markup( $post_id ) {
	$markup = '';

	if ( have_rows( 'sidebar_content', $post_id ) ) {
		while ( have_rows( 'sidebar_content', $post_id ) ) : the_row();
			switch ( get_row_layout() ) {
				case 'sidebar_events' :
					$feed_url  = get_sub_field( 'events_feed_url' );
					$layout    = get_sub_field( 'events_layout' ) ?: 'classic';
					$num_posts = get_sub_field( 'events_number_of_posts' ) ?: 4;
					$view_link = get_sub_field( 'events_view_all_link' );

					$markup .= today_display_sidebar_events( array(
						'feed_url' => $feed_url,
						'layout'   => $layout,
						'limit'    => $num_posts,
						'more_url' => $view_link
					) );
					break;
				case 'sidebar_in_the_news' :
					$layout    = get_sub_field( 'news_layout' ) ?: 'condensed';
					$num_posts = get_sub_field( 'news_number_of_posts' ) ?: 4;

					$markup .= today_display_sidebar_external_stories( array(
						'layout' => $layout,
						'limit'  => $num_posts
					) );
					break;
				case 'sidebar_resources_menu' :
					$menu = get_sub_field( 'resources_menu' ) ?: 'Resources';

					$markup .= today_display_sidebar_menu( array(
						'menu' => $menu
					) );
					break;
				case 'sidebar_spotlight' :
					if ( $spotlight = get_sub_field( 'spotlight_object' ) ) {
						$title = '';
						$content = do_shortcode( '[ucf-spotlight slug="' . $spotlight->post_name . '"]' );

						$markup .= today_display_sidebar_content( $title, $content );
					}
					break;
				case 'sidebar_custom_content' :
					if ( $custom_content = get_sub_field( 'custom_content' ) ) {
						$markup .= today_display_sidebar_content( null, $custom_content );
					}
					break;
				default :
					break;
			}
		endwhile;
	}

	return $markup;
}

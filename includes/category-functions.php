<?php
/**
 * Provides functions specifically for pages assigned to the Category template
 */


/**
 * Returns the sidebar events section markup.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @return string HTML markup for the sidebar events section.
 **/
function today_get_category_sidebar_events( $feed_url, $layout, $num_posts, $view_link ) {
	ob_start();
	?>
		<div class="mb-5">
			<h2 class="h6 text-uppercase text-default-aw mb-3">Events at UCF</h2>
			<?php echo do_shortcode( '[ucf-events feed_url="' . $feed_url . '" layout="' . $layout . '" limit="' . $num_posts . '" title=""]' ); ?>
			<p class="text-right"><a href="<?php echo $view_link; ?>">View All Events</a></p>
		</div>
	<?php
	return ob_get_clean();
}


/**
 * Returns the sidebar news section markup.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @return string HTML markup for the sidebar news section.
 **/
function today_get_category_sidebar_news( $layout, $num_posts ) {
	ob_start();
	?>
	<div class="mb-5">
		<h2 class="h6 text-uppercase text-default-aw mb-3">UCF In the News</h2>
		<?php echo do_shortcode( '[ucf-post-list layout="' . $layout . '" post_type="ucf_resource_link" numberposts="' . $num_posts . '"]' ); ?>
		<p class="text-right"><a href="<?php echo get_permalink( get_page_by_title( 'UCF in the News' ) ); ?>">View All UCF In The News</a></p>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * Returns the sidebar resources menu markup.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @return string HTML markup for the sidebar resources menu.
 **/
function today_get_category_sidebar_resources_menu( $menu_name ) {
	ob_start();
	?>
	<div class="mb-5">
		<h2 class="h6 text-uppercase text-default-aw mb-3">Resources</h2>
		<?php
			wp_nav_menu( array(
				'menu'       => $menu_name,
				'menu_class' => 'list-unstyled'
			) );
		?>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * Returns the sidebar spotlight markup.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param string $spotlight_slug The slug for the selected spotlight.
 * @return string HTML markup for the sidebar spotlight.
 **/
function today_get_category_sidebar_spotlight( $spotlight_slug ) {
	ob_start();
	?>
	<div class="mb-5">
		<?php echo do_shortcode( '[ucf-spotlight slug="' . $spotlight_slug . '"]' ); ?>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * Returns the sidebar custom content markup.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param string $custom_content The custom content markup
 * @return string HTML markup for the sidebar custom content section.
 **/
function today_get_category_sidebar_custom_content( $custom_content ) {
	ob_start();
	?>
	<div class="mb-5">
		<?php echo $custom_content; ?>
	</div>
	<?php
	return ob_get_clean();
}


/**
 * Returns the sidebar markup for category pages.
 *
 * @since 1.0.0
 * @author Cadie Brown
 * @param integer $post_id WP Post ID
 * @return string HTML markup for the category sidebar
 **/
function today_get_category_sidebar_markup( $post_id ) {
	$markup = '';

	if ( have_rows( 'sidebar_content', $post_id ) ) {
		while ( have_rows( 'sidebar_content', $post_id ) ) : the_row();
			switch ( get_row_layout() ) {
				case 'category_sidebar_events' :
					$feed_url  = get_sub_field( 'events_feed_url' ) ?: 'https://events.ucf.edu/upcoming/feed.json';
					$layout    = get_sub_field( 'events_layout' ) ?: 'classic';
					$num_posts = get_sub_field( 'events_number_of_posts' ) ?: 4;
					$view_link = get_sub_field( 'events_view_all_link' ) ?: 'https://events.ucf.edu/upcoming/';

					$markup .= today_get_category_sidebar_events( $feed_url, $layout, $num_posts, $view_link );
					break;
				case 'category_sidebar_in_the_news' :
					$layout    = get_sub_field( 'news_layout' ) ?: 'condensed';
					$num_posts = get_sub_field( 'news_number_of_posts' ) ?: 4;

					$markup .= today_get_category_sidebar_news( $layout, $num_posts );
					break;
				case 'category_sidebar_resources_menu' :
					$menu_name = get_sub_field( 'resources_menu_name' ) ?: 'Resources';

					$markup .= today_get_category_sidebar_resources_menu( $menu_name );
					break;
				case 'category_sidebar_spotlight' :
					if ( $spotlight = get_sub_field( 'spotlight_object' ) ) {
						$markup .= today_get_category_sidebar_spotlight( $spotlight->post_name );
					}
					break;
				case 'category_sidebar_custom_content' :
					if ( $custom_content = get_sub_field( 'custom_content' ) ) {
						$markup .= today_get_category_sidebar_custom_content( $custom_content );
					}
					break;
				default :
					break;
			}
		endwhile;
	}

	return $markup;
}

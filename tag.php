<?php
	get_header();

	$posts = get_posts( array(
		'numberposts' => 10
	) );

	if( isset( $posts ) ) {
		$first_post = array_shift( $posts );
	}

	$atts = array(
		'posts_per_row' => 1,
		'layout'        => 'vertical',
		'layout__type'  => 'primary'
	);
?>

<div class="container mt-4 mb-5 pb-sm-4">
	<div class="row">
		<div class="col-md-8">
			<?php
				if( isset( $posts ) && isset( $first_post ) ) {
					echo today_post_list_display_feature( '', $first_post, $atts );

					$atts['posts_per_row'] = 3;
					echo today_post_list_display_feature( '', $posts, $atts );
				} else {
					echo "No results found.";
				}
			?>
		</div>
		<div class="col-md-4">
			<h2 class="heading-underline h6">Events at UCF</h2>
			<?php echo do_shortcode('[ucf-events feed_url="https://events.ucf.edu/upcoming/feed.json" layout="classic" offset="1" limit="4" title=""]'); ?>
			<a href="https://events.ucf.edu/upcoming/">View All Events</a>

			<h2 class="heading-underline h6 mt-5">UCF In the News</h2>
			<?php echo do_shortcode('[ucf-post-list layout="condensed" post_type="ucf_resource_link" numberposts="4"]'); ?>
			<a href="in-the-news/">View All</a>

			<h2 class="heading-underline h6 mt-5">Resources</h2>
			<?php
				wp_nav_menu( array(
					'menu'       => 'Resources',
					'menu_class' => 'list-unstyled'
				) );
			?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

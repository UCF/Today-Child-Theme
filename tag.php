<?php
	get_header();

	$posts = get_posts( array(
		'numberposts' => 10,
		'tag'         => get_queried_object()->slug
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
			<?php if ( $first_post ): ?>
				<?php echo today_display_feature_vertical( $first_post, array( 'layout__type' => 'primary' ) ); ?>
			<?php endif; ?>

			<?php if ( $posts ): ?>
				<div class="row">
					<?php foreach ( $posts as $post ): ?>
						<div class="col-lg-4">
							<?php echo today_display_feature_vertical( $post ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! $first_post && ! $posts ): ?>
				<p>No results found.</p>
			<?php endif; ?>
		</div>
		<div class="col-md-4">
			<h2 class="h6 text-uppercase text-default-aw mb-4">Events at UCF</h2>
			<?php echo do_shortcode('[ucf-events feed_url="https://events.ucf.edu/upcoming/feed.json" layout="classic" offset="1" limit="4" title=""]'); ?>
			<a href="https://events.ucf.edu/upcoming/">View All Events</a>

			<h2 class="h6 text-uppercase text-default-aw mb-4 mt-5">UCF In the News</h2>
			<?php echo do_shortcode('[ucf-post-list layout="condensed" post_type="ucf_resource_link" numberposts="4"]'); ?>
			<a href="<?php echo get_permalink( get_page_by_title( "UCF in the News" ) ); ?>">View All</a>

			<h2 class="h6 text-uppercase text-default-aw mb-4 mt-5">Resources</h2>
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

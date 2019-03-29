<?php get_header(); ?>

<div class="container mt-3 mt-md-4 mb-5 pb-sm-4">
	<div class="row">
		<div class="col-lg-8">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ): the_post(); ?>
					<div class="pb-2">
						<?php
						echo today_display_feature_horizontal( $post, array(
							'layout'        => 'horizontal',
							'show_image'    => false,
							'show_excerpt'  => true,
							'show_subhead'  => true
						) );
						?>
					</div>
				<?php endwhile; ?>
				<?php ucfwp_the_posts_pagination(); ?>
			<?php else : ?>
				<div class="alert alert-info">
					<p>No recent news articles found.</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-lg-4 pl-lg-5 mt-4 mt-lg-0">
			<h2 class="h6 text-uppercase text-default-aw mb-4">Resources</h2>
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

<?php get_header(); ?>

<div class="container mb-5">
	<div class="row">
		<div class="col-lg-8">
			<?php if ( have_posts() ) : ?>
				<div class="row">
					<?php while ( have_posts() ): the_post(); ?>
						<div class="col-lg-4 mb-4">
							<?php echo today_display_feature_vertical( $post ); ?>
						</div>
					<?php endwhile; ?>
				</div>
				<?php ucfwp_the_posts_pagination(); ?>
			<?php else : ?>
				<div class="alert alert-info">
					<p>There are currently no stories for <?php echo date( 'F Y' ); ?>.</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-lg-4 pl-lg-5 mt-4 mt-lg-0">
			<?php echo today_display_sidebar_menu(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

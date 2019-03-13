<?php
/**
 * Template Name: Archives
 */
get_header();
?>

<div class="container">
	<div class="row">
		<div class="col-md-8">
			<?php today_archive_pagination(); ?>
			<?php if ( have_posts() ) : ?>
				<?php echo today_post_list_display_feature( '', $posts, array( 'posts_per_row' => 3, 'layout' => 'vertical' ) ); ?>
				<?php ucfwp_the_posts_pagination(); ?>
			<?php else : ?>
				<div class="alert alert-info">
					<p>There are currently no stories for <?php echo date( 'F Y' ); ?>.</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-4">
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

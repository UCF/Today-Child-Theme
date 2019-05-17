<?php get_header(); the_post(); ?>

<?php
$primary = today_get_homepage_content( $post->ID, true );
$secondary = today_get_homepage_content( $post->ID );
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-4 mt-md-5 mb-5 pb-sm-4">
		<?php echo $primary; ?>
		<?php if ( get_field( 'enable_sidebar' ) ) : ?>
		<div class="row">
			<div class="col-lg-8">
				<?php echo $secondary; ?>
			</div>
			<div class="col-lg-4 pl-lg-5 mt-5 mt-lg-0">
				<?php echo today_get_sidebar_markup( $post->ID ); ?>
			</div>
		</div>
		<?php else : ?>
			<?php echo $secondary; ?>
		<?php endif; ?>

	</div>
</article>

<?php get_footer(); ?>

<?php get_header(); the_post(); ?>

<?php
$content = today_get_homepage_content( $post->ID );
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-4 mt-md-5 mb-5 pb-sm-4">

		<?php if ( get_field( 'enable_sidebar' ) ) : ?>
		<div class="row">
			<div class="col-lg-8">
				<?php echo $content; ?>
			</div>
			<div class="col-lg-4 pl-lg-5">
				<?php echo today_get_sidebar_markup( $post->ID ); ?>
			</div>
		</div>
		<?php else : ?>
			<?php echo $content; ?>
		<?php endif; ?>

	</div>
</article>

<?php get_footer(); ?>

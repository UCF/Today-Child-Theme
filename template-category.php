<?php
/**
 * Template Name: Category
 * Template Post Type: page
 */
?>
<?php get_header(); the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-2 mt-md-3 mb-5 pb-sm-4">
	<?php if ( get_field( 'category_enable_sidebar' ) ) : ?>
		<div class="row">
			<div class="col-lg-8">
				<?php the_content(); ?>
			</div>
			<div class="col-lg-4 pl-lg-5">
				<?php echo today_get_category_sidebar_markup( $post->ID ); ?>
			</div>
		</div>
	<?php else : ?>
		<?php the_content(); ?>
	<?php endif; ?>
</article>

<?php get_footer(); ?>

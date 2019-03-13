<?php
/**
 * Template Name: Archives
 */
get_header();
?>

<div class="container">
	<?php today_archive_pagination(); ?>
	<div class="row">
		<div class="col-md-8">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
				<article class="<?php echo $post->post_status; ?> post-list-item mb-4">
					<h2 class="h3">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>
					<div class="meta">
						<span class="date text-muted text-uppercase letter-spacing-3"><?php the_time( 'F j, Y' ); ?></span>
					</div>
					<div class="summary">
						<?php the_excerpt(); ?>
					</div>
				</article>
				<?php endwhile; ?>

				<?php ucfwp_the_posts_pagination(); ?>
			<?php else : ?>
				<div class="alert alert-info">
					<p>There are currently no stories for <?php echo date( 'F Y' ); ?>.</p>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-md-4">

		</div>
	</div>
</div>

<?php get_footer(); ?>

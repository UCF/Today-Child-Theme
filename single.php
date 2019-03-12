<?php get_header(); the_post(); ?>

<?php
$header_media = today_get_post_header_media( $post );
$source       = today_get_post_source( $post );
$social       = ( shortcode_exists( 'ucf-social-links' ) ) ? do_shortcode( '[ucf-social-links]' ) : '';
$author_bio   = today_get_post_author_bio( $post );
$related      = today_get_post_related( $post );
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-3 mt-sm-4">
		<?php echo $header_media; ?>
	</div>
	<div class="container mb-5 pb-sm-4">
		<div class="row mb-4 mb-md-5">
			<div class="col-lg-10 offset-lg-1 px-lg-5 col-xl-8 offset-xl-2 px-xl-3">
				<?php the_content(); ?>

				<?php echo $source; ?>

				<?php if ( $social ): ?>
				<div class="mt-5">
					<?php echo $social; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $author_bio || $related ): ?>
		<footer>
			<?php if ( $author_bio ): ?>
			<div class="row mb-4 mb-md-5">
				<div class="col-lg-10 offset-lg-1 px-lg-5 col-xl-8 offset-xl-2 px-xl-3">
					<hr class="mb-4 mb-md-5">
					<?php echo $author_bio; ?>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( $related ): ?>
			<hr class="mb-5">
			<?php echo $related; ?>
			<?php endif; ?>
		</footer>
		<?php endif; ?>
	</div>
</article>

<?php get_footer(); ?>




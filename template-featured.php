<?php
/**
 * Template Name: Featured Story
 * Template Post Type: post
 */
?>

<?php get_header(); the_post(); ?>

<?php
$header_media = today_get_post_header_media( $post );
$source       = today_get_post_source( $post );
$author_bio   = today_get_post_author_bio( $post );
$comment_form = today_get_post_comment_form( $post );
$comments     = today_get_post_comments( $post );
$related      = today_get_post_related( $post );
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-3 mt-sm-4 mb-4 mb-md-5">
		<?php echo $header_media; ?>
	</div>
	<div class="container mb-5 pb-sm-4">
		<div class="row mb-4 mb-md-5">
			<div class="col-lg-10 offset-lg-1 px-lg-5 col-xl-8 offset-xl-2 px-xl-3">
				<?php the_content(); ?>
				<?php echo $source; ?>
			</div>
		</div>

		<?php echo do_shortcode( '[ucf-social-links]' ); ?>

		<?php if ( $author_bio ): ?>
			<hr class="my-4 my-md-5">
			<?php echo $author_bio; ?>
			<hr class="my-4 my-md-5">
		<?php endif; ?>

		<?php echo $comment_form; ?>
		<?php echo $comments; ?>
		<?php echo $related; ?>
	</div>
</article>

<?php get_footer(); ?>

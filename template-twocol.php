<?php
/**
 * Template Name: Two Column
 * Template Post Type: post
 */
?>

<?php get_header(); the_post(); ?>

<?php
$header_media   = today_get_post_header_media( $post );
$source         = today_get_post_source( $post );
$author_bio     = today_get_post_author_bio( $post );
$social         = ( shortcode_exists( 'ucf-social-links' ) ) ? do_shortcode( '[ucf-social-links layout="affixed"]' ) : '';
$more_headlines = today_get_post_more_headlines( $post );
$cat_headlines  = today_get_post_cat_headlines( $post );
$tag_headlines  = today_get_post_tag_headlines( $post );
$tag_cloud      = today_get_tag_cloud( $post, 'mb-5' );
?>

<article class="<?php echo $post->post_status; ?> post-list-item"  aria-label="<?php echo esc_attr( get_the_title() ); ?>">
	<div class="container mt-3 mt-sm-4 mb-5 pb-sm-4">
		<div class="row">
			<div class="col-lg-8">
				<?php echo $header_media; ?>

				<div class="post-content">
					<?php the_content(); ?>
				</div>

				<?php echo $source; ?>

				<?php if ( $author_bio ): ?>
				<hr class="my-4 my-md-5">
				<footer>
					<?php echo $author_bio; ?>
				</footer>
				<?php endif; ?>
			</div>
			<div class="col-lg-4 pl-lg-5">
				<hr class="mt-4 mb-5 mt-md-5 hidden-lg-up">

				<?php echo $social; ?>

				<?php if ( $more_headlines ): ?>
				<div class="mb-5">
					<?php echo $more_headlines; ?>
				</div>
				<?php endif; ?>

				<?php if ( $tag_headlines ): ?>
				<div class="mb-5">
					<?php echo $tag_headlines; ?>
				</div>
				<?php endif; ?>

				<?php if ( $cat_headlines ): ?>
				<div class="mb-5">
					<?php echo $cat_headlines; ?>
				</div>
				<?php endif; ?>

				<?php echo $tag_cloud; ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>

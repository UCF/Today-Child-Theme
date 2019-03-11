<?php get_header(); the_post(); ?>

<?php
$header_media   = today_get_post_header_media( $post );
$source         = today_get_post_source( $post );
$author_bio     = today_get_post_author_bio( $post );
$comment_form   = today_get_post_comment_form( $post );
$comments       = today_get_post_comments( $post );
$more_headlines = today_get_post_more_headlines( $post );
$cat_headlines  = today_get_post_cat_headlines( $post );
$tag_headlines  = today_get_post_tag_headlines( $post );
$topics_list    = today_get_post_topics_list( $post );
?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-3 mt-sm-4 mb-5 pb-sm-4">
		<div class="row">
			<div class="col-lg-8">
				<?php echo $header_media; ?>

				<?php
				// TODO is this necessary anymore?
				// echo strip_tags( $content, '<p><a><ol><ul><li><em><strong><img><blockquote><div>' );
				?>
				<?php the_content(); ?>

				<?php if ( $author_bio ): ?>
					<hr class="my-4">
					<?php echo $author_bio; ?>
					<hr class="my-4">
				<?php endif; ?>

				<?php echo $comment_form; ?>
			</div>
			<div class="col-lg-4 pl-lg-5">
				<?php echo $source; ?>
				<?php echo do_shortcode( '[ucf-social-links]' ); ?>
				<?php echo $more_headlines; ?>
				<?php echo $tag_headlines; ?>
				<?php echo $cat_headlines; ?>
				<?php echo $topics_list; ?>
				<?php echo $comments; ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>

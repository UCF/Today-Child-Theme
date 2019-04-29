<?php
/**
 * Header content template for Posts (both templates)
 */
?>

<?php
global $post;

$primary_category = today_get_primary_category( $post );
$primary_category = ( $primary_category ) ? wptexturize( $primary_category->name ) : null;
$title            = wptexturize( $post->post_title );
$deck             = get_field( 'post_header_deck', $post );
$meta             = today_get_post_meta_info( $post );
$post_template    = get_page_template_slug( $post->ID );
?>

<?php if ( $title ): ?>
<div class="container mt-4 mt-md-5">
	<?php if ( $post_template === 'single.php' ) : ?>
	<div class="row">
		<div class="col-xl-10 offset-xl-1">
	<?php endif; ?>
			<?php if ( $primary_category ): ?>
			<span class="d-block mb-3 text-default-aw font-weight-bold text-uppercase"><?php echo $primary_category; ?></span>
			<?php endif; ?>

			<h1 class="mb-3">
				<?php echo $title; ?>
			</h1>

			<?php if ( $deck ): ?>
			<div class="lead mb-3">
				<?php echo $deck; ?>
			</div>
			<?php endif; ?>

			<?php if ( $meta ): ?>
			<div class="mb-3">
				<?php echo $meta; ?>
			</div>
			<?php endif; ?>
	<?php if ( $post_template === 'single.php' ) : ?>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>

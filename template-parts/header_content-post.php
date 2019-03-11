<?php
/**
 * Header content template for Posts with the 'Default' page template
 */
?>

<?php
global $post;

$primary_category = today_get_primary_category( $post );
$primary_category = ( $primary_category ) ? wptexturize( $primary_category->name ) : null;
$title            = wptexturize( $post->post_title );
$subtitle         = get_field( 'post_header_subtitle', $post );
$deck             = get_field( 'post_header_deck', $post );
$meta             = today_get_post_meta_info( $post );
?>

<?php if ( $title ): ?>
<div class="container mt-4 mt-md-5">
	<?php if ( $primary_category ): ?>
	<span class="d-block mb-3 text-default-aw font-weight-bold text-uppercase"><?php echo $primary_category; ?></span>
	<?php endif; ?>

	<h1 class="mb-3">
		<?php echo $title; ?>
	</h1>

	<?php if ( $subtitle ): ?>
	<p class="font-weight-bold mb-3">
		<?php echo $subtitle; ?>
	</p>
	<?php endif; ?>

	<?php if ( $deck ): ?>
	<p class="lead mb-3">
		<?php echo $deck; ?>
	</p>
	<?php endif; ?>

	<?php if ( $meta ): ?>
	<div class="mb-3">
		<?php echo $meta; ?>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>

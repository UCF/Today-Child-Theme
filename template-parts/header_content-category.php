<?php
/**
 * Header content template for the 'Category' page template
 */
?>

<?php
global $post;

$title = wptexturize( $post->post_title );
?>

<?php if ( $title ): ?>
<div class="container mt-4 mt-md-5">
	<h1 class="mb-4">
		<?php echo $title; ?>
	</h1>
</div>
<?php endif; ?>

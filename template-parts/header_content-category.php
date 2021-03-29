<?php
/**
 * Header content template for Category archive pages
 */
?>

<?php
$term = get_queried_object();

$title = get_field( 'category_page_headline', $term ) ?: ucfwp_get_header_title( null ) . ' News';
?>

<?php if ( $title ): ?>
<div class="container mt-4 mt-md-5">
	<h1 class="mb-4">
		<?php echo $title; ?>
	</h1>
</div>
<?php endif; ?>

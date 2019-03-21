<?php
/**
 * Header content template for the 'Section' page template
 */
?>

<?php

$title_string = ucfwp_get_header_title( null );

ob_start();

?>
<h1 class="text-uppercase h4 mb-0"><?php echo $title_string; ?></h1>
<?php

$title = ob_get_clean();

?>
<?php if ( $title ): ?>
<div class="container mt-3 mt-md-3">
	<?php echo today_archive_pagination( $title ); ?>
	<hr>
</div>
<?php endif; ?>

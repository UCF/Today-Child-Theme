<?php
/**
 * Header content template for the archive pages
 */
?>

<?php
$title_string = ucfwp_get_header_title( null );
$title = '';
$header_content = '';

if ( is_date() ) {
	$title = today_archive_pagination( '<h1 class="text-uppercase h4 mb-0">' . $title_string . '</h1>' );
	$header_content = '<div class="container mt-3">' . $title . '<hr></div>';
}
else {
	$title = '<h1 class="mb-3">' . $title_string . '</h1>';
	$header_content = '<div class="container mt-4 mt-md-5">' . $title . '<hr></div>';
}
?>

<?php echo $header_content; ?>

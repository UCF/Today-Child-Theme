<?php

function today_get_homepage_content( $post_id ) {
	$content = '';

	switch ( get_field( 'home_content_type' ) ) {
		case 'latest':
			break;
		case 'curated':
			break;
		case 'custom':
		default:
			ob_start();
			the_content();
			$content = ob_get_clean();
			break;
	}

	return $content;
}

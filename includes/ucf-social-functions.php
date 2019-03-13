<?php

/**
 * Custom layout for content displayed before social links
 *
 * Ported from Today-Bootstrap
 *
 * @author Jo Dickson
 * @since 1.0.0
 * @param array $atts shortcode attributes
 * @return string
 **/
if ( ! function_exists( 'ucf_social_links_display_affixed_before' ) ) {
	function ucf_social_links_display_affixed_before( $content='', $atts ) {
		ob_start();
	?>
		<aside class="ucf-social-links ucf-social-links-affixed">
	<?php
		return ob_get_clean();
	}
}

add_filter( 'ucf_social_links_display_affixed_before', 'ucf_social_links_display_affixed_before', 10, 2 );

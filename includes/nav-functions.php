<?php

function today_get_nav_type() {
	return '';
}

add_filter( 'ucfwp_get_nav_type', 'today_get_nav_type' );

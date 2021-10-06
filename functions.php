<?php
define( 'TODAY_THEME_DIR', trailingslashit( get_stylesheet_directory() ) );


// Theme foundation
include_once TODAY_THEME_DIR . 'includes/utilities.php';
include_once TODAY_THEME_DIR . 'includes/config.php';
include_once TODAY_THEME_DIR . 'includes/meta.php';
include_once TODAY_THEME_DIR . 'includes/excerpts.php';
include_once TODAY_THEME_DIR . 'includes/features.php';
include_once TODAY_THEME_DIR . 'includes/nav-functions.php';
include_once TODAY_THEME_DIR . 'includes/header-functions.php';
include_once TODAY_THEME_DIR . 'includes/footer-functions.php';
include_once TODAY_THEME_DIR . 'includes/sidebar-functions.php';
include_once TODAY_THEME_DIR . 'includes/homepage-functions.php';
include_once TODAY_THEME_DIR . 'includes/post-functions.php';
include_once TODAY_THEME_DIR . 'includes/pegasus-functions.php';
include_once TODAY_THEME_DIR . 'includes/archive-functions.php';
include_once TODAY_THEME_DIR . 'includes/tag-cloud-functions.php';
include_once TODAY_THEME_DIR . 'includes/pegasus-list-functions.php';

// AMP
include_once TODAY_THEME_DIR . 'includes/amp-functions.php';


// Required plugin extras/overrides
include_once TODAY_THEME_DIR . 'includes/weather-functions.php';
include_once TODAY_THEME_DIR . 'includes/post-list-functions.php';
include_once TODAY_THEME_DIR . 'includes/ucf-resource-links-functions.php';


// Plugin extras/overrides

if ( class_exists( 'UCF_Social_Common' ) ) {
	include_once TODAY_THEME_DIR . 'includes/ucf-social-functions.php';
}

if ( class_exists( 'UCF_Events_Common' ) ) {
	include_once TODAY_THEME_DIR . 'includes/ucf-events-functions.php';
}

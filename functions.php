<?php

// Theme foundation
include_once 'includes/utilities.php';
include_once 'includes/config.php';
include_once 'includes/meta.php';
include_once 'includes/excerpts.php';
include_once 'includes/features.php';
include_once 'includes/nav-functions.php';
include_once 'includes/header-functions.php';
include_once 'includes/post-functions.php';
include_once 'includes/archive-functions.php';
include_once 'includes/weather-functions.php';
include_once 'includes/tag-cloud-functions.php';
include_once 'includes/category-functions.php';


// Plugin extras/overrides

if ( class_exists( 'UCF_Post_List_Common' ) ) {
	include_once 'includes/post-list-functions.php';
}

if ( class_exists( 'UCF_Social_Common' ) ) {
	include_once 'includes/ucf-social-functions.php';
}

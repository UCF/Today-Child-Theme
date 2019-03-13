<?php

// Theme foundation
include_once 'includes/utilities.php';
include_once 'includes/config.php';
include_once 'includes/meta.php';
include_once 'includes/excerpts.php';
include_once 'includes/features.php';
include_once 'includes/nav-functions.php';
include_once 'includes/archive-functions.php';


// Plugin extras/overrides

if ( class_exists( 'UCF_Post_List_Common' ) ) {
	include_once 'includes/post-list-functions.php';
}

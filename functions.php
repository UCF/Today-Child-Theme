<?php

// Theme foundation
include_once 'includes/config.php';
include_once 'includes/meta.php';
include_once 'includes/nav-functions.php';
include_once 'includes/post-display-functions.php';


// Plugin extras/overrides

if ( class_exists( 'UCF_Post_List_Common' ) ) {
	include_once 'includes/post-list-functions.php';
}

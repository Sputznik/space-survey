<?php

// INCLUDE THE NECESSARY FILES FOR UPLOAD
if (!function_exists('wp_handle_upload')) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
}


if (isset($_FILES['file'])) {
	// UPLOAD THE FILE
	$movefile = wp_handle_upload($_FILES['file'], array('test_form' => false));

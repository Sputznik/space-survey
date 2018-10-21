<?php
	/*
	Plugin Name: Space Survey
	Plugin URI: https://sputznik.com/
	Description: A survey plugin.
	Version: 1.0.0
	Author: Sputznik
	Author URI: https://sputznik.com/
	*/


	if( ! defined( 'ABSPATH' ) ){
		exit;
	}
	$inc_files = array(
		'admin/class-space-admin.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
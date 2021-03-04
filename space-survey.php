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
	//Constant changes all the js and css version on the go
	define( 'SPACE_SURVEY_VERSION', '2.2.2' );	//2.1.1.

	$inc_files = array(
		'class-space-base.php',
		'db/db.php',
		'admin/class-space-admin.php',
		'frontend/frontend.php',
		'helper/helper.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}

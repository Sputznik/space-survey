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
		'db/db.php',
		'admin/class-space-admin.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	
	
	/*
	add_action( 'init', function(){
		add_rewrite_rule('survey/(\d*)$', 'index.php?survey=$matches[1]', 'top');
	} );
	
	add_action( 'query_vars', function( $query_vars ){
		$query_vars[] = 'survey';
		return $query_vars;
	} );
	
	add_action( 'parse_request', function( &$wp ){
		if ( array_key_exists( 'survey', $wp->query_vars ) ){
			
			$template = apply_filters( 'space-survey-template', 'templates/survey.php' );
			
			include( $template );
			
			exit();
		}
	} );
	*/
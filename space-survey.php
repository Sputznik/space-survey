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

include plugin_dir_path( __FILE__ ) .'class-space-survey-tables.php';
include plugin_dir_path( __FILE__ ) .'menu.php';

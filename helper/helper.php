<?php


	$inc_files = array(
		'class-space-util.php',
		'class-space-export.php',
		'class-space-batch-process.php',
		'class-space-csv.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}

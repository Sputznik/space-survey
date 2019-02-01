<?php 

	
	$inc_files = array(
		'class-space-export.php',
		'class-space-batch-process.php'
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	
	
<?php 

	
	$inc_files = array(
		'class-space-db-base.php',
		'class-space-db-survey.php',
		'class-space-db-question.php',
		'class-space-db-choice.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	
	SPACE_DB_QUESTION::getInstance();
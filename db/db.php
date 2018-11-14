<?php 

	
	$inc_files = array(
		'class-space-db-base.php',
		'class-space-db-survey.php',
		'class-space-db-page.php',
		'class-space-db-question.php',
		'class-space-db-choice.php',
		'class-space-db-page-question-relation.php',
		'class-space-db-guest.php',
		'class-space-db-response.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
	
	
<?php
	
	require_once( plugin_dir_path(__FILE__).'../../forms/class-space-results-form.php' );
			
	$survey_db = SPACE_DB_SURVEY::getInstance();
			
	$totalGuests = $survey_db->totalGuests( $_GET['post'] );
			
	_e( '<p>Total Forms that have been submitted: <b>'. $totalGuests .'</b></p>' );
			
	// RESULTS FORM
	$results_form = new SPACE_RESULTS_FORM();
	$results_form->display();
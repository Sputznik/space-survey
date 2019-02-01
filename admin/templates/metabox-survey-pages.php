<?php

	require_once( plugin_dir_path(__FILE__).'../../forms/class-space-page-form.php' );

	// GET LIST OF PAGES FOR THE SURVEY
	$pages = array();
	if( isset( $_GET['post'] ) ){
		$survey_db = SPACE_DB_SURVEY::getInstance();
		$pages = $survey_db->listPages( $_GET['post'] );
	}
			
	// PAGE FORM
	$page_form = new SPACE_PAGE_FORM( $pages );
	$page_form->display();
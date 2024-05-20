<?php
/*
* URLS HELPER CLASS
*/

class SPACE_URLS extends SPACE_BASE{

	function csvs( $survey_id, $filterChoices = array(), $hide_zero_attempted = 0 ){
		$url = admin_url( 'admin.php?page=space-export&survey_id='.$survey_id );
		if( is_array( $filterChoices ) && count( $filterChoices ) ){
			$choices_str = implode( ',', $filterChoices );
			$url .= "&choices=$choices_str";
		}
		if( $hide_zero_attempted ){
			$url .= "&hide-zero-attempted=1";
		}
		return $url;
	}

	function responses( $survey_id ){
		return admin_url( 'admin.php?page=space-responses&survey='.$survey_id );
	}

	function edit_survey( $survey_id ){

	}


}

// CREATE AN INSTANCE FOR THE AJAX CALLBACK TO BE HANDLED
SPACE_URLS::getInstance();

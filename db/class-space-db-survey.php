<?php
/*
* SURVEY MODEL
*/

class SPACE_DB_SURVEY extends SPACE_DB_BASE{

	var $page_db;
	
	function __construct(){ 
		$this->setTableSlug( 'survey' );
		parent::__construct();

		require_once('class-space-db-page.php');
		$this->setPageDB( SPACE_DB_PAGE::getInstance() );

	}
	
	//GETTER AND SETTER FUNCTIONS
	function getPageDB(){ return $this->page_db; }
	function setPageDB( $page_db ){ $this->page_db = $page_db; }
	
	function updateRequiredQuestions( $survey_id, $required_questions ){
		update_post_meta( $survey_id, 'required_questions', $required_questions );
	}
	
	function listRequiredQuestions( $survey_id = 0 ){
		$required_questions = array();
		
		if( !$survey_id ){
			global $post;
			if( $post && isset( $post->ID ) ){
				$survey_id = $post->ID;
			}
		}
		
		if( $survey_id ){
			$required_questions = get_post_meta( $survey_id, 'required_questions', true );
		}
		
		return $required_questions;
	}
	
	//RETURN THE LIST OF ASSOCIATED PAGES
	function listPages( $survey_id ){
		
		return $this->getPageDB()->listForSurvey( $survey_id );
		
	}

	// DELETE MULTIPLE PAGES BY ARRAY OF PAGE IDs
	function deletePages( $pages_id_arr ){
		return $this->getPageDB()->delete_rows( $pages_id_arr );
	}
	
	// UPDATE MULTIPLE PAGES ASSOCIATED WITH THE QUESTION
	function updatePages( $survey_id, $pages ){
		foreach( $pages as $page ){
			// CHECK IF DATA MEETS THE MINIMUM REQUIREMENT
			if( isset( $page['id'] ) && isset( $page['title'] ) && $page['title'] ){ 
			
				$page['survey_id'] = $survey_id;	
				$this->getPageDB()->updateForSurvey( $page );
				
			}
		}
	}
	
	
}

SPACE_DB_SURVEY::getInstance();
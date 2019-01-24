<?php
/*
* SURVEY MODEL
*/

class SPACE_DB_SURVEY extends SPACE_DB_BASE{

	var $page_db;
	
	var $guest_db;
	
	function __construct(){ 
		$this->setTableSlug( 'survey' );
		parent::__construct();

		require_once('class-space-db-page.php');
		$this->setPageDB( SPACE_DB_PAGE::getInstance() );
		
		require_once('class-space-db-guest.php');
		$this->setGuestDB( SPACE_DB_GUEST::getInstance() );

	}
	
	//GETTER AND SETTER FUNCTIONS
	function getPageDB(){ return $this->page_db; }
	function setPageDB( $page_db ){ $this->page_db = $page_db; }
	
	function getGuestDB(){ return $this->guest_db; }
	function setGuestDB( $guest_db ){ $this->guest_db = $guest_db; }
	
	// GET SINGLE ROW USING UNIQUE ID
	function get_row( $ID ){
		
		$survey = get_post( $ID );
		
		$survey->rules = $this->listRules( $survey->ID );
		
		$survey->required_questions = $this->listRequiredQuestions( $survey->ID );
		
		$survey->pages = $this->listPages( $survey->ID );
		
		return $survey;
		
	}
	
	function listMetaInfo( $survey_id, $meta_key ){
		$data = array();
		
		if( !$survey_id ){
			global $post;
			if( $post && isset( $post->ID ) ){
				$survey_id = $post->ID;
			}
		}
		
		if( $survey_id ){
			$data = get_post_meta( $survey_id, $meta_key, true );
		}
		
		return $data;
	}
	
	// REQUIRED QUESTIONS WITHIN THE SURVEY
	function updateRequiredQuestions( $survey_id, $required_questions ){
		update_post_meta( $survey_id, 'space_required_questions', $required_questions );
		
		
	}
	function listRequiredQuestions( $survey_id = 0 ){
		return $this->listMetaInfo( $survey_id, 'space_required_questions' );
	}
	
	// RULES WITHIN THE SURVEY: CONDITIONAL DISPLAY OF QUESTIONS
	function updateRules( $survey_id, $rules ){
		update_post_meta( $survey_id, 'space_rules', $rules );
	}
	function listRules( $survey_id = 0 ){
		return $this->listMetaInfo( $survey_id, 'space_rules' );
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
	
	//RETURN THE LIST OF ASSOCIATED GUESTS
	function totalGuests( $survey_id ){
		
		return $this->getGuestDB()->getCount( array(
			'col_formats'	=> array( 'survey_id' => '%d' ),
			'col_values'	=> array( $survey_id ),
			'operator'		=> '='
		) );
		
	}
	
}

SPACE_DB_SURVEY::getInstance();
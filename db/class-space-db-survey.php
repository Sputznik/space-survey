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

		/*REMOVE FROM PRODUCTION*/
		add_action('space_survey_drop', array($this, 'drop_table'));
	}
	
	//GETTER AND SETTER FUNCTIONS
	function getPageDB(){ return $this->page_db; }
	function setPageDB( $page_db ){ $this->page_db = $page_db; }
	
	function create(){
			
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255),
			description VARCHAR(255),
			author_id BIGINT(20),
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			modified_on DATETIME ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY(ID)
		) $charset_collate;";
		
		return $this->query( $sql );
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
	/*
	// $page SHOULD HAVE id AND title AS ATTRIBUTES
	function updatePage( $survey_id, $page ){
		
		// PREPARE THE PAGE DATA FOR UPDATION OR INSERTION
		$page['survey_id'] = $survey_id;
		$page_data = $this->getPageDB()->sanitize( $page );
		
		// CHECK IF THE DATA NEEDS TO BE UPDATED OR INSERTED
		if( $page['id'] ){
			$this->getPageDB()->update( $page['id'], $page_data );
		}
		else{
			$this->getPageDB()->insert( $page_data );
		}
	}
	*/
	function sanitize( $data ){
		$surveyData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'description'	=> sanitize_text_field( $data['desc'] ),
			'author_id'		=> get_current_user_id(),
		);
		return $surveyData;
	}

	/*AJAX CALLBACK TO DROP TABLE*/
	function drop_table(){
		$table = $this->getTable();
		$query = "DROP TABLE IF EXISTS $table";
		
		$this->query( $query );
		
		echo 'Survey Table dropped.<br/>';	
	}
}

SPACE_DB_SURVEY::getInstance();
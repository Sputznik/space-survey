<?php
/*
* SURVEY MODEL
*/

class SPACE_DB_SURVEY extends SPACE_DB_BASE{

	var $page_db;
	
	function __construct(){ 
		$this->setTableSlug( 'survey' );
		parent::__construct();

		//$this->setPageDB( SPACE_DB_PAGE::getInstance() );
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
			PRIMARY KEY(ID)
		) $charset_collate;";
		
		return $this->query( $sql );
	}
	
	//RETURN THE LIST OF ASSOCIATED PAGES
	/*function listPages( $survey_id ){
		return $this->getPageDB()->filter( 
			array(
				'survey_id'	=> '%d'
			),
			array( $survey_id )
		);
	}*/


	//IMPLEMENT FUNCTION FOR INSERT, DELETE, UPDATE PAGE INFO 


	function sanitize( $data ){
		$surveyData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'description'	=> sanitize_text_field( $data['desc'] )
		);
		return $surveyData;
	}
}


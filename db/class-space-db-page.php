<?php
/*
* PAGE MODEL ( REFERENCED IN SURVEY MODEL )
*/

class SPACE_DB_PAGE extends SPACE_DB_BASE{
	
	var $relation_db;
	
	function __construct(){ 
		$this->setTableSlug( 'page' );
		parent::__construct();
		
		require_once('class-space-db-page-question-relation.php');
		$this->setRelationDB( SPACE_DB_PAGE_QUESTION_RELATION::getInstance() );
	}
	
	function setRelationDB( $relation_db ){ $this->relation_db = $relation_db; }
	function getRelationDB(){ return $this->relation_db; }
		
	function create(){
		
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			description TEXT,
			rank INT DEFAULT 0,
			survey_id BIGINT(20) NOT NULL,
			PRIMARY KEY(ID)
		) $charset_collate;";
			
		$this->query( $sql );
		
	}
	
	function sanitize( $data ){
			
		$pageData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'description' 	=> isset( $data['description'] ) ? sanitize_text_field( $data['description'] ):'',
			'rank' 			=> isset( $data['rank'] ) ? absint( $data['rank'] ) : 0,
			'survey_id'		=> absint( $data['survey_id'] ),
		);
		return $pageData;
	}
	
	function listQuestions( $page_id ){
		
		// SELECT * FROM `wp_space_page_question_relation` r INNER JOIN `wp_space_question` q ON r.question_id = q.ID WHERE r.page_id = 1
		
		return $this->getRelationDB()->filter( 
			array(
				'page_id' => '%d'
			),
			array( (int)$page_id ),
			'rank',
			'ASC'
		);
		
	}
	
}

SPACE_DB_PAGE::getInstance();
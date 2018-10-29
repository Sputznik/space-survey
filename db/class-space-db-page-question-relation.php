<?php
/*
* PAGE MODEL ( REFERENCED IN SURVEY MODEL )
*/

class SPACE_DB_PAGE_QUESTION_RELATION extends SPACE_DB_BASE{
		
	function __construct(){ 
		$this->setTableSlug( 'page_question_relation' );
		parent::__construct();
	}
		
	function create(){
		
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			page_id BIGINT(20) NOT NULL,
			question_id BIGINT(20) NOT NULL,
			rank INT DEFAULT 0,
			PRIMARY KEY(ID)
		) $charset_collate;";
			
		$this->query( $sql );
		
	}
	
	function sanitize( $data ){
		$relationData = array(
			'rank' 				=> isset( $data['rank'] ) ? absint( $data['rank'] ) : 0,
			'page_id'			=> absint( $data['page_id'] ),
			'question_id'		=> absint( $data['question_id'] ),
		);
		return $relationData;
	}
	
}

SPACE_DB_PAGE_QUESTION_RELATION::getInstance();
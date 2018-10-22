<?php
/*
* SURVEY MODEL
*/

class SPACE_DB_SURVEY extends SPACE_DB_BASE{
	
	function __construct(){ 
		$this->setTableSlug( 'survey' );
		parent::__construct();
	}
	
	
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
	
	
	function sanitize( $data ){
		$surveyData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'description'	=> sanitize_text_field( $data['desc'] ),
		);
		return $surveyData;
	}
}


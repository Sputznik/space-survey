<?php
/*
* PAGE MODEL ( REFERENCED IN SURVEY MODEL )
*/

class SPACE_DB_PAGE extends SPACE_DB_BASE{
		
	function __construct(){ 
		$this->setTableSlug( 'page' );
		parent::__construct();
	}
		
	function create(){
		
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			survey_id BIGINT(20) NOT NULL,
			PRIMARY KEY(ID)
		) $charset_collate;";
			
		$this->query( $sql );
		
	}
	
	function sanitize( $data ){
		$pageData = array(
			'title' 	=> sanitize_text_field( $data['title'] ),
			'survey_id'	=> absint( $data['survey_id'] ),
		);
		return $pageData;
	}
}
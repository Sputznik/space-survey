<?php
/*
* CHOICE MODEL
*/

class SPACE_DB_CHOICE extends SPACE_DB_BASE{
		
	function __construct(){ 
		$this->setTableSlug( 'choice' );
		parent::__construct();
	}
		
	function create(){
		
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			description VARCHAR(255),
			question_id BIGINT(20) NOT NULL,
			PRIMARY KEY(ID)
		) $charset_collate;";
			
		$this->query( $sql );
		
	}
}
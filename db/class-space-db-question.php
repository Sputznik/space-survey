<?php
/*
* QUESTION MODEL
*/

require_once( 'class-space-db-base.php' );

class SPACE_DB_QUESTION extends SPACE_DB_BASE{
		
	function __construct(){
		$this->setTableSlug( 'question' );
		parent::__construct();
	}
		
	function create(){
			
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255),
			description VARCHAR(255),
			rank INT,
			type VARCHAR(20),
			author_id BIGINT(20),
			parent BIGINT(20),
			PRIMARY KEY(ID)
		) $charset_collate;";
			
		$this->query( $sql );
	}
}
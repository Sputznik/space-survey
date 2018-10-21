<?php
/*
* QUESTION MODEL
*/

class SPACE_DB_QUESTION extends SPACE_DB_BASE{
	
	var $choice_db;
	
	function __construct(){ 
		$this->setTableSlug( 'question' );
		parent::__construct();
		
		$this->setChoiceDB( SPACE_DB_CHOICE::getInstance() );
	}
	
	/* GETTER AND SETTER FUNCTIONS */
	function getChoiceDB(){ return $this->choice_db; }
	function setChoiceDB( $choice_db ){ $this->choice_db = $choice_db; }
	/* GETTER AND SETTER FUNCTIONS */
	
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
	
	function choices( $question_id ){
		return $this->getChoiceDB()->filter( 
			array(
				'question_id'	=> '%d'
			),
			array( $question_id )
		);
	}
}
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
			menu_rank INT DEFAULT 0,
			question_id BIGINT(20) NOT NULL,
			PRIMARY KEY(ID)
		) $charset_collate;";

		$this->query( $sql );

	}

	function sanitize( $data ){
		$choiceData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'rank' 			=> isset( $data['rank'] ) ? absint( $data['rank'] ) : 0,
			'question_id'	=> absint( $data['question_id'] ),
		);
		return $choiceData;
	}

	function alter_table(){
		$table = $this->getTable();
		$sql = "ALTER TABLE $table CHANGE `rank` `menu_rank` INT DEFAULT 0;";
		echo "Renamed rank columm in $table <br>";
		return $this->query( $sql );
	}


}

SPACE_DB_CHOICE::getInstance();

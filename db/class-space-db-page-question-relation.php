<?php
/*
* PAGE MODEL ( REFERENCED IN SURVEY MODEL )
*/

class SPACE_DB_PAGE_QUESTION_RELATION extends SPACE_DB_BASE{

	var $question_db;

	function __construct(){
		$this->setTableSlug( 'page_question_relation' );
		parent::__construct();

		require_once('class-space-db-question.php');
		$this->setQuestionDB( SPACE_DB_QUESTION::getInstance() );

	}

	function getQuestionDB(){ return $this->question_db; }
	function setQuestionDB( $question_db ){ $this->question_db = $question_db; }

	function create(){

		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			page_id BIGINT(20) NOT NULL,
			question_id BIGINT(20) NOT NULL,
			menu_rank INT DEFAULT 0,
			PRIMARY KEY(ID)
		) $charset_collate;";

		$this->query( $sql );

	}

	function sanitize( $data ){
		$relationData = array(
			'menu_rank' 	=> isset( $data['menu_rank'] ) ? absint( $data['menu_rank'] ) : 0,
			'page_id'			=> absint( $data['page_id'] ),
			'question_id'	=> absint( $data['question_id'] ),
		);
		return $relationData;
	}

	function listForPage( $page_id ){

		$relationTable = $this->getTable();
		$questionTable = $this->getQuestionDB()->getTable();

		$query = "SELECT * FROM $relationTable r INNER JOIN $questionTable q ON r.question_id = q.ID WHERE r.page_id = %d ORDER BY r.menu_rank ASC";

		$query = $this->prepare( $query, array( $page_id ) );

		$questions = $this->get_results( $query );

		// ADD CHOICES TO THE QUESTION DATA
		foreach( $questions as $question ){
			$question->choices = $this->getQuestionDB()->listChoices( $question->ID );
		}

		return $questions;

	}

	function deleteForPage( $page_id ){
		$table = $this->getTable();
		$query = $this->prepare( "DELETE FROM $table WHERE page_id = %d;", array( $page_id ) );
		$this->query( $query );
	}


	function alter_table(){
		$table = $this->getTable();
		$sql = "ALTER TABLE $table CHANGE `rank` `menu_rank` INT DEFAULT 0;";
		echo "Renamed rank columm in $table <br>";
		return $this->query( $sql );
	}

}

SPACE_DB_PAGE_QUESTION_RELATION::getInstance();

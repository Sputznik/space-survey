<?php
/*
* RESPONSE MODEL
*/

class SPACE_DB_RESPONSE extends SPACE_DB_BASE{

	function __construct(){

		$this->setTableSlug( 'response' );
		parent::__construct();

	}

	/* GETTER AND SETTER FUNCTIONS */


	/* GETTER AND SETTER FUNCTIONS */
	function create(){

		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			guest_id BIGINT(20) NOT NULL,
			question_id BIGINT(20) NOT NULL,
			choice_id BIGINT(20),
			choice_text	TEXT,
			PRIMARY KEY(ID)
		) $charset_collate;";

		return $this->query( $sql );
	}


	function sanitize( $data ){
		$responseData = array(
			'choice_text'	=> isset( $data['choice_text'] ) ? sanitize_text_field( $data['choice_text'] ) : '',
			'guest_id'		=> absint( $data['guest_id'] ),
			'question_id'	=> absint( $data['question_id'] ),
			'choice_id' 	=> isset( $data['choice_id'] ) ? absint( $data['choice_id'] ) : 0,
		);
		return $responseData;
	}

	function deleteResponsesForGuest( $guest_id ){
		$this->delete_selected_rows( array( 'guest_id'	=> '%d' ), array( $guest_id ) );
	}

	/* USED WHEN EACH SURVEY IS SELECTED */
	function getQuestionsList( $survey_id ){
		$responseTable = $this->getTable();
		$guestTable = $this->getGuestDB()->getTable();
		$questionTable = $this->getQuestionDB()->getTable();

		$data = array();

		$query = "SELECT * FROM $questionTable WHERE ID IN ( SELECT DISTINCT( question_id ) as ID FROM $responseTable WHERE guest_id
			IN ( SELECT ID FROM $guestTable WHERE survey_id = %d ) )";
		$query = $this->prepare( $query, array( $survey_id ) );

		$questions = $this->get_results( $query );
		foreach( $questions as $question ){
			$data[ $question->ID ] = $question;
		}
		return $data;
	}
	function getChoicesList( $survey_id ){
		$responseTable = $this->getResponseDB()->getTable();
		$guestTable = $this->getGuestDB()->getTable();
		$choiceTable = $this->getChoiceDB()->getTable();

		$data = array();

		$query = "SELECT * FROM $choiceTable WHERE question_id IN ( SELECT DISTINCT( question_id ) as ID FROM $responseTable WHERE guest_id
			IN ( SELECT ID FROM $guestTable WHERE survey_id = %d )  )";
		$query = $this->prepare( $query, array( $survey_id ) );

		$choices = $this->get_results( $query );
		foreach( $choices as $choice ){
			$data[ $choice->ID ] = $choice;
		}
		return $data;
	}
	/* USED WHEN EACH SURVEY IS SELECTED */
}

SPACE_DB_RESPONSE::getInstance();

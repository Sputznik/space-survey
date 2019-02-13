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
	
	
	

}

SPACE_DB_RESPONSE::getInstance();
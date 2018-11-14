<?php
/*
* GUEST MODEL
*/

class SPACE_DB_GUEST extends SPACE_DB_BASE{
	
	var $response_db;
	
	function __construct(){ 
		
		$this->setTableSlug( 'guest' );
		parent::__construct();
		
		require_once( 'class-space-db-response.php' );
		$this->setResponseDB( SPACE_DB_RESPONSE::getInstance() );
		
	}
	
	/* GETTER AND SETTER FUNCTIONS */
	function getResponseDB(){ return $this->response_db; }
	function setResponseDB( $response_db ){ $this->response_db = $response_db; }
	/* GETTER AND SETTER FUNCTIONS */
	
	function create(){
			
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();
			
		$sql = "CREATE TABLE IF NOT EXISTS $table ( 
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			ipaddress VARCHAR(30),
			meta VARCHAR(255),
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			survey_id BIGINT(20),
			PRIMARY KEY(ID)
		) $charset_collate;";
		
		return $this->query( $sql );
	}
	
	function sanitize( $data ){
		$guestData = array(
			'ipaddress' 	=> $this->getClientIP(),
			'meta'			=> $_SERVER['HTTP_USER_AGENT'],
			'survey_id'		=> absint( $data['survey_id'] ),
		);
		return $guestData;
	}
	
	// RETURNS THE LIST OF ASSOCIATED RESPONSES
	function getResponses( $guest_id ){

		return $this->getResponseDB()->filter( 
			array(
				'guest_id'	=> '%d'
			),
			array( (int) $guest_id )
		);
	}
	
	function deleteResponses( $guest_id ){
		$this->getResponseDB()->deleteResponsesForGuest( $guest_id );
	}
	
	function saveResponses( $data ){
		
		$responses = array();
		
		// CHECK IF GUEST ID AND QUESTION WITH RESPONSES HAS BEEN PASSED
		if( isset( $data['guest_id'] ) && isset( $data['quest'] ) && is_array( $data['quest'] ) ){
			
			foreach( $data['quest'] as $quest_id => $quest ){
				
				if( is_array( $quest ) && isset( $quest['type'] ) && isset( $quest['val'] ) ){
								
					switch( $quest['type'] ){
						
						case 'dropdown':
						
						case 'radio':
										
							$partialResponse = $this->getResponseDB()->sanitize( array(
								'question_id'	=> $quest_id,
								'guest_id'		=> $data['guest_id'],
								'choice_id'		=> $quest['val']
							) );
										
							array_push( $responses, $partialResponse );
										
						break;
										
						case 'checkbox':
							if( is_array( $quest['val'] ) ){
											
								foreach( $quest['val'] as $choice_id ){
									
									$partialResponse = $this->getResponseDB()->sanitize( array(
										'question_id'	=> $quest_id,
										'guest_id'		=> $data['guest_id'],
										'choice_id'		=> $choice_id
									) );
									
									array_push( $responses, $partialResponse );
								}
							}
							break;
						
						case 'text':
							$partialResponse = $this->getResponseDB()->sanitize( array(
								'question_id'	=> $quest_id,
								'guest_id'		=> $data['guest_id'],
								'choice_text'	=> $quest['val']
							) );
										
							array_push( $responses, $partialResponse );
							
							break;
						
						
							
					}
								
				}
							
			}
			
			// DELETE ALL RESPONSES FOR THE PARTICULAR GUEST
			$this->deleteResponses( $data['guest_id'] );
			
			// INSERT MULTIPLE RESPONSES FOR THE GUEST AT ONCE USING SINGLE QUERY
			$this->getResponseDB()->insert_rows( $responses );
			
		}
					
	}
	
	// GET CLIENT IP ADDRESS
	function getClientIP() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	

}

SPACE_DB_GUEST::getInstance();
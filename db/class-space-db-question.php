<?php
/*
* QUESTION MODEL
*/

class SPACE_DB_QUESTION extends SPACE_DB_BASE{
	
	var $choice_db;
	var $types;
	
	function __construct(){ 
		$this->setTypes( array(
			'single'	=> 'Radio Button',
			'multiple'	=> 'Checkboxes'
		) );
		$this->setTableSlug( 'question' );
		parent::__construct();
		
		add_action( 'wp_ajax_space_questions', array( $this, 'ajaxQuestions' ) );
		
		require_once( 'class-space-db-choice.php' );
		$this->setChoiceDB( SPACE_DB_CHOICE::getInstance() );

	}
	
	/* GETTER AND SETTER FUNCTIONS */
	function setTypes( $types ){ $this->types = $types; }
	function getTypes(){ return $this->types; }
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
			rank INT DEFAULT 0,
			type VARCHAR(20),
			author_id BIGINT(20),
			parent BIGINT(20),
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			modified_on DATETIME NOT NULL,
			PRIMARY KEY(ID)
		) $charset_collate;";
		
		return $this->query( $sql );
	}
	
	// RETURNS THE LIST OF ASSOCIATED CHOICES
	function listChoices( $question_id ){

		return $this->getChoiceDB()->filter( 
			array(
				'question_id'	=> '%d'
			),
			array( (int)$question_id ),
			'rank',
			'ASC'
		);
	}
	
	// DELETE MULTIPLE CHOICES BY ARRAY OF CHOICE IDs
	function deleteChoices( $choices_id_arr ){
		return $this->getChoiceDB()->delete_rows( $choices_id_arr );
	}
	
	// UPDATE MULTIPLE CHOICES ASSOCIATED WITH THE QUESTION
	function updateChoices( $question_id, $choices ){
		foreach( $choices as $choice ){
			// CHECK IF DATA MEETS THE MINIMUM REQUIREMENT
			if( isset( $choice['id'] ) && isset( $choice['title'] ) && $choice['title'] ){ 
				$this->updateChoice( $question_id, $choice );
			}
		}
	}
	
	// $choice SHOULD HAVE id AND title AS ATTRIBUTES
	function updateChoice( $question_id, $choice ){
		
		// PREPARE THE CHOICE DATA FOR UPDATION OR INSERTION
		$choice['question_id'] = $question_id;
		$choice_data = $this->getChoiceDB()->sanitize( $choice );
		
		// CHECK IF THE DATA NEEDS TO BE UPDATED OR INSERTED
		if( $choice['id'] ){
			$this->getChoiceDB()->update( $choice['id'], $choice_data );
		}
		else{
			$this->getChoiceDB()->insert( $choice_data );
		}
	}
	
	function sanitize( $data ){
		$questionData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'description'	=> sanitize_text_field( $data['desc'] ),
			'rank' 			=> absint( $data['rank'] ),
			'type' 			=> $data['type'],
			'author_id'		=> get_current_user_id(),
			'parent' 		=> absint( $data['parent'] ),
			'modified_on'	=> current_time('mysql', false)
		);
		return $questionData;
	}
	
	/*
	* USED IN class-space-question-list-table.php AND WITHIN THE SAME CLASS
	* INPUT: CURRENT PAGE, PER PAGE ITEMS, SEARCH TERM
	* OUTPUT: GETS A LIST OF QUESTION
	*/
	function listQuestions( $page, $per_page, $search_term = '' ){
		
		$search_term = '%'.$this->esc_like( $search_term ).'%';
		
		return $this->results( 
			$page, 		// CURRENT PAGE NUMBER
			$per_page, 	// POSTS PER PAGE
			array(		// SEARCH ARRAY
				'col_formats' 	=> array( 'title' => '%s' ), 
				'col_values'	=> array( $search_term ),
				'operator'		=> 'LIKE'
			) 
		);
		
	}
	
	function ajaxQuestions(){
		
		$data = $this->listQuestions( 1, 10, $_GET[ 'term' ] );
		
		$final_data = array();
		foreach( $data['results'] as $result ){
			$temp = array(
				'id'	=> $result->ID,
				'label'	=> $result->title,
				'value'	=> $result->title
			);
			array_push( $final_data, $temp );
		}
		
		print_r( wp_json_encode( $final_data ) );
		
		wp_die();
	}

	/*AJAX CALLBACK TO DROP TABLE*/
	function drop_table(){
		$table = $this->getTable();
		$query = "DROP TABLE IF EXISTS $table";
		
		$this->query( $query );
		
		echo 'Survey Question Table dropped.<br/>';	
	}

}

SPACE_DB_QUESTION::getInstance();
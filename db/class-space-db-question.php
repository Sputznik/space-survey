<?php
/*
* QUESTION MODEL
*/

class SPACE_DB_QUESTION extends SPACE_DB_BASE{

	var $choice_db;
	var $types;

	function __construct(){
		$this->setTypes( array(
			'radio'						=> 'Radio Button',
			'checkbox'				=> 'Checkboxes',
			//'checkbox-other'	=> 'Checkboxes With Other',
			'checkbox-ranking'	=> 'Checkboxes With Ranking',
			'dropdown'				=> 'Dropdown',
			'text'						=> 'Textbox'
		) );
		$this->setTableSlug( 'question' );
		parent::__construct();

		add_action( 'wp_ajax_space_questions', array( $this, 'ajaxQuestions' ) );

		add_action( 'wp_ajax_space_import_choices_csv', array( $this, 'importChoicesCSV' ) );

		require_once( 'class-space-db-choice.php' );
		$this->setChoiceDB( SPACE_DB_CHOICE::getInstance() );

	}

	/* GETTER AND SETTER FUNCTIONS */
	function setTypes( $types ){ $this->types = $types; }
	function getTypes(){ return $this->types; }
	function getChoiceDB(){ return $this->choice_db; }
	function setChoiceDB( $choice_db ){ $this->choice_db = $choice_db; }
	/* GETTER AND SETTER FUNCTIONS */

	function alter_table(){
		$table = $this->getTable();
		$sql = "ALTER TABLE $table ADD `meta` TEXT AFTER `parent`;";
		echo "Added meta columm in $table <br>";
		return $this->query( $sql );
	}

	function create(){

		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255),
			description VARCHAR(255),
			type VARCHAR(20),
			author_id BIGINT(20),
			parent BIGINT(20),
			meta TEXT,
			created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
			modified_on DATETIME ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY(ID)
		) $charset_collate;";

		// ALTER TABLE $table ADD meta VARCHAR(255)

		return $this->query( $sql );
	}

	// RETURNS THE LIST OF ASSOCIATED CHOICES
	function listChoices( $question_id ){

		return $this->getChoiceDB()->filter(
			array(
				'question_id'	=> '%d'
			),
			array( (int)$question_id ),
			'menu_rank',
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
			if( isset( $choice['id'] ) && isset( $choice['title'] ) && strlen( $choice['title'] ) ){
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
			'title' 			=> sanitize_text_field( $data['title'] ),
			'description'	=> sanitize_text_field( $data['desc'] ),
			'type' 				=> $data['type'],
			'author_id'		=> get_current_user_id(),
			'parent' 			=> isset( $data['parent'] ) ? absint( $data['parent'] ) : 0,
			'modified_on'	=> current_time('mysql', false),
			'meta'				=> array()
		);

		// INCLUDE META INFORMATION
		$metaFields = array( 'defaultDropdownOption', 'limitFlag', 'limit', 'limitError', 'otherFlag', 'otherText', 'nullFlag' );
		foreach( $metaFields as $metaField ){
			if( isset( $data[ $metaField ] ) ){
				$questionData['meta'][ $metaField ] = $data[ $metaField ];
			}
		}
		$questionData['meta'] = serialize( $questionData['meta'] );


		//echo "<pre>";
		//print_r( $questionData );
		//print_r( wp_unslash( $questionData ) );
		//echo "</pre>";
		
		return $questionData;
	}

	// RETURNS AN UNSERIALIZED VERSION OF THE FIELD
	function getMetaInfo( $row ){
		$meta_info = array();
		if( isset( $row->meta ) && $row->meta ){
			$meta_info = unserialize( $row->meta );
		}
		return $meta_info;
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

	// USED FOR FILTERING CHOICES IN THE SPACE RESPONSES
	function getQuestionFromChoice( $choice_id ){
		$table = $this->getTable();
		$choiceTable = $this->getChoiceDB()->getTable();

		$query = "SELECT * FROM $table WHERE ID IN ( SELECT question_id FROM $choiceTable WHERE ID = %d )";
		$query = $this->prepare( $query, array( $choice_id ) );

		$questions = $this->get_results( $query );
		$question = $questions[0];
		$question->choices = array();

		$choices = $this->listChoices( $question->ID );
		foreach( $choices as $choice ){
			$question->choices[ $choice->ID ] = $choice->title;
		}

		return $question;
	}

	function ajaxQuestions(){

		$data = $this->listQuestions( 1, 10, $_GET[ 'term' ] );

		$final_data = array();
		foreach( $data['results'] as $result ){
			$temp = array(
				'id'		=> $result->ID,
				'label'		=> $result->title,
				'value'		=> $result->title,
				'choices'	=> array()
			);

			$choices = $this->listChoices( $result->ID );
			foreach( $choices as $choice ){
				$temp['choices'][$choice->ID] = $choice->title;
			}

			array_push( $final_data, wp_unslash( $temp ) );
		}

		print_r( wp_json_encode( $final_data ) );

		wp_die();
	}

	function listSurveys( $question_id ){
		global $wpdb;
		$relationTable = $this->getPageQuestionRelationDB()->getTable();
		$pageTable = $this->getPageDB()->getTable();
		$query = "SELECT * FROM $wpdb->posts WHERE ID IN ( SELECT survey_id from $pageTable WHERE ID IN (SELECT page_id FROM $relationTable WHERE question_id = %d ) )";
		$query = $this->prepare( $query, array( $question_id ) );
		return $this->get_results( $query );
	}

	function importChoicesCSV(){
		/* UPDATE THE CHOICES DATA FROM THE CSV */
		if( isset( $_FILES['file'] ) && $_FILES['file'] ){

			$csv = SPACE_CSV::getInstance();

			// UPLOAD THE CSV FILE
			$movefile = $csv->upload( $_FILES['file'] );

			// // CHECK IF UPLOAD PROCESS WAS COMPLETED WITHOUT ANY ERROR
			if ( $movefile && !isset( $movefile['error'] ) ) {

				// CONVERT THE UPLOADED FILE TO ARRAY FORMAT
				$arrayCsv = $csv->convertToArray( $movefile['file'] );

				$data = array();

				foreach ( $arrayCsv as $row ) {
					if( is_array( $row ) && count( $row ) ){
						array_push( $data, $row[ 0 ] );
					}
				}

				echo wp_json_encode( $data );

				//SPACE_UTIL::getInstance()->test( $arrayCsv );

			}
		}
		wp_die();
	}

}

SPACE_DB_QUESTION::getInstance();

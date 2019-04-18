<?php
/*
* SURVEY MODEL
*/

class SPACE_DB_SURVEY extends SPACE_DB_BASE{

	var $page_db;

	var $guest_db;

	var $question_db;

	function __construct(){
		$this->setTableSlug( 'survey' );
		parent::__construct();

		require_once('class-space-db-page.php');
		$this->setPageDB( SPACE_DB_PAGE::getInstance() );

		require_once('class-space-db-guest.php');
		$this->setGuestDB( SPACE_DB_GUEST::getInstance() );

		require_once('class-space-db-question.php');
		$this->setQuestionDB( SPACE_DB_QUESTION::getInstance() );

		add_action( 'save_post', array( $this, 'saveSurvey' ) );

	}

	//GETTER AND SETTER FUNCTIONS
	function getPageDB(){ return $this->page_db; }
	function setPageDB( $page_db ){ $this->page_db = $page_db; }

	function getQuestionDB(){ return $this->question_db; }
	function setQuestionDB( $question_db ){ $this->question_db = $question_db; }

	function getGuestDB(){ return $this->guest_db; }
	function setGuestDB( $guest_db ){ $this->guest_db = $guest_db; }

	// GET SINGLE ROW USING UNIQUE ID
	function get_row( $ID ){

		$survey = get_post( $ID );

		if( $survey ){

			$survey->rules = $this->listRules( $survey->ID );

			$survey->required_questions = $this->listRequiredQuestions( $survey->ID );

			$survey->pages = $this->listPages( $survey->ID );

		}
		return $survey;

	}

	function listMetaInfo( $survey_id, $meta_key ){
		$data = array();

		if( !$survey_id ){
			global $post;
			if( $post && isset( $post->ID ) ){
				$survey_id = $post->ID;
			}
		}

		if( $survey_id ){
			$data = get_post_meta( $survey_id, $meta_key, true );
		}

		return $data;
	}

	// REQUIRED QUESTIONS WITHIN THE SURVEY
	function updateRequiredQuestions( $survey_id, $required_questions ){
		update_post_meta( $survey_id, 'space_required_questions', $required_questions );


	}
	function listRequiredQuestions( $survey_id = 0 ){
		return $this->listMetaInfo( $survey_id, 'space_required_questions' );
	}

	// RULES WITHIN THE SURVEY: CONDITIONAL DISPLAY OF QUESTIONS
	function updateRules( $survey_id, $rules ){
		update_post_meta( $survey_id, 'space_rules', $rules );
	}
	function listRules( $survey_id = 0 ){
		return $this->listMetaInfo( $survey_id, 'space_rules' );
	}

	//RETURN THE LIST OF ASSOCIATED PAGES
	function listPages( $survey_id ){

		return $this->getPageDB()->listForSurvey( $survey_id );

	}

	// DELETE MULTIPLE PAGES BY ARRAY OF PAGE IDs
	function deletePages( $pages_id_arr ){
		return $this->getPageDB()->delete_rows( $pages_id_arr );
	}

	// UPDATE MULTIPLE PAGES ASSOCIATED WITH THE QUESTION
	function updatePages( $survey_id, $pages ){
		foreach( $pages as $page ){
			// CHECK IF DATA MEETS THE MINIMUM REQUIREMENT
			if( isset( $page['id'] ) && isset( $page['title'] ) && $page['title'] ){

				$page['survey_id'] = $survey_id;
				$this->getPageDB()->updateForSurvey( $page );

			}
		}
	}

	//RETURN THE TOTAL COUNT OF ASSOCIATED GUESTS
	function totalGuests( $survey_id ){

		return $this->getGuestDB()->getCount( array(
			'col_formats'	=> array( 'survey_id' => '%d' ),
			'col_values'	=> array( $survey_id ),
			'operator'		=> '='
		) );

	}

	// RETURNS THE LIST OF ASSOCIATED GUESTS
	function getGuests( $survey_id, $page = 0, $per_page = 20 ){

		return $this->getGuestDB()->results(
			$page,
			$per_page,
			array(
				'col_formats' 	=>  array( 'survey_id'	=> '%d' ),
				'col_values'	=> 	array( (int) $survey_id ),
				'operator'		=> '='
			)
		);
	}

	// RETURNS THE LIST OF GUEST IDs IN A PAGINATED FORMAT
	function listGuestIDs( $survey_id, $choices, $page = 1, $per_page = 20 ){

		return $this->getGuestDB()->listIDsForSurvey( $survey_id, $choices, $page, $per_page );


	}

	function getChoicesList( $survey_id ){

		$data = array();

		$choiceTable 	= $this->getQuestionDB()->getChoiceDB()->getTable();
		$pageTable		= $this->getPageDB()->getTable();
		$relationTable 	= $this->getPageDB()->getRelationDB()->getTable();

		$query = "SELECT * FROM $choiceTable WHERE question_id IN ( SELECT question_id FROM $relationTable WHERE page_id IN ( SELECT ID from $pageTable WHERE survey_id = %d ) )";

		$query = $this->prepare( $query, array( $survey_id ) );

		$choices = $this->get_results( $query );

		foreach( $choices as $choice ){
			$data[ $choice->ID ] = $choice;
		}

		return $data;
	}

	function getQuestionsList( $survey_id ){

		$data = array();

		$questionTable 	= $this->getQuestionDB()->getTable();
		$pageTable		= $this->getPageDB()->getTable();
		$relationTable 	= $this->getPageDB()->getRelationDB()->getTable();

		$query = "SELECT * FROM $questionTable WHERE ID IN ( SELECT question_id FROM $relationTable WHERE page_id IN ( SELECT ID from $pageTable WHERE survey_id = %d ) )";

		$query = $this->prepare( $query, array( $survey_id ) );

		$questions = $this->get_results( $query );

		foreach( $questions as $question ){
			$data[ $question->ID ] = $question;
		}

		return $data;
	}

	function _from_query(){
		global $wpdb;
		$table = $wpdb->posts;
		return " FROM $table";
	}




	function saveSurvey( $survey_id ){

		if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'space_survey' ){

			/*	
			print_r( $survey_id );
			echo "<pre>";
			print_r( $_POST );
			echo "</pre>";
			wp_die();
			*/

			if( $survey_id && isset( $_POST[ 'pages' ] ) ){
				// UPDATE OR ADD NEW PAGE
				$this->updatePages( $survey_id, $_POST[ 'pages' ] );
			}

			if( $survey_id && isset( $_POST['pages_delete'] ) && $_POST['pages_delete'] ){
				// $_POST['pages_delete'] HAS A COMMA SEPERATED STRING OF PAGE IDs THAT ARE NO LONGER NEEDED
				$this->deletePages( explode(',', $_POST['pages_delete'] ) );
			}


			// FIND THE REQUIRED QUESTIONS
			$required_questions = array();
			$rules = array();
			if( isset( $_POST['pages'] ) && is_array( $_POST['pages'] ) ){
				foreach( $_POST['pages'] as $page ){
					if( isset( $page['questions'] ) && is_array( $page['questions'] ) ){
						foreach( $page['questions'] as $question ){
							if( isset( $question['required'] ) && isset( $question['id'] ) ){
								array_push( $required_questions, $question['id'] );
							}
							elseif( isset( $question['id'] ) && isset( $question['rules']) && is_array( $question['rules'] ) ){
								// OBVIOUSLY ASSUMING THAT A QUESTION WILL BE ADDED ONLY ONCE IN A SURVEY
								$rules[ $question['id'] ] = $question['rules'];
							}
						}
					}
				}
			}
			$this->updateRequiredQuestions( $survey_id, $required_questions );
			$this->updateRules( $survey_id, $rules );

		}

	}

}

SPACE_DB_SURVEY::getInstance();

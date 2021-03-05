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

		add_action( 'wp_ajax_surveys_json', array( $this, 'json' ) );

		add_action( 'wp_ajax_space_survey_settings_json', array( $this, 'settingsJson' ) );

		add_action( 'wp_ajax_space_survey_import_json', array( $this, 'importJson' ) );

	}

	function settingsJson(){

		$data = array();

		$survey_id = isset( $_GET['survey_id'] ) ? $_GET['survey_id'] : 0;

		if( $survey_id ){

			$unset_question_fields = array(
				'ID', 'question_id', 'choices', 'page_id', 'title', 'description', 'type', 'author_id', 'parent', 'meta' ,
				'created_on', 'modified_on'
			);

			$data['pages'] = array();
			$pages = $this->listPages( $survey_id );
			foreach ( $pages as $page ) {
				unset( $page->ID );
				unset( $page->survey_id );
				$page->id = 0;


				foreach( $page->questions as $question ){
					$question->id = $question->question_id;

					foreach( $unset_question_fields as $field ){
						unset( $question->$field );
					}
				}
				array_push( $data['pages'], wp_unslash( $page ) );
			}


			$data['required_questions'] = $this->listRequiredQuestions( $survey_id );
			$data['rules'] = $this->listRules( $survey_id );

			//echo "<pre>";
			//print_r( $data );
			//echo "</pre>";
		}

		wp_send_json( $data );

		//wp_die();
	}

	function json(){
		global $wpdb;
		$search = $_GET['term'];
		$query = "SELECT ID, post_title FROM ".$wpdb->posts." WHERE post_title LIKE '%".$search."%' AND post_type='space_survey' AND post_status='publish' ORDER BY post_title ASC LIMIT 0,10";
		$posts = array();
		foreach($wpdb->get_results($query) as $row){
			array_push( $posts, array( 'id' => $row->ID, 'value'=> $row->post_title ) );
		}
		wp_send_json( $posts );
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

	// SURVEY SETTINGS
	function updateSettings( $survey_id, $settings ){
		if( is_array( $settings ) ){
			update_post_meta( $survey_id, 'survey_settings', $settings );
		}
	}

	//RETURN THE LIST OF ASSOCIATED PAGES
	function listPages( $survey_id ){ return $this->getPageDB()->listForSurvey( $survey_id ); }

	// DELETE MULTIPLE PAGES BY ARRAY OF PAGE IDs
	function deletePages( $pages_id_arr ){
		if( !is_user_logged_in() ){ return false; }
		return $this->getPageDB()->delete_rows( $pages_id_arr );
	}

	// UPDATE MULTIPLE PAGES ASSOCIATED WITH THE QUESTION
	function updatePages( $survey_id, $pages ){
		if( is_array( $pages ) && count( $pages ) ){
			foreach( $pages as $page ){
				// CHECK IF DATA MEETS THE MINIMUM REQUIREMENT
				if( isset( $page['id'] ) && isset( $page['title'] ) && $page['title'] ){
					$page['survey_id'] = $survey_id;
					$this->getPageDB()->updateForSurvey( $page );
				}
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

	/* RETURNS THE LIST OF GUEST IDs IN A PAGINATED FORMAT
	function listGuestIDs( $survey_id, $choices, $page = 1, $per_page = 20 ){
		return $this->getGuestDB()->listIDsForSurvey( $survey_id, $choices, $page, $per_page );
	}
	*/

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

	// COMMON REPO FOR GATHERING QUERIES FOR QUESTIONS PER SURVEY
	function getQuestionsQuery( $survey_id, $search_term = '', $page = 1, $per_page = 10 ){

		$queries = array();

		$search_term = '%'.$this->esc_like( $search_term ).'%';

		$params = array( $search_term );

		$questionTable 	= $this->getQuestionDB()->getTable();
		$pageTable		= $this->getPageDB()->getTable();
		$relationTable 	= $this->getPageDB()->getRelationDB()->getTable();

		$queries['common'] = " FROM $questionTable WHERE title LIKE %s";

		if( $survey_id ){
			$queries['filter_by_survey'] = " AND ID IN (
				SELECT question_id FROM $relationTable WHERE page_id IN ( SELECT ID from $pageTable WHERE survey_id = %d )
			)";
			$queries['common'] .= $queries['filter_by_survey'];
			array_push( $params, $survey_id );
		}

		$queries['results'] = $this->prepare( "SELECT *". $queries['common'], $params );
		if( $page && $per_page ){
			$queries['results'] .= $this->_limit_query( $page, $per_page );
		}
		$queries['count'] = $this->prepare( "SELECT COUNT(*)". $queries['common'], $params );

		return $queries;
	}

	// COMMON REPO FOR GATHERING QUERIES FOR RESPONSES PER SURVEY
	function getResponsesQuery( $survey_id, $filterChoices, $hide_zero_attempted, $search_term = '', $page = 1, $per_page = 10 ){

		$queries = array();

		$search_term = '%'.$this->esc_like( $search_term ).'%';

		$params = array( $search_term );

		$guestTable 	= $this->getGuestDB()->getTable();
		$responsesTable = $this->getGuestDB()->getResponseDB()->getTable();

		$queries['common'] = " FROM $guestTable WHERE meta LIKE %s";

		if( $survey_id ){
			$queries['filter_by_survey'] = " AND survey_id = %d";
			$queries['common'] .= $queries['filter_by_survey'];
			array_push( $params, $survey_id );

			if( $hide_zero_attempted ){
				$queries['filter_by_nonzero_attempts'] = " AND ID IN (
					SELECT guest_id FROM $responsesTable WHERE guest_id IN (
						SELECT ID FROM $guestTable WHERE survey_id = %d
					) )";
				$queries['common'] .= $queries['filter_by_nonzero_attempts'];
				array_push( $params, $survey_id );
			}
		}



		//print_r( $filterChoices );

		if( is_array( $filterChoices ) && count( $filterChoices ) ){
			$queries['filter_by_choices'] = $this->getGuestDB()->getNestedQueryForChoices( $filterChoices );
			//echo $queries['filter_by_choices'];
			$queries['common'] .= " AND ID IN (" . $queries['filter_by_choices'] . ")";
		}

		$queries['common'] .= " ORDER BY ID DESC";

		//print_r( "SELECT *". $queries['common'] );

		$queries['results'] = $this->prepare( "SELECT *". $queries['common'], $params );
		if( $page && $per_page ){
			$queries['results'] .= $this->_limit_query( $page, $per_page );
		}
		$queries['count'] = $this->prepare( "SELECT COUNT(*)". $queries['common'], $params );

		//echo "<pre>";
		//print_r( $queries );
		// echo "</pre>";

		return $queries;
	}

	function getQuestionsList( $survey_id ){

		$data = array();

		/*
		* PARAMS: SURVEY_ID, SEARCH_TERM, PAGE, ITEMS_PER_PAGE
		* PASS PAGE AND ITEMS_PER_PAGE AS 0 TO REMOVE THE LIMIT QUERY
		*/
		$queries = $this->getQuestionsQuery( $survey_id, '', 0, 0 );

		$questions = $this->get_results( $queries['results'] );

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


			//print_r( $survey_id );
			//echo "<pre>";
			//print_r( $_POST );
			//echo "</pre>";
			//wp_die();


			//echo "<pre>";
			//print_r( $_FILES );
			//echo "</pre>";

			// CHECK FILE NEEDS TO BE IMPORTED
			if( $survey_id && isset( $_FILES['survey-file'] ) && $_FILES['survey-file'] && $_FILES['survey-file']['error'] == 0 ){
				/*

				MOVED TO AJAX

				$survey_json_str = file_get_contents( $_FILES['survey-file']['tmp_name'] );

				// Convert JSON string to Array
  			$survey_json = json_decode( $survey_json_str, true );

				if( isset( $survey_json[ 'pages' ] ) ){
					$this->updatePages( $survey_id, wp_unslash( $survey_json[ 'pages' ] ) );
				}

				if( isset( $survey_json[ 'required_questions' ] ) ){
					$this->updateRequiredQuestions( $survey_id, $survey_json[ 'required_questions' ] );
				}

				if( isset( $survey_json[ 'rules' ] ) ){
					$this->updateRules( $survey_id, $survey_json[ 'rules' ] );
				}
				*/
			}
			else{
				// NORMAL UPDATION OF THE POST

				// UPDATE OR ADD NEW PAGE
				if( $survey_id && isset( $_POST[ 'pages' ] ) ){
					$this->updatePages( $survey_id, $_POST[ 'pages' ] );
				}

				// $_POST['pages_delete'] HAS A COMMA SEPERATED STRING OF PAGE IDs THAT ARE NO LONGER NEEDED
				if( $survey_id && isset( $_POST['pages_delete'] ) && $_POST['pages_delete'] ){
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
								if( isset( $question['id'] ) && isset( $question['rules']) && is_array( $question['rules'] ) ){
									// OBVIOUSLY ASSUMING THAT A QUESTION WILL BE ADDED ONLY ONCE IN A SURVEY
									$rules[ $question['id'] ] = $question['rules'];
								}
							}
						}
					}
				}
				$this->updateRequiredQuestions( $survey_id, $required_questions );
				$this->updateRules( $survey_id, $rules );

				// SAVE ADDITIONAL SETTINGS FROM THE meta_boxes
				if( $survey_id && isset( $_POST[ 'survey_settings' ] ) ){
					$this->updateSettings( $survey_id, $_POST[ 'survey_settings' ] );
				}

			}

			//wp_die();
		}

	}

	function importJson(){

		$data = array();

		if( isset( $_GET['survey_id'] ) && isset( $_FILES['file'] ) && $_FILES['file'] ){

			$survey_id = $_GET['survey_id'];

			$survey_json_str = file_get_contents( $_FILES['file']['tmp_name'] );

			// Convert JSON string to Array
			$survey_json = json_decode( $survey_json_str, true );

			// ONLY NEEDED FOR DEBUGGING
			$data = array( 'id' => $survey_id, 'data' => $survey_json );

			if( isset( $survey_json[ 'pages' ] ) ){
				$this->updatePages( $survey_id, wp_unslash( $survey_json[ 'pages' ] ) );
			}

			if( isset( $survey_json[ 'required_questions' ] ) ){
				$this->updateRequiredQuestions( $survey_id, $survey_json[ 'required_questions' ] );
			}

			if( isset( $survey_json[ 'rules' ] ) ){
				$this->updateRules( $survey_id, $survey_json[ 'rules' ] );
			}

		}

		return wp_send_json( $data );
	}

}

SPACE_DB_SURVEY::getInstance();

<?php
/*
* EXPORT HELPER CLASS
*/

class SPACE_EXPORT extends SPACE_BASE{

	function __construct(){

		/* SAMPLE ACTION HOOK FOR AJAX CALL */
		add_action('space_batch_action_export', function(){

			// GET PARAMETERS
			$step 					= $_GET['space_batch_step'];
			$batches 				= $_GET['space_batches'];
			$survey_id 			= $_GET['survey_id'];
			$per_page				= $_GET['per_page'];
			$filterChoices 	= isset( $_GET['filterChoices'] ) && $_GET['filterChoices'] ? explode( ',', $_GET['filterChoices'] ) : array();
			$hide_zero_attempted 	= isset( $_GET['hide-zero-attempted'] ) && $_GET['hide-zero-attempted'] == '1' ? true : false;

			$file_slug 			= 'space_survey' . $survey_id;

			if( ! $survey_id ){
				echo "Invalid Survey Information";
				wp_die();
			}

			/*
			* DATABASE OPERATIONS -
			* GET THE LIST OF QUESTIONS IN THE SURVEY
			* GET THE MAP OF CHOICES IN THE SURVEY
			* GET PAGINATED GUESTS - ONLY THE ID
			*/
			$survey_db = SPACE_DB_SURVEY::getInstance();
			$response_db = SPACE_DB_RESPONSE::getInstance();

			$questions = wp_unslash( $response_db->getQuestionsList( $survey_id ) );
			$choices = wp_unslash( $response_db->getChoicesList( $survey_id ) );

			/*
			echo "<pre>";
			print_r( $questions );
			print_r( $choices );
			echo "</pre>";
			wp_die();
			*/

			//$questions = $survey_db->getQuestionsList( $survey_id );
			//$choices = $survey_db->getChoicesList( $survey_id );

			$queries = $survey_db->getResponsesQuery( $survey_id, $filterChoices, $hide_zero_attempted, '', $step, $per_page );
			$guests = $survey_db->get_results( $queries['results'] );

			/*
			echo "<pre>";
			print_r( $queries );
			print_r( $guests );
			echo "</pre>";
			wp_die();
			*/

			// ADD HEADER ROW FOR THE FIRST BATCH REQUEST ONLY
			if( $step == 1 ){
				echo "<p>Header Row has been added in the CSV file</p>";
				$this->addQuestionsAsHeader( $file_slug, $questions );
			}

			/*
			* ITERATE THROUGH EACH GUEST IN THE RESULT
			* AND APPEND THEM AS ROW IN THE CSV FILE
			*/
			if( count( $guests ) ){
				$question_ids = $this->getListQuestionIDs( $questions );

				foreach( $guests as $guest ){

					$cache_key = 'space_guests_' . $guest->ID;
					$responses = get_transient( $cache_key );
					if ( $responses === false ) {
						$guestResponses = $survey_db->getGuestDB()->getResponses( $guest->ID );
						$responses = $this->getFormattedResponses( $guestResponses, $questions, $choices );
						set_transient( $cache_key, $responses, 3600 * 1 );
					}

					$this->addGuestResponses( $file_slug, $guest, $responses, $question_ids );
				}
				$num_guests = count( $guests );
				echo "<p>$num_guests guest responses have been added to the CSV file</p>";

				/*
				echo "<pre>";
				print_r( $guests );
				echo "</pre>";
				wp_die();
				*/

			}


			/*
			* IN THE LAST ITERATION APPEND THE DOWNLOAD LINK
			*/
			if( $step == $batches ){
				$fileURL = $this->getFilePath( $file_slug )['url'] . '?v=' . rand(0, 100);
				echo "<p>File has been exported successfully. <a target='_blank' href='$fileURL'>Download here.</a></p>";
			}

		});

	}

	// RETURNING THE FILE PATH WHICH EXISTS IN THE WP UPLOADS DIRECTORY
	function getFilePath( $file_slug ){
		$file = "$file_slug.csv";
		$filePath = array();
		$path = wp_upload_dir();
		$filePath['path'] 	= $path['path'] . "/$file";
		$filePath['url'] 	= $path['url'] . "/$file";
		return $filePath;
	}



	// POSSIBLY A NEW FILE WHERE HEADER IS THE FIRST ROW OF DATA IN THE FILE
	function addHeaderToCSV( $file_slug, $header ){
		$path = $this->getFilePath( $file_slug );
		$outstream = fopen( $path['path'], "w" );
		fputcsv( $outstream, $header );
		fclose( $outstream );
	}

	// APPENDS THE ROW OF DATA TO AN ALREADY EXISTING FILE
	function addRowToCSV( $file_slug, $row ){
		$path = $this->getFilePath( $file_slug );
		$outstream = fopen( $path['path'], "a");
		fputcsv($outstream, $row );
		fclose($outstream);
	}

	// RETURNS THE ARRAY OF ALL QUESTION IDS WHEN PASSED THE ASSOCIATIVE ARRAY OF QUESTION
	function getListQuestionIDs( $questions ){
		$question_ids = array();
		foreach( $questions as $question ){
			array_push( $question_ids, $question->ID );
		}
		return $question_ids;
	}

	function addQuestionsAsHeader( $file_slug, $questions ){

		$header = array(
			'ID',
			'IP ADDRESSS',
			'META',
			'CREATED ON'
		);

		foreach( $questions as $question ){
			array_push( $header, $question->title );
		}

		$this->addHeaderToCSV( $file_slug, $header );

	}

	function addGuestResponses( $file_slug, $guest, $responses, $question_ids ){

		$row = array(
			$guest->ID,
			$guest->ipaddress,
			$guest->meta,
			$guest->created_on
		);

		foreach( $question_ids as $question_id ){
			if( isset( $responses[ $question_id ] ) && isset( $responses[ $question_id ]['choices'] ) && is_array( $responses[ $question_id ]['choices'] ) ){
				array_push( $row, implode( ';', $responses[ $question_id ]['choices'] ) );
			}
			else{
				array_push( $row, '' );
			}
		}

		$this->addRowToCSV( $file_slug, $row );

	}

	function getFormattedResponses( $responses, $questions, $choices ){

		$data = array();

		foreach( $responses as $response ){

			// SETTING DEFAULT VALUES
			if( ! isset( $data[ $response->question_id ] ) ){
				if( isset( $questions[ $response->question_id ] ) && isset( $questions[ $response->question_id ]->title ) ){
					$data[ $response->question_id ] = array(
						'question_title'	=> $questions[ $response->question_id ]->title,
						'choices'			=> array()
					);
				}
			}


			if( $response->choice_id && isset( $choices[ $response->choice_id ] ) && isset( $choices[ $response->choice_id ]->title ) ){
				// CHECK IF THE CHOICE TEXT HAS THE RANKING VALUE
				if( $response->choice_text && is_numeric( $response->choice_text ) ){
					$data[ $response->question_id ][ 'choices' ][ $response->choice_text ] = $choices[ $response->choice_id ]->title;
				}
				else{
					array_push( $data[ $response->question_id ][ 'choices' ], $choices[ $response->choice_id ]->title );
				}
			}
			elseif( $response->choice_text && isset( $data[ $response->question_id ][ 'choices' ] ) && is_array( $data[ $response->question_id ][ 'choices' ] ) ){
				array_push( $data[ $response->question_id ][ 'choices' ], $response->choice_text );
			}

			// SORTING THE CHOICES BY KEY : USEFUL FOR CHECKBOX WITH RANKING
			if( isset( $data[ $response->question_id ][ 'choices' ] ) && is_array( $data[ $response->question_id ][ 'choices' ] ) ){
				ksort( $data[ $response->question_id ][ 'choices' ] );
			}



		}

		return $data;

	}

	function output( $survey_id ){

		$survey_slug = 'space_survey';

		$survey_db = SPACE_DB_SURVEY::getInstance();

		// GET THE LIST OF QUESTIONS IN THE SURVEY
		$questions = $survey_db->getQuestionsList( $survey_id );

		// GET THE MAP OF CHOICES IN THE SURVEY
		$choices = $survey_db->getChoicesList( $survey_id );

		// ADD THESE QUESTIONS AS CSV HEADER - RETURN LIST OF QUESTION IDs
		$question_ids = $this->addQuestionsAsHeader( $survey_slug, $questions );

		// LOGIC THAT RETURNS THE LIST OF GUEST IDs IN AN ARRAY
		$guest_ids = array( 1, 2, 3 );

		// ITERATE THROUGH EACH GUEST
		foreach( $guest_ids as $guest_id ){

			$guestResponses = $survey_db->getGuestDB()->getResponses( $guest_id );

			$responses = $this->getFormattedResponses( $guestResponses, $questions, $choices );

			$this->addGuestResponses( 'space_survey', $responses, $question_ids );

		}

	}

}

// CREATE AN INSTANCE FOR THE AJAX CALLBACK TO BE HANDLED
SPACE_EXPORT::getInstance();

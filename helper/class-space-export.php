<?php
/*
* EXPORT MODEL
*/

class SPACE_EXPORT extends SPACE_BASE{
	
	// RETURNING THE FILE PATH WHICH EXISTS IN THE WP UPLOADS DIRECTORY
	function getFilePath( $file_slug ){
		$filePath = array();
		$path = wp_upload_dir();
		$filePath['path'] = $path['path']."/$file_slug.csv";
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
	
	function addQuestionsAsHeader( $file_slug, $questions ){
		
		$header = array();
		
		$question_ids = array();
		
		foreach( $questions as $question ){
			array_push( $header, $question->title );
			array_push( $question_ids, $question->ID );
		}
		
		$this->addHeaderToCSV( $file_slug, $header );
		
		return $question_ids;
		
	}
	
	function addGuestResponses( $file_slug, $responses, $question_ids ){
		
		$row = array();
		
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
			
			if( ! isset( $data[ $response->question_id ] ) ){
				
				if( isset( $questions[ $response->question_id ] ) && isset( $questions[ $response->question_id ]->title ) ){
				
					$data[ $response->question_id ] = array(
						'question_title'	=> $questions[ $response->question_id ]->title,
						'choices'			=> array()
					);
				}
			}	
			
			
			if( $response->choice_id && isset( $choices[ $response->choice_id ] ) && isset( $choices[ $response->choice_id ]->title ) ){
				array_push( $data[ $response->question_id ][ 'choices' ], $choices[ $response->choice_id ]->title );
			}
			elseif( $response->choice_text ){
				array_push( $data[ $response->question_id ][ 'choices' ], $response->choice_text );
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
	
	
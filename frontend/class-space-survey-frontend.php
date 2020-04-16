<?php

	class SPACE_SURVEY_FRONTEND{

		var $survey;

		function __construct( $survey_id ){

			$survey_db = SPACE_DB_SURVEY::getInstance();
			$this->setSurvey( $survey_db->get_row( $survey_id ) );

		}

		function setSurvey( $survey ){ $this->survey = $survey;}
		function getSurvey(){ return $this->survey; }

		function get_input_name( $id, $key = 'val' ){
			return "quest[$id][$key]";
		}

		function question_type_field( $question ){
			echo "<input type='hidden' name='quest[$question->ID][type]' value='$question->type' />";
		}

		function question_html( $question ){
			$question = wp_unslash( $question );
			$question_tmp = apply_filters( 'space-question-template',  'partials/question.php' );
			ob_start();
			include( $question_tmp );
			return ob_get_clean();
		}

		function choices_html( $question ){
			$choices_tmp = apply_filters( "space-choices-$question->type-template",  "partials/choices-$question->type.php" );
			ob_start();
			include( $choices_tmp );
			return ob_get_clean();
		}

		function page_html( $page ){
			$page_tmp = apply_filters( 'space-page-template',  'partials/page.php' );
			ob_start();
			include( $page_tmp );
			return ob_get_clean();
		}

		function html(){
			ob_start();
			include( "partials/slides.php" );
			return ob_get_clean();
		}

	}

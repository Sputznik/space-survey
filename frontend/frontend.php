<?php
	
	class SPACE_FRONTEND{
		
		function __construct(){
			
			// Enqueue assets
			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
			
			
		}
		
		function choices_html( $question ){
			
			$choices_tmp = apply_filters( "space-choices-$question->type-template",  "partials/choices-$question->type.php" );
			
			include( $choices_tmp );
		}
		
		function question_html( $question ){
			
			$question_tmp = apply_filters( 'space-question-template',  'partials/question.php' );
			
			include( $question_tmp );
			
		}
		
		function page_html( $page ){
			
			$page_tmp = apply_filters( 'space-page-template',  'partials/page.php' );
			
			include( $page_tmp );
			
		}
		
		function assets(){
			
			$plugin_assets_folder = 'space-survey/assets/';
				
			wp_enqueue_style( 
				'space',	 													// SLUG OF THE CSS
				plugins_url( $plugin_assets_folder.'css/styles.css' ), 			// LOCATION OF THE CSS FILE
				array(), 														// DEPENDENCIES EHICH WOULD NEED TO BE LOADED BEFORE THIS FILE IS LOADED
				"1.0.3" 														// VERSION
			);
					
			wp_enqueue_script(	
				'space-slides', 
				plugins_url( $plugin_assets_folder.'js/slides.js' ), 
				array( 'jquery'), 
				'1.0.2', 
				true 
			);
			
			
		}
		
		
	}
	
	global $space_frontend;
	$space_frontend = new SPACE_FRONTEND;
	
	
	add_shortcode('space_survey', function( $atts ){
		
		global $space_frontend;
		
		$survey_id = $atts['id'];
		
		$survey_db = SPACE_DB_SURVEY::getInstance();
		
		$pages = $survey_db->listPages( $survey_id );
		
		include( "partials/slides.php" );
		
	});
	
	
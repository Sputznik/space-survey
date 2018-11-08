<?php
	
	class SPACE_FRONTEND{
		
		function __construct(){
			
			// Enqueue assets
			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
			
			
		}
		
		function question_html( $question ){
			_e("<div data-type='$question->type' class='space-question'>");
			
			_e("<h5>$question->title</h5>");
			
			_e("<p class='space-desc'>$question->description</p>");
			
			include("partials/choices-$question->type.php");
			
			_e("</div>");
		}
		
		function page_html( $page ){
			foreach( $page->questions as $question ){
				$this->question_html( $question );
			}
		}
		
		function assets(){
			
			$plugin_assets_folder = 'space-survey/assets/';
				
			wp_enqueue_style( 
				'space',	 													// SLUG OF THE CSS
				plugins_url( $plugin_assets_folder.'css/styles.css' ), 			// LOCATION OF THE CSS FILE
				array(), 														// DEPENDENCIES EHICH WOULD NEED TO BE LOADED BEFORE THIS FILE IS LOADED
				"1.0.2" 														// VERSION
			);
					
			wp_enqueue_script(	
				'space-slides', 
				plugins_url( $plugin_assets_folder.'js/slides.js' ), 
				array( 'jquery'), 
				'1.0.0', 
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
		
		echo "<div data-behaviour='space-slides'>";
		
		$i = 0;
		
		foreach( $pages as $page ){
			
			$slide_class = 'space-slide';
			if( !$i ){
				$slide_class .= ' active';
			}
			
			echo "<div id='slide-$i' class='$slide_class'>";
			
			
			$space_frontend->page_html( $page );
			
			
			
			//echo "<pre>";
			//print_r( $page );
			//echo "</pre>";
			
			echo "<button data-behaviour='space-slide-next'>Continue</button>";
			
			echo "</div>";
			
			$i++;
			
		}
		
		echo "</div>";
		
	});
	
	
<?php

	class SPACE_FRONTEND{

		function __construct(){

			// Enqueue assets
			add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

			add_action( 'wp_ajax_space_survey_guest', array( $this, 'getGuestData' ) );
			add_action( 'wp_ajax_nopriv_space_survey_guest', array( $this, 'getGuestData' ) );

			add_action( 'wp_ajax_space_survey_save', array( $this, 'saveGuestData' ) );
			add_action( 'wp_ajax_nopriv_space_survey_save', array( $this, 'saveGuestData' ) );
			
			add_shortcode( 'space_survey', array( $this, 'surveyHTML' ) );
			
			add_filter( 'the_content', array( $this, 'overrideContent' ) );

		}

		function getGuestData(){

			$data = array(
				'survey_id'	=> isset( $_GET['survey_id'] ) ? $_GET['survey_id'] : 0
			);

			$cookie_name = 'space_survey_' . $data['survey_id'];

			$guest_db = SPACE_DB_GUEST::getInstance();

			try{

				$guest = $guest_db->get_row( $_COOKIE[ $cookie_name ] );

				if( !$guest ) throw new Exception('Guest does not exist');

				$data['guest_id'] = $guest->ID;


			}catch( Exception $e ){

				$guestData = $guest_db->sanitize( array(
					'survey_id'	=> $data['survey_id']
				) );

				$data['guest_id'] = $guest_db->insert( $guestData );

				// SET COOKIE THAT IS VALID FOR 15 DAYS
				setcookie( $cookie_name, $data['guest_id'], time() + (86400 * 30 * 15), "/" );

			}

			if( $data['guest_id'] ){
				// ADD RESPONSES
				$data['responses'] = $guest_db->getResponses( $data['guest_id'] );
			}

			print_r( wp_json_encode( $data ) );

			wp_die();
		}

		function saveGuestData(){

			if( ! isset( $_POST['space_survey'] ) || ! wp_verify_nonce( $_POST['space_survey'], 'save' ) ){

				print 'Sorry, your nonce did not verify.';


			} else {

				$guest_db = SPACE_DB_GUEST::getInstance();

				$guest_db->saveResponses( $_POST );

			}

			wp_die();
		}

		function question_type_field( $question ){
			echo "<input type='hidden' name='quest[$question->ID][type]' value='$question->type' />";
		}

		function get_input_name( $id ){
			return "quest[$id][val]";
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
				'1.0.8',
				true
			);

			wp_localize_script( 'space-slides', 'space_settings', array(
				'ajax_url'	=> admin_url('admin-ajax.php')
			) );


		}

		function surveyHTML( $atts ){
			
			$survey_id = $atts['id'];

			$survey_db = SPACE_DB_SURVEY::getInstance();
			
			$pages = $survey_db->listPages( $survey_id );
			
			ob_start();
			include( "partials/slides.php" );
			return ob_get_clean();
		}
		
		function overrideContent( $content ){
			
			global $post;
			
			$content = $this->surveyHTML( array( 'id' => $post->ID ) );
			
			return $content;
		}

	}


	global $space_frontend;
	$space_frontend = new SPACE_FRONTEND;
	
	
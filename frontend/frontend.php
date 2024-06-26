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

				if( $this->isCookieDisabled( $data['survey_id'] ) ) throw new Exception("Cookie has been disabled");

				if( isset( $_COOKIE[ $cookie_name ] ) && $_COOKIE[ $cookie_name ] ){

					$guest = $guest_db->get_row( $_COOKIE[ $cookie_name ] );

					if( !$guest ) throw new Exception('Guest does not exist');

					$data['guest_id'] = $guest->ID;

				}
				else{
					throw new Exception('Guest does not exist');
				}
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

		function isCookieDisabled($survey_id){
			$data = get_post_meta( $survey_id, 'survey_settings', true );
			$value_attr = isset( $data[ 'disable-cookie' ] ) ? $data[ 'disable-cookie' ] : '';
			if($value_attr){ return true; }
			return false;
		}

		function saveGuestData(){

			if( ! isset( $_POST['space_survey'] ) || ! wp_verify_nonce( $_POST['space_survey'], 'save' ) ){

				print 'Sorry, your nonce did not verify.';


			} else {

				//echo "<pre>";
				//print_r( $_POST );
				//echo "</pre>";

				$guest_db = SPACE_DB_GUEST::getInstance();

				$response = $guest_db->saveResponses( $_POST );

				wp_send_json( $response );

				//print_r( $response );

			}

			wp_send_json( $_POST );


		}

		function assets(){

			$plugin_assets_folder = 'space-survey/assets/';

			wp_enqueue_style(
				'space',	 													// SLUG OF THE CSS
				plugins_url( $plugin_assets_folder.'css/styles.css' ), 			// LOCATION OF THE CSS FILE
				array(), 														// DEPENDENCIES EHICH WOULD NEED TO BE LOADED BEFORE THIS FILE IS LOADED
				SPACE_SURVEY_VERSION 														// VERSION
			);

			wp_enqueue_script(
				'space-autosave',
				plugins_url( $plugin_assets_folder.'js/autosave.js' ),
				array( 'jquery'),
				SPACE_SURVEY_VERSION,
				true
			);

			wp_enqueue_script(
				'space-slides',
				plugins_url( $plugin_assets_folder.'js/slides.js' ),
				array( 'jquery' ),
				SPACE_SURVEY_VERSION,
				true
			);

			wp_enqueue_script(
				'space-frontend',
				plugins_url( $plugin_assets_folder.'js/frontend.js' ),
				array( 'jquery', 'space-autosave', 'space-slides' ),
				SPACE_SURVEY_VERSION,
				true
			);

			wp_enqueue_script(
				'space-limit',
				plugins_url( $plugin_assets_folder.'js/limit.js' ),
				array( 'jquery'),
				SPACE_SURVEY_VERSION ,
				true
			);

			wp_enqueue_script(
				'space-rank',
				plugins_url( $plugin_assets_folder.'js/rank.js' ),
				array( 'jquery'),
				time() ,
				true
			);

			wp_localize_script( 'space-slides', 'space_settings', array(
				'ajax_url'	=> admin_url('admin-ajax.php')
			) );


		}

		function surveyHTML( $atts ){
			// $atts = shortcode_atts( array(
			// 		'id'		=>	0,
			// 		'prev'	=>	'Go Back',
			// 		'next'	=>	'Continue'
			// 	) , $atts);

			$survey_id = $atts['id'];
			require_once('class-space-survey-frontend.php');
			$survey_frontend = new SPACE_SURVEY_FRONTEND( $survey_id );
			return $survey_frontend->html();
		}

		function overrideContent( $content ){

			global $post;

			if( $post->post_type == 'space_survey' ){
				$content = $this->surveyHTML( array( 'id' => $post->ID ) );
			}

			return $content;
		}

	}


	global $space_frontend;
	$space_frontend = new SPACE_FRONTEND;

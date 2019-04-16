<?php

	class SPACE_ADMIN{

		var $menu;
		var $includes;
		var $post_types;
		var $meta_boxes;

		function __construct(){

			/* INCLUDE FILES */
			$this->includes = array(
				plugin_dir_path(__FILE__).'../forms/class-space-admin-form.php',
				'class-space-list-table.php',
				'class-space-question-list-table.php',
			);

			foreach( $this->includes as $inc_file ){
				require_once( $inc_file );
			}

			// SETTING CLASS VARIABLES
			$this->setPostTypes( array(
				'space_survey' => array(
					'slug'		=> 'space_survey',
					'labels' 	=> array(
						'name' 			=> 'Space Surveys',
						'singular_name' => 'Space Survey',
						'add_new_item'	=> 'Add New Survey'
					),
					'supports'	=> array( 'title','author' ),
					'menu_icon'	=> 'dashicons-editor-kitchensink'
				)
			) );

			$this->setMenu( array(
				'space-survey'	=> array(
					'title'	=> 'Space Survey',
					'icon'	=> 'dashicons-editor-kitchensink'
				),
				'space-questions' => array(
					'title'	=> 'Questions',
					'menu'	=> 'space-survey'
				),
				'space-question-edit' => array(
					'title'	=> 'Add Question',
					'menu'	=> 'space-survey'
				),
				'space-export'	=> array(
					'title'	=> 'Export',
					'menu'	=> 'space-survey'
				)
			) );

			$this->setMetaBoxes( array(
				array(
					'id'		=> 'survey-pages',
					'title'		=> 'Pages For Survey',
					'box_html'	=> 'survey_metabox_html',
					'supports'	=>	array('editor')
				),
				array(
					'id'		=> 'survey-results',
					'title'		=> 'Results',
					'box_html'	=> 'results_metabox_html',
					'supports'	=>	array('editor')
				)
			) );

			add_action( 'init', array( $this, 'init' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			add_action( 'admin_head', array( $this, 'admin_head' ), 50 );

			add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

			//add_action( 'save_post', array( $this, 'handle_survey_metabox' ) );

			// AJAX ACTION TO DROP TABLES. REMOVE FROM PRODUCTION
			add_action('wp_ajax_space_survey_drop', array($this, 'drop_db_tables'));

		}

		/*
		* GETTER AND SETTER FUNCTIONS
		*/
		function setPostTypes( $post_types ){ $this->post_types = $post_types; }
		function getPostTypes(){ return $this->post_types; }

		function getMenu(){ return $this->menu; }
		function setMenu( $menu ){ $this->menu = $menu; }

		function getMetaBoxes(){ return $this->meta_boxes; }
		function setMetaBoxes( $meta_boxes ){ $this->meta_boxes = $meta_boxes; }
		/*
		* END OF GETTER AND SETTER FUNCTIONS
		*/


		/*
		* HANDLES WORDPRESS INIT ACTION HOOK
		* REGISTER POST TYPES
		*/
		function init(){

			// REGISTER POST TYPES
			foreach( $this->getPostTypes() as $post_type ){
				register_post_type($post_type['slug'],
					array(
						'labels' 				=> $post_type['labels'],
						'public' 				=> isset( $post_type['public'] ) ? $post_type['public'] : true,
						'publicly_queryable' 	=> true,
						'show_ui'				=> true,
						'query_var' 			=> true,
						//'rewrite' 				=> array('slug' => $post_type['slug'], 'with_front' => FALSE),
						'has_archive' 			=> true,
						'menu_icon'				=> isset( $post_type['menu_icon'] ) ? $post_type['menu_icon'] : 'dashicons-images-alt',
						'supports'				=>	$post_type['supports']
					)
				);
			}



		}

		function add_meta_boxes(){

			// REGISTER META BOXES
			foreach( $this->getMetaBoxes() as $meta_box ){

				add_meta_box(
					$meta_box['id'], 													// Unique ID
					$meta_box['title'], 												// Box title
					array( $this, 'metabox_html' ),//array( $this, $meta_box['box_html'], ), 							// Content callback
					'space_survey',
					isset( $meta_box['context'] ) ? $meta_box['context'] : 'normal', 	// Context
					'default',															// Priority
					$meta_box
				);
			}
		}

		function admin_menu(){

			// REMOVE MENUS FOR CUSTOM POST TYPES
			remove_menu_page('edit.php?post_type=space_survey');

			foreach( $this->getMenu() as $slug => $menu_item ){

				$menu_item['slug'] = $slug;

				// CHECK FOR MAIN MENU OR SUB MENU
				if( !isset( $menu_item['menu'] ) ){
					add_menu_page( $menu_item['title'], $menu_item['title'], 'manage_options', $menu_item['slug'], array( $this, 'menu_page' ), $menu_item['icon'] );
				}
				else{
					add_submenu_page( $menu_item['menu'], $menu_item['title'], $menu_item['title'], 'manage_options', $menu_item['slug'], array( $this, 'menu_page' ) );
				}

			}



		}

		function admin_head(){

			$screen = get_current_screen();

			if( in_array( $screen->id, array('edit-space_survey', 'space_survey') ) ):

			/* HIGHLIGHT THE CURRENT MENU ITEM AFTER REDIRECT */
			?>
			<script>
				jQuery(document).ready(function($) {
					$('#toplevel_page_space-survey').addClass('wp-has-current-submenu wp-menu-open menu-top menu-top-first').removeClass('wp-not-current-submenu');
					$('#toplevel_page_space-survey > a').addClass('wp-has-current-submenu').removeClass('wp-not-current-submenu');
				});
			</script>
			<?php

			endif;

			?>
			<script>
				/* REWRITE THE PERMALINKS FOR THE SUBMENU ITEMS THAT ARE BEING REDIRECTED */
				jQuery(document).ready(function($) {

					var anchors = [
						['admin.php?page=space-survey', 'edit.php?post_type=space_survey'],
					];

					for( var i=0; i<anchors.length; i++ ){
						$( "a[href='" + anchors[i][0] + "']" ).attr( 'href', anchors[i][1] );
					}

				});
			</script>
			<?php
		}

		// LOAD ASSETS INCLUDING CSS AND JS
		function assets( $hook ) {

			$plugin_assets_folder = 'space-survey/assets/';

			wp_enqueue_style(
				'space-admin', 													// SLUG OF THE CSS
				plugins_url( $plugin_assets_folder.'css/admin-styles.css' ), 	// LOCATION OF THE CSS FILE
				array(), 														// DEPENDENCIES EHICH WOULD NEED TO BE LOADED BEFORE THIS FILE IS LOADED
				"1.1.6" 														// VERSION
			);

			wp_enqueue_script(
				'space-autosize',
				plugins_url( $plugin_assets_folder.'js/autosize.js' ),
				array( 'jquery'),
				'1.0.1',
				true
			);

			wp_enqueue_script(
				'space-autocomplete',
				plugins_url( $plugin_assets_folder.'js/autocomplete.js' ),
				array( 'jquery', 'jquery-ui-autocomplete' ),
				'1.0.2',
				true
			);

			wp_enqueue_script(
				'space-repeater',
				plugins_url( $plugin_assets_folder.'js/repeater.js' ),
				array( 'jquery'),
				'1.0.8',
				true
			);

			wp_enqueue_script(
				'space-repeater-choices',
				plugins_url( $plugin_assets_folder.'js/repeater-choices.js' ),
				array( 'jquery', 'jquery-ui-sortable', 'space-autocomplete', 'space-autosize', 'space-repeater' ),
				'1.0.0',
				true
			);

			wp_enqueue_script(
				'space-repeater-rules',
				plugins_url( $plugin_assets_folder.'js/repeater-rules.js' ),
				array( 'jquery', 'jquery-ui-sortable', 'space-autocomplete', 'space-autosize', 'space-repeater' ),
				'1.0.2',
				true
			);

			wp_enqueue_script(
				'space-repeater-questions',
				plugins_url( $plugin_assets_folder.'js/repeater-questions.js' ),
				array( 'jquery', 'jquery-ui-sortable', 'space-autocomplete', 'space-autosize', 'space-repeater', 'space-repeater-rules' ),
				'1.0.5',
				true
			);

			wp_enqueue_script(
				'space-repeater-pages',
				plugins_url( $plugin_assets_folder.'js/repeater-pages.js' ),
				array( 'jquery', 'jquery-ui-sortable', 'space-autocomplete', 'space-autosize', 'space-repeater' ),
				'1.0.1',
				true
			);

			wp_enqueue_script(
				'space-repeater-export',
				plugins_url( $plugin_assets_folder.'js/repeater-export-filters.js' ),
				array( 'jquery', 'jquery-ui-sortable', 'space-autocomplete', 'space-autosize', 'space-repeater' ),
				'1.0.0',
				true
			);

			wp_enqueue_script(
				'space-batch-process',
				plugins_url( $plugin_assets_folder.'js/batch-process.js' ),
				array( 'jquery' ),
				'1.0.2',
				true
			);

			wp_enqueue_script(
				'space-script',
				plugins_url( $plugin_assets_folder.'js/main.js' ),
				array( 'space-repeater-choices', 'space-repeater-questions', 'space-repeater-pages', 'space-repeater-export', 'space-batch-process', 'wp-backbone', 'wp-api'),
				'1.0.3',
				true
			);

			wp_enqueue_editor();

			wp_enqueue_media();

			$survey_db = SPACE_DB_SURVEY::getInstance();

			wp_localize_script( 'space-script', 'space_settings', array(
				'ajax_url'				=> admin_url('admin-ajax.php'),
				'required_questions'	=> $survey_db->listRequiredQuestions(),
				'rules'					=> $survey_db->listRules()
			) );
		}

		/* MENU PAGE */
		function menu_page(){
			$page = $_GET[ 'page' ];
			_e( '<div class="wrap">' );
			include( 'templates/'.$page.'.php' );
			_e( '</div>' );
		}

		/*AJAX CALLBACK TO DROP DB TABLES*/
		function drop_db_tables(){
			do_action('space_survey_drop');
			wp_die();
		}

		function metabox_html( $post, $metabox ){
			include( 'templates/metabox-'.$metabox['id'].'.php' );
		}

	}

	new SPACE_ADMIN;

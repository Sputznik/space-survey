<?php
	
	class SPACE_ADMIN{
		
		var $menu;
		var $includes;
		var $post_types;
		
		function __construct(){
			
			/* INCLUDE FILES */
			$this->includes = array(
				plugin_dir_path(__FILE__).'../forms/class-space-admin-form.php',
				'class-space-list-table.php',
				'class-space-question-list-table.php',
				'class-space-survey-list-table.php',
			);
			
			foreach( $this->includes as $inc_file ){
				require_once( $inc_file );
			}
			
			/* REGISTER POST TYPES *
			$this->post_types = array(
				'space-question' => array(
					'slug'		=> 'space-question',
					'labels' 	=> array(
						'name' 			=> 'Questions', 
						'singular_name' => 'Question',
						'add_new_item'	=> 'Add New Question'
					),
					'supports'	=> array( 'title', 'editor' )
				)
			);
			add_action( 'init', function(){
				foreach( $this->post_types as $post_type ){
					$this->register_post_type( $post_type );
				}
			} );
			*/
			
			/* ADMIN MENU FOR THE SPACE SURVEY */
			$this->menu = array(
				'space-survey'	=> array(
					'title'	=> 'Space Survey',
					'icon'	=> 'dashicons-editor-kitchensink'
				),
				'space-survey-edit' => array(
					'title'	=> 'Add Survey',
					'menu'	=> 'space-survey'
				),
				'space-questions' => array(
					'title'	=> 'Questions',
					'menu'	=> 'space-survey'
				),
				'space-question-edit' => array(
					'title'	=> 'Add Question',
					'menu'	=> 'space-survey'
				),
			);
			add_action( 'admin_menu', function(){
				
				foreach( $this->menu as $slug => $menu_item ){
					
					$menu_item['slug'] = $slug;
					
					if( !isset( $menu_item['menu'] ) ){
						$this->add_menu_page( $menu_item );
					}
					else{
						$this->add_submenu_page( $menu_item );
					}
					
				}
			} );
			
			
			/* ENQUEUE SCRIPTS AND STYLES ON ADMIN DASHBOARD */
			add_action( 'admin_enqueue_scripts', array( $this, 'assets') );	
			
		}
		
		// LOAD ASSETS INCLUDING CSS AND JS
		function assets( $hook ) {
			
			$plugin_assets_folder = 'space-survey/assets/';
			
			wp_enqueue_style( 
				'space-admin', 													// SLUG OF THE CSS
				plugins_url( $plugin_assets_folder.'css/admin-styles.css' ), 	// LOCATION OF THE CSS FILE
				array(), 														// DEPENDENCIES EHICH WOULD NEED TO BE LOADED BEFORE THIS FILE IS LOADED
				"1.0.4" 														// VERSION
			);
			
			wp_enqueue_script(	
				'space-autosize', 
				plugins_url( $plugin_assets_folder.'js/autosize.js' ), 
				array( 'jquery'), 
				'1.0.0', 
				true 
			);
			
			wp_enqueue_script(	
				'space-repeater', 
				plugins_url( $plugin_assets_folder.'js/repeater.js' ), 
				array( 'jquery'), 
				'1.0.1', 
				true 
			);
			
			wp_enqueue_script(	
				'space-checkbox', 
				plugins_url( $plugin_assets_folder.'js/choice-form.js' ), 
				array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-autocomplete', 'space-autosize', 'space-repeater'), 
				'1.1.3', 
				true 
			);
			
		}
		
		/* REGISTER CUSTOM POST TYPE */
		function register_post_type( $post_type ){
			
			/*
			echo "<pre>";
			print_r( $post_type );
			echo "</pre>";
			*/
			
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
		
		/* ADD MAIN MENU FOR THE PLUGIN */
		function add_menu_page( $item ){
			add_menu_page( $item['title'], $item['title'], 'manage_options', $item['slug'], array( $this, 'menu_page' ), $item['icon'] );
		}
		
		/* ADD SUB MENU FOR THE PLUGIN */
		function add_submenu_page( $item ){
			add_submenu_page( $item['menu'], $item['title'], $item['title'], 'manage_options', $item['slug'], array( $this, 'menu_page' ) );
		}
		
		/* MENU PAGE */
		function menu_page(){
			$page = $_GET[ 'page' ];
			_e( '<div class="wrap">' );
			include( 'templates/'.$page.'.php' );
			_e( '</div>' );
		}
		
		
	}
	
	new SPACE_ADMIN;
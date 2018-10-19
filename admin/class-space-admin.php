<?php
	
	class SPACE_ADMIN{
		
		var $menu;
		var $includes;
		var $post_types;
		
		function __construct(){
			
			/* INCLUDE FILES */
			$this->includes = array(
				'class-space-form.php',
				'class-space-list-table.php',
				'class-space-question-list-table.php',
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
				'space-questions' => array(
					'title'	=> 'Questions',
					'menu'	=> 'space-survey'
				),
				'space-question-edit' => array(
					'title'	=> 'Add/Edit Question',
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
			_e( '<h1>'.$this->menu[ $page ][ 'title' ].'</h1>');
			include( 'templates/'.$page.'.php' );
			_e( '</div>' );
		}
		
		
	}
	
	new SPACE_ADMIN;
<?php

	class SPACE_SURVEY_ADMIN extends SPACE_BASE{
		var $meta_boxes;

		function __construct(){
			$this->setMetaBoxes( array(
				array(
					'id'		=> 'survey-pages',
					'title'		=> 'Pages For Survey',
					'supports'	=>	array('editor')
				),
				array(
					'id'		=> 'survey-buttons',
					'title'		=> 'Settings',
					'supports'	=>	array('editor')
				),
			) );

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

			add_action( 'post_submitbox_misc_actions', array( $this, 'export_button' ) );
			add_filter( 'manage_space_survey_posts_columns', array( $this, 'manage_columns' ) );
			add_action( 'manage_space_survey_posts_custom_column', array( $this, 'fill_column' ), 10, 2 );

		}

		function getMetaBoxes(){ return $this->meta_boxes; }
		function setMetaBoxes( $meta_boxes ){ $this->meta_boxes = $meta_boxes; }

		function add_meta_boxes(){
			// REGISTER META BOXES
			foreach( $this->getMetaBoxes() as $meta_box ){
				add_meta_box(
					$meta_box['id'], 														// Unique ID
					$meta_box['title'], 												// Box title
					array( $this, 'metabox_html' ),
					'space_survey',
					isset( $meta_box['context'] ) ? $meta_box['context'] : 'normal', 	// Context
					'default',																	// Priority
					$meta_box
				);
			}
		}

		function metabox_html( $post, $metabox ){
			include( 'templates/metabox-'.$metabox['id'].'.php' );
		}

		function manage_columns( $columns ){
			$columns['responses'] = 'Responses';
			//$columns['num_questions'] = 'Number of Questions';
			return $columns;
		}

		function fill_column( $column, $post_id ){

			switch ( $column ) {
				case 'responses' :
					$survey_db = SPACE_DB_SURVEY::getInstance();
					$totalGuests = $survey_db->totalGuests( $post_id );
					$urls_util = SPACE_URLS::getInstance();
					if( $totalGuests ){
						_e( "<p><a target='_blank' href='".$urls_util->csvs( $post_id )."'>Generate CSV</a></p>" );
						_e( "<p><a href='".$urls_util->responses( $post_id )."'>View Responses ($totalGuests)</a></p>" );
					}
					else{
						_e('<p>No responses</p>');
					}
					break;
			}
		}

		function export_button( $post ){
			$survey_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;
			if( $post->post_type == 'space_survey' && $survey_id ){
				include( 'templates/metabox-survey-export.php' );
			}
		}
	}

	SPACE_SURVEY_ADMIN::getInstance();

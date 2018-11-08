<?php

	class SPACE_SURVEY_LIST_TABLE extends SPACE_LIST_TABLE{ 
		
		var $singular_edit_page;
		
		function __construct(){
			
			$this->singular_edit_page = 'space-survey-edit';
			
			$this->screen = get_current_screen();
			
			parent::__construct( array(
				'singular'  => 'survey',     //singular name of the listed records
				'plural'    => 'surveys',    //plural name of the listed records
				'ajax'      => false 
			) );
		}
		
		function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'desc':
					return $item->description;
				default:
					return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
			}
		}
		
		/**
		* Decide which columns to activate the sorting functionality on
		* @return array $sortable, the array of columns that can be sorted by the user
		*/
		public function get_sortable_columns() {
			$sortable_columns = array(
				'survey'  => array('survey',true),
			); 
			return $sortable_columns;
		}
		
		function get_columns(){
			$columns = array(
				'cb'		=> '<input type="checkbox" />',
				'survey' 	=> 'Title',
				'desc'    	=> 'Description'
			);
			return $columns;
		}
		
		function column_cb( $item ) {
			return '<input type="checkbox" name="survey[]" value="'.$item->ID.'" />';
		}
		
		function prepare_items() {
			$columns = $this->get_columns();
			
			$hidden = $this->get_hidden_columns();
			
			$sortable = $this->get_sortable_columns();
			
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			$per_page = 10;
			$page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
			
			$survey_db = SPACE_DB_SURVEY::getInstance();
			$data = $survey_db->results( $page, $per_page );
			$this->items = $data['results'];
			
			$this->set_pagination_args( array(
				'total_items'	=> $data['num_rows'],
				'per_page'		=> $per_page
			) );
			
			
			
		}
		
		// Setup Hidden columns and return them
		public function get_hidden_columns(){
			return array();
		}
		
		function column_survey($item) {
			$actions = array(
				'edit'	=> sprintf('<a href="?page=%s&ID=%s">Edit</a>', $this->singular_edit_page, $item->ID ),
				'trash'	=> sprintf('<a href="?page=%s&action=trash&ID=%s">Trash</a>', $this->singular_edit_page, $item->ID ),
			);
			return sprintf('%1$s %2$s', $item->title, $this->row_actions( $actions ) );
		}
		
		function get_bulk_actions(){
			$actions = array(
				'trash'    => 'Move To Trash'
			);
			return $actions;
		}
		public function process_bulk_action(){
			if ('trash' === $this->current_action()) {
				
			}
		}
		
		function get_primary_column_name() {
			return 'ID';
		}
		
		
	}
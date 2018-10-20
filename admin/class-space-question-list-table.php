<?php

	class SPACE_QUESTION_LIST_TABLE extends SPACE_LIST_TABLE{ 
		
		var $example_data;
		
		var $singular_edit_page;
		
		function __construct(){
			
			$this->singular_edit_page = 'space-question-edit';
			
			$this->example_data = array(
				array('ID' => 1, 'question' => 'Quarter Share', 'author' => 'Nathan Lowell','isbn' => '978-0982514542'),
				array('ID' => 2, 'question' => '7th Son: Descent', 'author' => 'J. C. Hutchins', 'isbn' => '0312384378'),
				array('ID' => 3, 'question' => 'Shadowmagic', 'author' => 'John Lenahan', 'isbn' => '978-1905548927'),
				array('ID' => 4, 'question' => 'The Crown Conspiracy', 'author' => 'Michael J. Sullivan', 'isbn' => '978-0979621130'),
				array('ID' => 5, 'question' => 'Max Quick: The Pocket and the Pendant', 'author' => 'Mark Jeffrey', 'isbn' => '978-0061988929'),
				array('ID' => 6, 'question' => 'Jack Wakes Up: A Novel', 'author' => 'Seth Harwood', 'isbn' => '978-0307454355')
			);
			
			
			
			$this->screen = get_current_screen();
			
			parent::__construct( array(
				'singular'  => 'question',     //singular name of the listed records
				'plural'    => 'questions',    //plural name of the listed records
				'ajax'      => false 
			) );
		}
		
		function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'desc':
					return $item->description;
				case 'type':
					return $item->type;
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
				'question'  => array('question',true),
			); 
			return $sortable_columns;
		}
		
		function get_columns(){
			$columns = array(
				'cb'		=> '<input type="checkbox" />',
				'question' 	=> 'Title',
				'desc'    	=> 'Description',
				'type'      => 'Type'
			);
			return $columns;
		}
		
		function column_cb( $item ) {
			return '<input type="checkbox" name="question[]" value="'.$item->ID.'" />';
		}
		
		function prepare_items() {
			$columns = $this->get_columns();
			
			$hidden = $this->get_hidden_columns();
			
			$sortable = $this->get_sortable_columns();
			
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			$per_page = 10;
			$page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;
			
			$question_db = SPACE_DB_QUESTION::getInstance();
			$data = $question_db->results( $page, $per_page );
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
		
		function column_question($item) {
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
<?php

	class SPACE_RESPONSES_LIST_TABLE extends SPACE_LIST_TABLE{

		var $example_data;

		var $singular_edit_page;

		var $filterChoices;
		var $survey_id;
		var $hide_zero_attempted;

		function __construct(){

			$this->singular_edit_page = 'space-response-view';

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
				'singular'  => 'response',     //singular name of the listed records
				'plural'    => 'responses',    //plural name of the listed records
				'ajax'      => false
			) );


		}

		protected function export_button(){
			if( $this->survey_id ){
				$export_link = SPACE_URLS::getInstance()->csvs( $this->survey_id, $this->filterChoices, $this->hide_zero_attempted );
				echo "<div class='alignleft actions'>";
				echo "<a target='_blank' href='$export_link' class='button'>Export CSV</a>";
				echo "</div>";
			}
		}

		/**
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {

			echo '<div class="alignleft actions">';

			if ( 'top' === $which && ! is_singular() ) {
				ob_start();

				do_action( 'restrict_manage_posts', $this->screen->post_type, $which );

				$output = ob_get_clean();

				if ( ! empty( $output ) ) {
					echo $output;
					submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
				}
			}

			if ( $this->is_trash && current_user_can( get_post_type_object( $this->screen->post_type )->cap->edit_others_posts ) && $this->has_items() ) {
				submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
			}

			if( $this->has_items() ){
				$this->export_button();
			}

			echo '</div>';

			/**
			 * Fires immediately following the closing "actions" div in the tablenav for the posts
			 * list table.
			 *
			 * @since 4.4.0
			 *
			 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
			 */

		}

		function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'created_on':
					return date("F d, Y h:i:s", strtotime( $item->created_on ) );
				case 'meta':
					return $item->meta;
				case 'survey':
					return "<a href='".SPACE_URLS::getInstance()->responses( $item->survey_id )."'>" . get_the_title( $item->survey_id ) . "</a>";
				case 'tot_questions':
					return SPACE_DB_GUEST::getInstance()->totalQuestionsAttempted( $item->ID );
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
				//'ipaddress'  => array( 'ipaddress', true ),
			);
			return $sortable_columns;
		}

		function get_columns(){
			$columns = array(
				'cb'					=> '<input type="checkbox" />',
				'ipaddress' 	=> 'IP Address',
				'meta'    		=> 'Meta',
				'survey'			=> 'Survey',
				'tot_questions'	=> 'Attempted Questions',
				'created_on' 	=> 'Created on',
			);
			return $columns;
		}

		function column_cb( $item ) {
			return '<input type="checkbox" name="guests[]" value="'.$item->ID.'" />';
		}

		function prepare_items( $survey_id = 0, $filterChoices = array(), $hide_zero_attempted = false ) {

			$this->filterChoices = $filterChoices;
			$this->survey_id = $survey_id;
			$this->hide_zero_attempted = $hide_zero_attempted;

			/*
			* SETTING COLUMNS
			* SORTABLE COLUMNS
			* HIDDEN COLUMNS
			* COLUMN HEADERS
			*/
			$columns = $this->get_columns();
			$hidden = $this->get_hidden_columns();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);

			/*
			* SETTING PAGINATION ATTRIBUTES
			*/
			$per_page = 10;
			$page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;

			//echo "<pre>";
			//print_r( $filterChoices );
			//echo "</pre>";

			$survey_db = SPACE_DB_SURVEY::getInstance();

			$search_term = "";
			if( isset( $_GET['s'] ) && $_GET['s'] ){
				$search_term = $_GET['s'];
			}

			$queries = $survey_db->getResponsesQuery( $this->survey_id, $this->filterChoices, $this->hide_zero_attempted, $search_term, $page, $per_page );

			//echo "<pre>";
			//print_r( $queries );
			//echo "</pre>";



			/*
			* FEEDING THE DATA FROM THE DATABASE INTO THE LIST TABLE UI
			*/
			$this->items = $survey_db->get_results( $queries['results'] );
			$this->set_pagination_args( array(
				'total_items'	=> $survey_db->get_var( $queries['count'] ),
				'per_page'		=> $per_page
			) );



		}

		// Setup Hidden columns and return them
		public function get_hidden_columns(){
			return array();
		}

		function column_ipaddress($item) {
			$actions = array(
				'edit'	=> sprintf('<a href="?page=%s&ID=%s">View</a>', $this->singular_edit_page, $item->ID ),
				'trash'	=> sprintf('<a href="?page=%s&action=trash&ID=%s">Trash</a>', $this->singular_edit_page, $item->ID ),
			);
			return sprintf('%1$s %2$s', $item->ipaddress, $this->row_actions( $actions ) );
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

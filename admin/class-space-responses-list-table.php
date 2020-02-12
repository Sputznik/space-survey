<?php

	class SPACE_RESPONSES_LIST_TABLE extends SPACE_LIST_TABLE{

		var $example_data;

		var $singular_edit_page;

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

		protected function surveys_dropdown() {

			/*
			* GET THE LAST 20 SURVEYS
			*/
			$survey_db = SPACE_DB_SURVEY::getInstance();
			$surveys = $survey_db->results( 1, 20, array(
				'col_formats'	=> array(
					'post_type'		=> '%s',
					'post_status'	=> '%s',
				),
				'col_values'	=> array( 'space_survey', 'publish' ),
				'operator'		=> '='
			) );

			$filter_by_survey = isset( $_GET['survey'] ) ? $_GET['survey'] : 0;

			// LABEL
			printf( "<label for='%s' class='%s'>%s</label>\n",
				esc_attr( 'filter-by-survey' ),
				esc_attr( 'screen-reader-text' ),
				esc_attr( 'Filter by survey' )
			);

			// START OF SELECT TAG
			printf( "<select id='%s' name='%s'>",
				esc_attr( 'filter-by-survey' ),
				esc_attr( 'filter_by_survey' )
			);

			// DEFAULT OPTION
			printf( "<option %s value='%s'>%s</option>\n",
				selected( $filter_by_survey, 0 ),
				esc_attr( 0 ),
				esc_attr( 'All surveys' )
			);

			// ITERATE THROUGH EACH SURVEY - AS OPTIONS FOR DROPDOWN
			foreach ( $surveys['results'] as $survey ) {

				printf( "<option %s value='%s'>%s</option>\n",
					selected( $filter_by_survey, $survey->ID, false ),
					esc_attr( $survey->ID ),
					esc_attr( $survey->post_title )
				);
			}

			_e('</select>');

		}

		/**
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {
			?>
			<div class="alignleft actions">
			<?php
			if ( 'top' === $which && ! is_singular() ) {
				ob_start();

				$this->surveys_dropdown();

				/**
				 * Fires before the Filter button on the Posts and Pages list tables.
				 *
				 * The Filter button allows sorting by date and/or category on the
				 * Posts list table, and sorting by date on the Pages list table.
				 *
				 * @since 2.1.0
				 * @since 4.4.0 The `$post_type` parameter was added.
				 * @since 4.6.0 The `$which` parameter was added.
				 *
				 * @param string $post_type The post type slug.
				 * @param string $which     The location of the extra table nav markup:
				 *                          'top' or 'bottom' for WP_Posts_List_Table,
				 *                          'bar' for WP_Media_List_Table.
				 */
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
			?>
			</div>
			<?php
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
					return $item->created_on;
				case 'meta':
					return $item->meta;
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
				'ipaddress'  => array( 'ipaddress', true ),
			);
			return $sortable_columns;
		}

		function get_columns(){
			$columns = array(
				'cb'					=> '<input type="checkbox" />',
				'ipaddress' 	=> 'IP Address',
				'meta'    		=> 'Meta',
				'created_on' 	=> 'Created on'
			);
			return $columns;
		}

		function column_cb( $item ) {
			return '<input type="checkbox" name="guests[]" value="'.$item->ID.'" />';
		}

		function prepare_items() {

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
			//print_r( $_POST );
			//echo "</pre>";

			/*
			* GET DATA FROM THE DATABASE. CHECK IF SEARCH TERM HAS BEEN ENTERRED.

			$question_db = SPACE_DB_QUESTION::getInstance();
			if( isset( $_POST['s'] ) ){
				$data = $question_db->listQuestions( $page, $per_page, $_POST['s'] );
			}
			else{
				$data = $question_db->listQuestions( $page, $per_page );
			}
			*/

			$survey_db = SPACE_DB_SURVEY::getInstance();

			$filter_by_survey = 0;
			if( isset( $_GET['survey'] ) ){
				// CHECK TO SEE IF FILTER IS SET ON THE SURVEYS DROPDOWN
				$filter_by_survey = $_GET['survey'];
			}

			$search_term = "";
			if( isset( $_GET['s'] ) && $_GET['s'] ){
				$search_term = $_GET['s'];
			}

			$queries = $survey_db->getResponsesQuery( $filter_by_survey, $search_term, $page, $per_page );

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

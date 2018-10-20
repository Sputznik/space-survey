<?php

	class SPACE_DB_BASE{
		
		private $table;
		private $table_slug;
		private static $instance = null;
		
		function __construct(){
			$this->setTable( $this->getTablePrefix() . $this->getTableSlug() );
		}
		
		// SINGLETON DESIGN PATTERN - NEEDS TO BE IMPLEMENTED IN EACH CHILD
		public static function getInstance(){
			if( self::$instance == null ){
				self::$instance = new static();
			}
			return self::$instance;
		}
		
		/* GETTER AND SETTER FUNCTIONS */
		function setTable( $table ){ $this->table = $table; }
		function getTable(){ return $this->table; }
		function setTableSlug( $slug ){ $this->table_slug = $slug; }
		function getTableSlug(){ return $this->table_slug; }
		function getTablePrefix(){
			global $wpdb;
			return $wpdb->prefix.'space_'; 
		}
		/* GETTER AND SETTER FUNCTIONS */
		
		// WRAPPER AROUND WPDB->QUERY
		function query( $sql ){
			global $wpdb;
			$result = $wpdb->query( $sql );
		}
		
		// WRAPPER AROUND WPDB->INSERT
		function insert( $data ){
			global $wpdb;
			return $wpdb->insert( $this->getTable(), $data );
		}
		
		function update( $id, $data ){
			global $wpdb;
			return $wpdb->update( $this->getTable(), $data, array( 'ID' => $id ) );
		}
		
		// WRAPPER AROUND WPDB->GET_RESULTS
		function get_results( $sql ){
			global $wpdb;
			return $wpdb->get_results( $sql );
		}
		
		// WRAPPER AROUND WPDB->GET_VAR
		function get_var( $sql ){
			global $wpdb;
			return $wpdb->get_var( $sql );
		}
		
		// WRAPPER AROUND WPDB->GET_CHARSET_COLLATE
		function get_charset_collate(){
			global $wpdb;
			return $wpdb->get_charset_collate();
		}
		
		// GET SINGLE ROW USING UNIQUE ID
		function get_row( $ID ){
			global $wpdb;
			
			$table = $this->getTable();
			
			$query = "SELECT * FROM $table WHERE ID=$ID;"; 
			
			return $wpdb->get_row( $query );
		}
		
		function results( $page, $per_page ){
			$data = array();
			$table = $this->getTable();
			
			// QUERY TO GET TOTAL NUMBER OF ROWS
			$data['num_rows'] = $this->get_var( "SELECT COUNT(*) FROM $table;" );
			
			// QUERY TO GET PAGINATED RESPONSE
			$offset = ( $page-1 ) * $per_page;
			$data['results'] = $this->get_results( "SELECT * FROM $table LIMIT $offset,$per_page;" );
			
			return $data;
		}
	}
	
	class SPACE_DB_QUESTION extends SPACE_DB_BASE{
		
		function __construct(){
			$this->setTableSlug( 'question' );
			parent::__construct();
			$this->create();
		}
		
		function getData(){
			
		}
		
		function create(){
			
			$table = $this->getTable();
			$charset_collate = $this->get_charset_collate();
			
			$sql = "CREATE TABLE IF NOT EXISTS $table ( 
				ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(255),
				description VARCHAR(255),
				rank INT,
				type VARCHAR(20),
				author_id BIGINT(20),
				parent BIGINT(20),
				PRIMARY KEY(ID)
			) $charset_collate;";
			
			$this->query( $sql );
		}
	}
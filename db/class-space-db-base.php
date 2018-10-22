<?php
/*
* ABSTRACT MODEL THAT HANDLES SQL QUERIES AND ACTS AS A WRAPPER FOR WPDB
*/

class SPACE_DB_BASE{
		
	private $table;
	private $table_slug;
	private static $instance = null;
		
	function __construct(){
		$this->setTable( $this->getTablePrefix() . $this->getTableSlug() );
		$this->create();
	}
		
	// SINGLETON DESIGN PATTERN - NEEDS TO BE IMPLEMENTED IN EACH CHILD
	public static function getInstance(){
		
		if( self::$instance == null ){
			self::$instance = array();
		}
		
		$class = get_called_class();
		
		if( !isset( self::$instance[ $class ] ) ){
            // new $class() will work too
            self::$instance[ $class ] = new static();
        }
		
        return self::$instance[ $class ];
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
		return $wpdb->query( $sql );
	}
		
	// WRAPPER AROUND WPDB->INSERT
	function insert( $data ){
		global $wpdb;
		$wpdb->insert( $this->getTable(), $data );
		return $wpdb->insert_id;
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
	
	// WRAPPER AROUND WPDB->PREPARE
	function prepare( $query, $args ){
		global $wpdb;
		return $wpdb->prepare( $query, $args );
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
	
	function filter( $col_formats, $col_values, $order_by = 'ID', $order = 'ASC' ){
		
		$table = $this->getTable();
		
		// FORM THE QUERY
		$query = "SELECT * from $table";
		if( is_array( $col_formats ) && count( $col_formats ) ){
			$query .= " WHERE";
			$i = 0;
			foreach( $col_formats as $col => $col_format ){
				if( $i ){ $query .= " AND"; }
				$query .= " $col = $col_format";
				$i++;
			}
		}
		$query .= " ORDER BY $order_by $order";
		$query .= ";";
		
		return $this->get_results( $this->prepare( $query, $col_values ) );
	}
	
	// DELETE SPECIFIC ROW
	function delete_row( $ID ){
		$table = $this->getTable();
		$sql = "DELETE FROM $table WHERE ID = %d;";
		$this->query( $this->prepare( $sql, $ID ) );
	}
	
	// DELETE MULTIPLE ROWS WITH MATCHING ID
	function delete_rows( $ids_arr ){
		if( is_array( $ids_arr ) && count( $ids_arr ) ){
			$ids_str = implode( ',', $ids_arr );
			$table = $this->getTable();
			$query = "DELETE FROM $table WHERE ID IN ($ids_str);";
			$this->query( $query );
		}
	}
		
	// TO BE IMPLEMENTED BY CHILD CLASSES - HANDLES TABLE CREATION
	function create(){}
	
	// TO BE IMPLEMENTED BY CHILD CLASSES - RETURNS DB DATA
	function sanitize( $data ){ return $data; }
}
	
	
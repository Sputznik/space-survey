<?php
/*
* 
*/	

require_once( 'class-space-form.php' );
	
class SPACE_ADMIN_FORM extends SPACE_FORM{
		
	var $fields;
	var $page;
	var $pageTitle;
		
	function __construct( $fields, $pageTitle, $page ){
			
		// SET FIELDS
		$this->setFields( $fields );
			
		// SET PAGE
		$this->setPage( $page );
			
		// SET PAGE TITLE
		$this->setPageTitle( $pageTitle );
			
		// FORM INIT ACTION HOOK - WHERE ALL THE DATABASE RELATED QUERIES ARE DONE
		do_action( $this->getPage(). '-form-init', $this );
			
	}
		
	/* GETTER AND SETTER FUNCTIONS */
	function setFields( $fields ){ $this->fields = $fields; }
	function getFields(){ return $this->fields; }
		
	function setPage( $page ){ $this->page = $page; }
	function getPage(){ return $this->page; }
		
	function setPageTitle( $pageTitle ){ $this->pageTitle = $pageTitle; }
	function getPageTitle(){ return $this->pageTitle; }
	/* GETTER AND SETTER FUNCTIONS */
		
	function display(){
		include 'templates/admin-form.php';
	}
		
	// WRAPPER FOR WP DO_ACTION
	function do_action( $action_hook ){
		do_action( $this->getPage().'-'.$action_hook, $this );
	}
}
<?php
/*
* IDEA IS TO CREATE A SORTABLE REPEATABLE FIELD THAT CAN BE CALLED FROM THE SURVEY FORM
*/

class SPACE_PAGE_FORM extends SPACE_FORM{

	var $pages;

	function __construct( $pages ){
		$this->setPages( $pages );
	}

	function getPages(){ return $this->pages; }
	function setPages( $pages ){ $this->pages = $pages; }

	/*
	* Template: called from display() of space-survey/admin/class-space-checkbox-form.php
	* Idea: to create a repeatable field to add pages in a survey
	*/
	function display(){

		/*
		if( isset( $_GET['post'] )  && $_GET['post'] == 417 ){
			echo "<pre>";
			print_r( $this->getPages() );
			echo "<pre>";
		}
		*/
		
		_e( "<div class='space-box' data-pages='".wp_json_encode( $this->getPages() )."' data-behaviour='space-pages'></div>" );

	}


}

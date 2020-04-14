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

		$this->json( 'pages', $this->getPages() );

		_e( "<div class='space-box' data-behaviour='space-pages'></div>" );

	}


}

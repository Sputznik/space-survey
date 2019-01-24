<?php
/*
* IDEA IS TO CREATE A SORTABLE REPEATABLE FIELD THAT CAN BE CALLED FROM THE SURVEY FORM
*/
	
class SPACE_RESULTS_FORM extends SPACE_FORM{
	
	function __construct(  ){
		
	}
	
	/*
	* Template: called from display() of space-survey/admin/class-space-checkbox-form.php
	* Idea: to create a repeatable field to add pages in a survey
	*/
	function display(){	
		
		
		//_e('hi');
		//_e( "<div class='space-box' data-pages='".wp_json_encode( $this->getPages() )."' data-behaviour='space-pages'></div>" );
		
	}
		
		
}
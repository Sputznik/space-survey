<?php
/*
* IDEA IS TO CREATE A SORTABLE REPEATABLE FIELD THAT CAN BE CALLED FROM THE QUESTIONS FORM
*/
	
class SPACE_CHOICE_FORM extends SPACE_FORM{
	
	var $choices;
	
	function __construct( $choices ){
		$this->setChoices( $choices );
	}
	
	function getChoices(){ return $this->choices; }
	function setChoices( $choices ){ $this->choices = $choices; }
	
	/*
	* Template: called from display() of space-survey/admin/class-space-checkbox-form.php
	* Idea: to create a repeatable field to add choices in a question
	*/
	function display(){
		
		_e( "<div class='space-box' data-choices='".wp_json_encode( $this->getChoices() )."' data-behaviour='space-choices'></div>" );
		
		//include( 'templates/choice-form.php' );
			
	}
		
		
}
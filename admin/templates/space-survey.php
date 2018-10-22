<h1 class='wp-heading-inline'>Surveys</h1>
<a href="?page=space-survey-edit" class="page-title-action">Add New</a>
<?php

	$data = array();

	$spaceSurveyTable = new SPACE_SURVEY_LIST_TABLE;
	
	$spaceSurveyTable->prepare_items();
	$spaceSurveyTable->display();
	
?>
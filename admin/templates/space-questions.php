<h1 class='wp-heading-inline'>Questions</h1>
<a href="?page=space-question-edit" class="page-title-action">Add New</a>
<?php

	$data = array();

	$spaceQuestionTable = new SPACE_QUESTION_LIST_TABLE;
	
	$spaceQuestionTable->prepare_items();
	$spaceQuestionTable->display();
	
?>
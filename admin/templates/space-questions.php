<?php

	$data = array();

	$spaceQuestionTable = new SPACE_QUESTION_LIST_TABLE;
	
	$spaceQuestionTable->prepare_items();
	$spaceQuestionTable->display();
	
?>
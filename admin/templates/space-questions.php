<h1 class='wp-heading-inline'>Questions</h1>
<a href="?page=space-question-edit" class="page-title-action">Add New</a>
<form action="<?php _e( admin_url( 'admin.php?page='.$_GET['page'] ) );?>" method="POST">
<?php

	$data = array();

	$spaceQuestionTable = new SPACE_QUESTION_LIST_TABLE;
	
	$spaceQuestionTable->prepare_items();
	
	$spaceQuestionTable->search_box( 'Search', 'search-id' );
	
	$spaceQuestionTable->display();
	
?>
</form>
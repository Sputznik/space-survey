<?php
	$url = admin_url( 'admin.php?page='.$_GET['page'] );

	$url_has_changed = false;

	if( isset( $_POST['filter_by_survey'] ) && $_POST['filter_by_survey'] ){
		$url .= '&survey='.$_POST['filter_by_survey'];
		$url_has_changed = true;
	}

	if( isset( $_POST['s'] ) && $_POST['s']  ){
		$url .= '&s='.$_POST['s'];
		$url_has_changed = true;
	}

	if( $url_has_changed ){
		_e("<script>location.href='$url';</script>");
	}

?>
<h1 class='wp-heading-inline'>Questions</h1>
<a href="?page=space-question-edit" class="page-title-action">Add New</a>
<form action="<?php _e( $url );?>" method="POST" data-behaviour="space-form-table">
<?php

	$data = array();

	$spaceQuestionTable = new SPACE_QUESTION_LIST_TABLE;

	$spaceQuestionTable->prepare_items();

	$spaceQuestionTable->search_box( 'Search', 'search-id' );

	$spaceQuestionTable->display();

?>
</form>

<?php

	// MULTIPLE DELETION
	if( isset( $_POST['action'] ) && $_POST['action'] == 'trash' && isset( $_POST['guests'] ) && is_array( $_POST['guests'] ) && count( $_POST['guests'] ) ){
		$guest_ids = $_POST['guests'];
		SPACE_DB_GUEST::getInstance()->delete_rows( $_POST['guests'] );
	}

	$url = admin_url( 'admin.php?page='.$_GET['page'] );
	$url_has_changed = false;

	// CHECK FOR SURVEY & FILTERS SELECTION
	if( isset( $_POST['survey'] ) && is_array( $_POST['survey'] ) && isset( $_POST['survey']['id'] ) && $_POST['survey']['id'] ){
		$url .= '&survey='.$_POST['survey']['id'];
		$url_has_changed = true;

		// FILTER RULES - CONVERT POST DATA INTO URL STRING
		$filterChoices = array();
		$rules = isset( $_POST['rules'] ) ? $_POST['rules'] : array();
		foreach( $rules as $slug => $rule ){
			if( isset( $rule['value'] ) && $rule['value'] ){
				array_push( $filterChoices, $rule['value'] );
			}
		}
		if( count( $filterChoices ) ){
			$choices_str = implode( ',', $filterChoices );
			$url .= "&choices=$choices_str";
		}
	}

	// CHECK FOR SEARCH PARAMETER
	if( isset( $_POST['s'] ) && $_POST['s']  ){
		$url .= '&s='.$_POST['s'];
		$url_has_changed = true;
	}

	// IF THE URL HAS CHANGED THEN DO A REDIRECT
	if( $url_has_changed ){ _e("<script>location.href='$url';</script>"); }

	$survey = array( 'id' => 0, 'title' => '' );

	if( isset( $_GET['survey'] ) && $_GET['survey'] ){
		$survey_row = SPACE_DB_SURVEY::getInstance()->get_row( $_GET['survey'] );
		if( $survey_row && isset( $survey_row->ID ) && isset( $survey_row->post_title ) ){
			$survey['id'] = $survey_row->ID;
			$survey['title'] = $survey_row->post_title;
		}
	}

	// CONVERT THE CHOICES PASSED IN THE URL INTO A JSON BASED DATA OF RULES
	$filterChoicesArr = isset( $_GET['choices'] ) ? explode( ',', $_GET['choices'] ) : array();
	$question_db = SPACE_DB_QUESTION::getInstance();
	$rules = array();
	foreach( $filterChoicesArr as $choice_id ){
		$question = $question_db->getQuestionFromChoice( $choice_id );
		$temp_rule = array(
			'question'	=> $question->ID,
			'value'			=> $choice_id,
			'data'			=> array(
				'id'			=> $question->ID,
				'label'		=> $question->title,
				'value'		=> $question->title,
				'choices'	=> $question->choices
			)
		);
		array_push( $rules, wp_unslash( $temp_rule ) );
	}
	SPACE_UTIL::getInstance()->browserData( 'rules', $rules );

?>
<h1 class='wp-heading-inline'>Responses</h1>
<a href="#" class="page-title-action" data-behaviour="responses-filter-btn">Filter</a>
<form action="<?php _e( $url );?>" method="POST" data-behaviour="space-form-table">
	<div class='filters-box <?php if( !$survey['id'] ){ _e('hide');}?>'>
		<?php

			$field = array(
				'label'								=> 'Choose Survey',
				'slug'								=> 'survey[id]',
				'placeholder'					=> 'Type title of the survey here',
				'url'									=> admin_url( 'admin-ajax.php?action=surveys_json' ),
				'value'								=> $survey['id'],
				'autocomplete_value'	=> $survey['title'],
				'autocomplete_slug'		=> 'survey[title]'
			);

		?>
		<div class='survey-autocomplete' data-behaviour='space-autocomplete' data-field='<?php _e( wp_json_encode( $field ) );?>'></div>
		<div data-behaviour='space-export-filters'></div>
		<p><input type='submit' name='filters' class='button button-primary button-large' value='Apply Filters'></p>
	</div>
	<?php

		$data = array();

		$spaceResponsesTable = new SPACE_RESPONSES_LIST_TABLE;

		$spaceResponsesTable->prepare_items( $survey['id'], $filterChoicesArr );

		$spaceResponsesTable->search_box( 'Search', 'search-id' );

		$spaceResponsesTable->display();

	?>
</form>
<style>
	.filters-box{
		margin-top: 20px;
		margin-bottom: 20px;
		padding: 10px;
		position: relative;
		background: #fff;
		border: 10px solid #c2e1f5;
	}
	.filters-box:after, .filters-box:before {
		bottom: 100%;
		left: 120px;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
	}

	.filters-box:after {
		border-color: rgba(136, 183, 213, 0);
		border-bottom-color: #c2e1f5;
		border-width: 12px;
		margin-left: -12px;
	}
	.filters-box:before {
		border-color: rgba(194, 225, 245, 0);
		border-bottom-color: #c2e1f5;
		border-width: 26px;
		margin-left: -26px;
	}
	.filters-box.hide{ display: none; }
	.filters-box #space-filters{ max-width: 600px; }
	.filters-box label{ display: block; margin-bottom: 10px; }
	.filters-box .survey-autocomplete .ui-autocomplete-input{ min-width: 300px; }

	th#tot_questions{ width: 120px; }
	th#survey{ width: 250px; }
	th#created_on{ width: 180px; }
	th#ipaddress{ width: 120px; }
</style>

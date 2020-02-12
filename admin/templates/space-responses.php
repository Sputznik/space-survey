<?php

	// MULTIPLE DELETION
	if( isset( $_POST['action'] ) && $_POST['action'] == 'trash' && isset( $_POST['guests'] ) && is_array( $_POST['guests'] ) && count( $_POST['guests'] ) ){
		$guest_ids = $_POST['guests'];
		SPACE_DB_GUEST::getInstance()->delete_rows( $_POST['guests'] );
	}

	$url = admin_url( 'admin.php?page='.$_GET['page'] );
	$url_has_changed = false;

	// FILTER RULES
	$filterChoices = array();
	$rules = isset( $_POST['rules'] ) ? $_POST['rules'] : array();
	foreach( $rules as $slug => $rule ){
		$rules[ $slug ][ 'data' ] = json_decode( stripslashes( $rule['data'] ), true );
		if( isset( $rule['value'] ) && $rule['value'] ){
			array_push( $filterChoices, $rule['value'] );
		}
	}
	//echo "<pre>";
	//print_r( $rules );
	//echo "</pre>";

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
<h1 class='wp-heading-inline'>Responses</h1>
<a href="#" class="page-title-action" data-behaviour="responses-filter-btn">Filter</a>
<form action="<?php _e( $url );?>" method="POST" data-behaviour="space-form-table">
	<div class='filters-box <?php if( !$_POST ){ _e('hide');}?>'>
		<?php

			//echo "<pre>";
			//print_r( $_POST );
			//echo "</pre>";

			$survey = isset( $_POST['survey'] ) ? $_POST['survey'] : array( 'id' => 0, 'title' => '' );
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
		<div data-behaviour='space-export-filters' data-rules='<?php _e( wp_json_encode( $rules ) );?>'></div>
		<p><input type='submit' name='filters' class='button button-primary button-large' value='Apply Filters'></p>
	</div>
<?php

	$data = array();

	$spaceResponsesTable = new SPACE_RESPONSES_LIST_TABLE;

	$spaceResponsesTable->prepare_items( $survey['id'], $filterChoices );

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
	
	.filters-box label{
		display: block;
		margin-bottom: 10px;
	}
	.filters-box .survey-autocomplete .ui-autocomplete-input{
		min-width: 300px;
	}
</style>

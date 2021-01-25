<?php

	$items_per_batch = 20;

  _e( "<h1 class='wp-heading-inline'>Export</h1>" );

  $survey_id = 0;
  if( isset( $_GET['survey_id'] ) && $_GET['survey_id'] ){
    $survey_id = $_GET['survey_id'];
  }

  if( ! $survey_id ){
    echo "<p>For export to function correctly, the survey needs to be selected first.</p>";
    wp_die();
  }

  $filterChoices = array();
  if( isset( $_GET['choices'] ) && $_GET['choices'] ){
    $filterChoices = explode( ',', $_GET['choices'] );
  }

  $search = "";
  if( isset( $_GET['search'] ) && $_GET['search'] ){
    $search = $_GET['search'];
  }

	$hide_zero_attempted = false;
	if( isset( $_GET['hide-zero-attempted'] ) && $_GET['hide-zero-attempted'] == '1' ){
		$hide_zero_attempted = true;
	}

  $survey_db = SPACE_DB_SURVEY::getInstance();
  $survey = $survey_db->get_row( $survey_id );
  $queries = $survey_db->getResponsesQuery( $survey_id, $filterChoices, $hide_zero_attempted, $search );

  $totGuests = $survey_db->get_var( $queries['count'] );

  //echo "<pre>";
  //print_r( $queries );
  //echo "</pre>";

  if( $survey && $totGuests ){

    // CALCULATION OF THE BATCH REQUESTS
    $batches = (int) ( $totGuests / $items_per_batch );
    $batches = $batches + 1;

    // SECONDARY TITLE
    _e( "<p>Export <b>$totGuests</b> responses for <b>".$survey->post_title."</b> in CSV format</p>" );

    $batch_process = SPACE_BATCH_PROCESS::getInstance();

    $batch_process->process( array(
      'title'			   => '',
      'desc'			   => '',
      'batches'		   => $batches,
      'btn_text' 		 => 'Generate CSV',
      'batch_action' => 'export',
      'params'		   => array(
        'survey_id'		  			=> $survey->ID,
        'filterChoices'				=> implode( ',', $filterChoices ),
				'hide-zero-attempted' => $hide_zero_attempted ? 1 : 0,
        'per_page'		  			=> $items_per_batch
      )
    ) );

  }
  else{

    // SECONDARY TITLE
    _e( "<p>No information avalilable for the selected survey.</p>" );

  }

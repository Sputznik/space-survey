<?php
	
	
	$survey_db = SPACE_DB_SURVEY::getInstance();
	
	$survey_id = isset( $_REQUEST['survey'] ) ? $_REQUEST['survey'] : 0;
	
	// MAIN HEADING
	_e( "<h1 class='wp-heading-inline'>Export</h1>" );
	
	// CHECKING IF SURVEY HAS ALREADY SELECTED
	// IF NOT THEN MAKE THE USER SELECT FROM A DROPDOWN
	if( ! $survey_id ):
		
		_e( "<p>Export responses for a particular survey in CSV format</p>" );
		
		/*
		* GET THE LAST 10 SURVEYS
		*/
		$surveys = $survey_db->results( 1, 10, array(
			'col_formats'	=> array(
				'post_type'		=> '%s',
				'post_status'	=> '%s',
			),
			'col_values'	=> array( 'space_survey', 'publish' ),
			'operator'		=> '='
		) );
?>
		<form method="POST">
			<label>Select Survey</label><br>
			<select name='survey'>
				<?php foreach( $surveys['results'] as $survey ):?>
				<option value='<?php _e( $survey->ID );?>'><?php _e( $survey->post_title );?></option>
				<?php endforeach;?>
			</select>
			<div data-behaviour='space-export-filters'></div>
			<p><input type="submit" name="publish" class="button button-primary button-large" value="Generate CSV"></p>
		</form>
<?php 
	
	else:
		
		// SURVEY HAS ALREADY BEEN SELECTED AND THE ID HAS BEEN PASSED
		
		
		// TAKE OUT THE CHOICES FROM THE FILTERS
		$filterChoices = array();
		if( isset( $_POST['rules'] ) && is_array( $_POST['rules'] ) ){
			foreach( $_POST['rules'] as $filter ){
				if( isset( $filter['value'] ) && $filter['value'] ){
					array_push( $filterChoices, $filter['value'] );
				}
			}
		}
		
		
		// DB OPERATIONS
		$survey_db = SPACE_DB_SURVEY::getInstance();
		$survey = $survey_db->get_row( $survey_id );
		$totGuests = $survey_db->totalGuests( $survey_id, $filterChoices );
		$guests = $survey_db->listGuestIDs( $survey_id, $filterChoices );
		$totExportGuests = $guests['num_rows'];
		
		if( $survey && $totGuests ){
			
			// CALCULATION OF THE BATCH REQUESTS
			$batches = (int) ( $totGuests / 100 );
			$batches = $batches + 1;
			
			// SECONDARY TITLE
			_e( "<p>Export <b>$totExportGuests/$totGuests</b> responses for <b>".$survey->post_title."</b> in CSV format</p>" );
			
			$batch_process = SPACE_BATCH_PROCESS::getInstance();
			
			$batch_process->process( array(
				'title'			=> '',
				'desc'			=> '',
				'batches'		=> $batches,
				'btn_text' 		=> 'Generate CSV', 
				'batch_action' 	=> 'export',
				'params'		=> array(
					'survey_id'		=> $survey_id,
					'filterChoices'	=> implode( ',', $filterChoices ),
					'per_page'		=> 100
				)
			) );
			
		}
		else{
			
			// SECONDARY TITLE
			_e( "<p>No information avalilable for the selected survey.</p>" );
			
		}
	endif;
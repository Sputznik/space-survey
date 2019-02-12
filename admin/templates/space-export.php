<?php
	
	$survey_db = SPACE_DB_SURVEY::getInstance();
	
	$survey_id = isset( $_REQUEST['survey'] ) ? $_REQUEST['survey'] : 0;
	
?>
<h1 class='wp-heading-inline'>Export</h1>
<?php if( ! $survey_id ): ?>
<p>Export responses for a particular survey in CSV format</p>
<?php
	/*
	* GET THE LAST 10 SURVEYS
	*/
	$surveys_data = $survey_db->results( 1, 10, array(
		'col_formats'	=> array(
			'post_type'		=> '%s',
			'post_status'	=> '%s',
		),
		'col_values'	=> array( 'space_survey', 'publish' ),
		'operator'		=> '='
	) );
	
	$surveys = array();
	
	if( isset( $surveys_data['results'] ) ){
		foreach( $surveys_data['results'] as $survey ){
			$temp_data = array(
				'id'	=> $survey->ID,
				'title'	=> $survey->post_title
			);
			array_push( $surveys, $temp_data );
		}
	}
	
?>
<form method="POST">
	<div class='form-field'>
		<label>Select Survey</label><br>
		<select name='survey'>
			<?php foreach( $surveys as $survey ):?>
			<option value='<?php _e( $survey['id'] );?>'><?php _e( $survey['title'] );?></option>
			<?php endforeach;?>
		</select>
	</div>
	<p><input type="submit" name="publish" class="button button-primary button-large" value="Generate CSV"></p>
</form>
<?php else:?>
<?php
	
	// SURVEY HAS ALREADY BEEN SELECTED AND THE ID HAS BEEN PASSED
	
	$survey_db = SPACE_DB_SURVEY::getInstance();
	
	$survey = $survey_db->get_row( $survey_id );
	
	$totGuests = $survey_db->totalGuests( $survey_id );
	
	if( $survey && $totGuests ){
	
		$batches = (int) ( $totGuests / 100 );
		$batches = $batches + 1;
		
		_e( "<p>Export <b>".$totGuests."</b> responses for <b>".$survey->post_title."</b> in CSV format</p>" );
		
		$batch_process = SPACE_BATCH_PROCESS::getInstance();
		
		echo $batch_process->process( array(
			'title'			=> '',
			'desc'			=> '',
			'batches'		=> $batches,
			'btn_text' 		=> 'Generate CSV', 
			'batch_action' 	=> 'export',
			'survey'		=> $survey_id
		) );
		
	}
	else{
		
		_e( "<p>No information avalilable for the selected survey.</p>" );
		
	}
	
?>
<?php endif; ?>
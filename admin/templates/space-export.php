<?php
	
	$survey_db = SPACE_DB_SURVEY::getInstance();
	
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
<h1 class='wp-heading-inline'>Export</h1>
<p>Export responses for a particular survey in CSV format</p>
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

<?php
	
	/*
	echo "<pre>";
	print_r( $_POST );
	echo "</pre>";
	
	$survey_id = $_POST['survey'];
	$survey_db = SPACE_DB_SURVEY::getInstance();
	
	$data = $survey_db->getGuests( $survey_id );
	
	echo "<pre>";
	print_r( $data );
	echo "</pre>";
	*/
	
	//$file = fopen("contacts.csv","w");
	
	//$export = SPACE_EXPORT::getInstance();
	
	//$export->output( 41290 );
	
	
	$batch_process = SPACE_BATCH_PROCESS::getInstance();
	
	echo $batch_process->process( array(
		
	) );
	
	
	
?>
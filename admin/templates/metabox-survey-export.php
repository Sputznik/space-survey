<div style="padding: 10px;">
	<div data-behaviour='space-survey-import-export' data-survey='<?php _e( $survey_id );?>'>
		<button class='button' type="button">Export</button>
		<p class='space-loader'>Waiting for the download link<span class="spinner is-active"></span></p>
		<!--p class='help'>Make sure the survey is updated as the export will contain the last updated version.</p-->
	</div>
<?php
	$survey_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

	if( $survey_id ){
		$survey_db = SPACE_DB_SURVEY::getInstance();
		$urls_util = SPACE_URLS::getInstance();
		$totalGuests = $survey_db->totalGuests( $survey_id );
		_e( "<ul style=''>" );
		_e( "<li style='display: inline-block;'><a href='" . $urls_util->csvs( $survey_id ) . "'>Generate CSV</a></li>" );
		_e( "<li style='margin-left:15px;display: inline-block;'><a href='" . $urls_util->responses( $survey_id ) . "'>View Responses ($totalGuests)</a></li>" );
		_e( "</ul>");
	}
?>
</div>

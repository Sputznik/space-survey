<?php

	$survey_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

?>
<div data-behaviour='space-survey-import-export' data-survey='<?php _e( $survey_id );?>' style="margin-top: 20px;">
	<input name="survey-file" type="file" />
	<p class='help'>After selecting the file, update the survey to see the import being reflected on the survey.</p>
	<?php if( $survey_id ):?>
	<br>
	<button class='button' type="button">Export</button>
	<p class='space-loader'>Waiting for the download link<span class="spinner is-active"></span></p>
	<p class='help'>Make sure the survey is updated before you export. The export will contain the last updated version.</p>
	<?php endif; ?>
</div>

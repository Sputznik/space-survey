<div data-type='<?php _e( $question->type );?>' class='space-question'>
	<h5><?php _e( $question->title );?></h5>
	<p class='space-desc'><?php _e( $question->description );?></p>
	<?php $this->choices_html( $question );?>		
</div>
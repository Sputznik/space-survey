<select name='<?php _e( $question->ID );?>'>
	<?php foreach( $question->choices as $choice ):?>
	<option value='<?php _e( $choice->ID );?>'><?php _e( $choice->title );?></option>
	<?php endforeach;?>
</select>
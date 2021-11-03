<?php

	$defaultOption = 'Please click here to select';
	$question_db = SPACE_DB_QUESTION::getInstance();
	$questionMeta = $question_db->getMetaInfo( wp_unslash( $question ) );
	if( isset( $questionMeta[ 'defaultDropdownOption' ] ) ){
		$defaultOption = $questionMeta[ 'defaultDropdownOption' ];
	}

?>

<select name='<?php _e( $this->get_input_name( $question->ID ) );?>'>
	<option value="" selected><?php _e( $defaultOption );?></option>
	<?php foreach( $question->choices as $choice ):?>
	<option value='<?php _e( $choice->ID );?>'><?php _e( $choice->title );?></option>
	<?php endforeach;?>
</select>

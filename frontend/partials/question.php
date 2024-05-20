<?php

	$required_questions = $this->survey->required_questions;

	$question->rules = array();
	if( is_array( $this->survey->rules ) && isset( $this->survey->rules[ $question->ID ] ) ){
		foreach( $this->survey->rules[ $question->ID ] as $rule ){
			unset( $rule['data'] );
			array_push( $question->rules, $rule );
		}
	}

	// FIGURE CLASS FOR THE QUESTION WRAPPER
	$question_class = 'space-question';
	if( in_array( $question->ID, $required_questions ) ){
		$question_class .= ' required';
		$question->title .= ' <span class="required-sign">*</span>';
	}
	else{
		$question_class .= ' not-required';

		if( count( $question->rules ) ){
			$question_class .= ' hide';
		}
	}

	//echo $question->meta;

	//_e( wp_json_encode( wp_unslash( unserialize( $question->meta ) ) ) );
	$question_db = SPACE_DB_QUESTION::getInstance();
	$questionMeta = $question_db->getMetaInfo( wp_unslash( $question ) );

?>
<div id='<?php _e( 'q'.$question->ID );?>' data-meta='<?php _e( wp_json_encode( $questionMeta ) );?>' data-rules='<?php _e( wp_json_encode( $question->rules ) );?>' data-type='<?php _e( $question->type );?>' class='<?php _e( $question_class );?>'>
	<h5><?php _e( $question->title );?></h5>
	<div class='space-desc'><?php _e( $question->description );?></div>
	<?php echo $this->choices_html( $question );?>
	<?php $this->question_type_field( $question );?>
</div>

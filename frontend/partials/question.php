<?php
	
	$required_questions = $this->survey->required_questions;
	
	// FIGURE CLASS FOR THE QUESTION WRAPPER
	$question_class = 'space-question';
	if( in_array( $question->ID, $required_questions ) ){
		$question_class .= ' required';
	}
	
	//print_r( $this->survey->rules );
	
	$question->rules = array();
	if( is_array( $this->survey->rules ) && isset( $this->survey->rules[ $question->ID ] ) ){
		foreach( $this->survey->rules[ $question->ID ] as $rule ){
			unset( $rule['data'] );
			array_push( $question->rules, $rule );
		}
	}
	
?>
<div id='<?php _e( 'q'.$question->ID );?>' data-rules='<?php _e( wp_json_encode( $question->rules ) );?>' data-type='<?php _e( $question->type );?>' class='<?php _e( $question_class );?>'>
	<h5><?php _e( $question->title );?></h5>
	<div class='space-desc'><?php _e( $question->description );?></div>
	<?php echo $this->choices_html( $question );?>
	<?php $this->question_type_field( $question );?>
</div>
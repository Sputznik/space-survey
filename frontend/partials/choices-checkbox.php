<?php
	$questionMeta = unserialize( $question->meta );

	$behaviours = array();
	if( isset( $questionMeta['nullFlag'] ) && $questionMeta['nullFlag'] ){
		array_push( $behaviours, 'space-null-choices' );
	}

	if( isset( $questionMeta['limitFlag'] ) && $questionMeta['limitFlag'] ){
		array_push( $behaviours, 'space-limit-choices' );
	}

?>
<ul class='space-choices' data-behaviour='<?php echo implode(' ', $behaviours );?>'>
	<?php foreach( $question->choices as $choice ):?>
	<li class='space-choice'>
		<label>
			<input type='checkbox' name='<?php _e( $this->get_input_name( $question->ID ) );?>[]' value='<?php _e( $choice->ID );?>' />
			<?php _e( $choice->title );?>
		</label>
	</li>
	<?php endforeach;?>
	<?php if( isset( $questionMeta['otherFlag'] ) && isset( $questionMeta['otherText'] ) && !empty( $questionMeta['otherText'] ) ):?>
	<li class='space-choice-other'>
		<!--label style="margin-right: 10px;"><?php _e( 'Other' );?></label-->
		<input type='text' name='<?php _e( $this->get_input_name( $question->ID, 'other' ) );?>' placeholder='<?php _e( $questionMeta['otherText'] );?>' value='' />
	</li>
	<?php endif;?>
</ul>

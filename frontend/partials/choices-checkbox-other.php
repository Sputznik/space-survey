<ul class='space-choices'>
	<?php foreach( $question->choices as $choice ):?>
	<li class='space-choice'>
		<label>
			<input type='checkbox' name='<?php _e( $this->get_input_name( $question->ID ) );?>[]' value='<?php _e( $choice->ID );?>' />
			<?php _e( $choice->title );?>
		</label>
	</li>
	<?php endforeach;?>
	<li class='space-choice'>
		<label style="margin-right: 10px;"><?php _e( 'Other' );?></label>
		<input type='text' name='<?php _e( $this->get_input_name( $question->ID, 'other' ) );?>' value='' />
	</li>
</ul>

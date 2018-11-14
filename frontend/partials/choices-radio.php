<ul class='space-choices'>
	<?php foreach( $question->choices as $choice ):?>
	<li class='space-choice'>
		<label>
			<input type='radio' name='<?php _e( $this->get_input_name( $question->ID ) );?>' value='<?php _e( $choice->ID );?>' />	
			<?php _e( $choice->title );?>
		</label>
	</li>
	<?php endforeach;?>
</ul>
<ul class='space-choices'>
	<?php foreach( $question->choices as $choice ):?>
	<li class='space-choice rank-field'>
		<label class="choice-type">
			<input type='checkbox' data-id="<?php _e( $choice->ID );?>" name='<?php _e( $this->get_input_name( $question->ID ) );?>[]' value='<?php _e( $choice->ID );?>' />
			<?php _e( $choice->title );?>
		</label>
		<label class="rank" id="choice-<?php _e( $choice->ID );?>">
			<span>#</span>
		</label>
	</li>
	<?php endforeach;?>
</ul>

<ul class='space-choices'>
	<?php foreach( $question->choices as $choice ):?>
	<li class='space-choice'>
		<label>
			<input type='checkbox' name='<?php _e( $question->ID );?>' value='<?php _e( $choice->ID );?>' />	
			<?php _e( $choice->title );?>
		</label>
	</li>
	<?php endforeach;?>
</ul>
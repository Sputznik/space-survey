<ul class='space-choices'>
	<?php foreach( $question->choices as $choice ):?>
	<li class='space-choice rank-field'>
		<label class="choice-type">
			<input type='checkbox' name='<?php _e( $this->get_input_name( $question->ID ) );?>[]' value='<?php _e( $choice->ID );?>' />
			<?php _e( $choice->title );?>
		</label>
		<label class="rank" id="choice-<?php _e( $choice->ID );?>">
			<span>#</span>
		</label>
	</li>
	<?php endforeach;?>
</ul>

<style media="screen">
.space-choice.rank-field{
	display:block;
	user-select:none;
}
.space-choice.rank-field > .choice-type{
	max-width:400px;
	width:100%;
}
.space-choice.rank-field > .rank{
  width: 46px;
  height: 46px;
	text-align: center;
	user-select:none;
	margin:0;
}
.space-choice.rank-field > .rank > span{
	display: block;
	line-height: 28px;
}
</style>

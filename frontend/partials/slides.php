<div data-behaviour='space-slides' data-id='<?php _e( $survey_id );?>'>
	<form>
		<?php wp_nonce_field('save', 'space_survey');?>
		<input type="hidden" name="survey_id" value="<?php _e( $survey_id );?>" />
		<input type="hidden" name="guest_id" value="0" />
		<?php $i = 0; foreach( $pages as $page ):?>
		<?php
			$slide_class = 'space-slide';
			if( !$i ){ $slide_class .= ' active'; }
		?>
		<div class='<?php _e( $slide_class );?>'>
			<?php $space_frontend->page_html( $page );?>
			<ul class='space-list space-list-inline'>
				<?php
				$end = count($pages); //count total number of pages
				if($i===0){?>
				<li>
					<button data-behaviour='space-slide-prev' style="display:none;">Go Back</button>
				</li>
				<?php } else{?>
				<li>
					<button data-behaviour='space-slide-prev' style="display:block;">Go Back</button>
				</li>
			<?php }?>
			<?php if($i == $end-1){?>
				<li>
					<button data-behaviour='space-slide-next' style="display:none;">Continue</button>
				</li>
			<?php } else{?>
				<li>
					<button data-behaviour='space-slide-next' style="display:block;">Continue</button>
				</li>
			<?php }?>
			</ul>
		</div>
		<?php $i++; endforeach;?>
	</form>
</div>

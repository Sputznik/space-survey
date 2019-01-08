<?php
	
	$survey_id = $this->survey->ID;
	$pages = $this->survey->pages;
	
?>
<div data-behaviour='space-slides' data-id='<?php _e( $survey_id );?>'>
	<form>
		<?php wp_nonce_field('save', 'space_survey');?>
		<input type="hidden" name="survey_id" value="<?php _e( $survey_id );?>" />
		<input type="hidden" name="guest_id" value="0" />
		<?php $i = 0; $num_pages = count( $pages ); foreach( $pages as $page ):?>
		<?php
			$slide_class = 'space-slide';
			if( !$i ){ $slide_class .= ' active'; }
		?>
		<div class='<?php _e( $slide_class );?>'>
			<?php echo $this->page_html( $page );?>
			<ul class='space-list space-list-inline'>
				<?php if( $i > 0 ):?>	
				<li><button data-behaviour='space-slide-prev'>Go Back</button></li>
				<?php endif;?>
				<?php if( $i != $num_pages-1 ):?>
				<li><button data-behaviour='space-slide-next'>Continue</button></li>
				<?php endif;?>
			</ul>
		</div>
		<?php $i++; endforeach;?>
	</form>
</div>

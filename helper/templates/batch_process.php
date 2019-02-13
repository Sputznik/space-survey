<div data-url='<?php echo $url;?>' data-params='<?php echo wp_json_encode( $atts['params'] );?>' data-behaviour='space-batch' data-action='<?php echo $atts['batch_action'];?>' data-batches='<?php echo $atts['batches'];?>' data-btn='<?php echo $atts['btn_text'];?>'>
	<?php if( $atts['title'] ):?><h3><?php _e( $atts['title'] );?></h3><?php endif;?>
	<?php if( $atts['desc'] ):?><p><?php _e( $atts['desc'] );?></p><?php endif;?>
	<div class='space-progress-container'>
		<div class='space-progress'></div>
	</div>
	<button class='btn btn-default'><?php echo $atts['btn_text'];?></button>
	<p class='result'></p>
	<div class='logs-container'>
		<h5>Logs</h5>
		<ul class='logs'></ul>
	</div>
</div>
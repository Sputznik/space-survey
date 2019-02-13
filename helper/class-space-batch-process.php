<?php
/**
* COPIED THE CODEBASE FROM https://github.com/samvthom16/Orbit-batch-process
* BATCH PROCESSING BY SENDING HTTP REQUESTS THROUGH AJAX
*/
	
class SPACE_BATCH_PROCESS extends SPACE_BASE{
		
	var $slug;
		
	function __construct(){
			
		$this->slug = 'space_batch_process';
			
		/* CREATE SHORTCODE */
		add_shortcode( $this->slug, array( $this, 'shortcode' ) );
			
		/* SAMPLE ACTION HOOK FOR AJAX CALL */
		add_action('space_batch_action_default', function(){
				
			$users = array( 'Samuel', 'Jay', 'Dennis', 4, 5, 6, 7, 8, 9, 10 );
				
			echo $users[ $_GET['space_batch_step'] - 1 ];
				
			// echo "AJAX ".$_GET['space_batch_step']." ".$_GET['space_batch_action'];
			
		});
		
		/* AJAX CALLBACK */
		add_action('wp_ajax_'.$this->slug, array( $this, 'ajax' ) );
			
	}
		
	/* SHORTCODE FUNCTION */
	function process( $atts ){
			
		/* CREATE ATTS ARRAY FROM DEFAULT PARAMETERS IN THE SHORTCODE */
		$atts = shortcode_atts( array(
			'title'			=> 'Title of the process',
			'desc'			=> 'Description of the process',
			'batches' 		=> '10', 
			'btn_text' 		=> 'Process Request', 
			'batch_action' 	=> 'default',
			'params'		=> array()
		), $atts, $this->slug );
				
		$url = admin_url( 'admin-ajax.php' ) . '?action=' . $this->slug;
			
		include "templates/batch_process.php";
	
	}
		
	/* AJAX CALLBACK */
	function ajax(){
		
		if( isset( $_GET['space_batch_action'] ) ){
			do_action('space_batch_action_'.$_GET['space_batch_action']);
		}
		
		wp_die();
	}
		
		
	
		
}

// CREATE AN INSTANCE FOR THE AJAX CALLBACK TO BE HANDLED
SPACE_BATCH_PROCESS::getInstance();
	
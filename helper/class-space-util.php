<?php
/*
* UTIL HELPER CLASS
*/

class SPACE_UTIL extends SPACE_BASE{

	function browserData( $type, $data ){
		?>
		<script type="text/javascript">
		if( window.browserData === undefined || window.browserData[ '<?php _e( $type );?>' ] === undefined ){
			var data = window.browserData = window.browserData || {};
			browserData[ '<?php _e( $type );?>' ] = <?php echo json_encode( wp_unslash( $data ) );?>;
		}
		</script>
		<?php
	}

	function test( $data ){
		echo "<pre>";
		print_r( $data );
		echo "</pre>";
	}


}

// CREATE AN INSTANCE FOR THE AJAX CALLBACK TO BE HANDLED
SPACE_UTIL::getInstance();

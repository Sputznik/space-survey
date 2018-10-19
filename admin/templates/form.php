<?php 
	
	$page = $_GET['page'];
	
	$action_data = array( 'form' => $this, 'fields' => $form_fields );
?>
<form action="" method="post">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			
			<div id="post-body-content" style="position: relative;">
				<?php do_action( $page.'-body-div', $action_data );?>
			</div><!-- /post-body-content -->

			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable ui-sortable-disabled" style="">
					<div id="submitdiv" class="postbox ">
						<h2 class="hndle ui-sortable-handle"><span>Settings</span></h2>
						<div class="inside">
							<div class="submitbox" id="submitpost">
								<div style='padding:0 15px 15px 15px;'>
									<?php do_action( $page.'-settings-div', $action_data );?>
								</div>
								<div id="major-publishing-actions">
									<div id="delete-action">
										<?php do_action( $page.'-delete-div', $action_data );?>
									</div>
									<div id="publishing-action">
										<?php do_action( $page.'-publish-div', $action_data );?>
									</div>
									<div class="clear"></div>
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div><!-- /post-body -->
		<br class="clear">
	</div>
</form>
<style>
	.big-text{
		padding: 3px 8px;
		font-size: 1.7em;
		line-height: 100%;
		height: 1.7em;
		width: 100%;
		outline: 0;
	}
	#post-body-content input{
		margin-bottom: 20px;
	}
</style>
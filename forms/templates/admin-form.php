<?php
/*
* Template: called from display() of space-survey/admin/class-space-form.php
* Idea: to create a reusable dashboard that looks similar to `Edit Post`
*/
?>
<h1><?php _e( $this->getPageTitle() );?></h1>
<form action="<?php _e( $_SERVER['REQUEST_URI'] );?>" method="POST" enctype="multipart/form-data">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">

			<div id="post-body-content" style="position: relative;">
				<?php $this->do_action('body-div');?>
			</div><!-- /post-body-content -->

			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable ui-sortable-disabled" style="">
					<div id="submitdiv" class="postbox ">
						<h2 class="hndle ui-sortable-handle"><span>Settings</span></h2>
						<div class="inside">
							<div class="submitbox" id="submitpost">
								<div style='padding:0 15px 15px 15px;'>
									<?php $this->do_action('settings-div');?>
								</div>
								<div id="major-publishing-actions">
									<div id="delete-action">
										<?php $this->do_action('delete-div');?>
									</div>
									<div id="publishing-action">
										<?php $this->do_action('publish-div');?>
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

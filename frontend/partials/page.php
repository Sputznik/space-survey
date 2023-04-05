<div class='space-title page-title'><?php _e( $page->title );?></div>
<div class='space-desc page-desc'><?php _e( $page->description );?></div>
<div class='space-questions'>
<?php
	foreach( $page->questions as $question ){
		echo $this->question_html( $question );
	}
?>
</div>

<?php
	
	$form = new SPACE_FORM;
	
	/* CONTENT IN THE MAIN BODY */
	add_action( 'space-question-edit-body-div', function( $form ){
		
		$form->display_field( array(
			'placeholder'	=> 'Enter question here',
			'slug'			=> 'question',
			'type'			=> 'big-text',
		) );
		
		$form->display_field( array(
			'placeholder'	=> 'Enter description of the question',
			'slug'			=> 'description',
			'type'			=> 'textarea',
		) );
	});
	
	/* CONTENT IN THE SETTINGS SECTION */
	add_action( 'space-question-edit-settings-div', function( $form ){
		
		$form->display_field( array(
			'label'	=> 'Question Type',
			'slug'	=> 'type',
			'type'	=> 'dropdown',
			'options'	=> array(
				'single'	=> 'Single',
				'multiple'	=> 'Multiple'
			)
		) );
		
		$form->display_field( array(
			'label'	=> 'Parent',
			'slug'	=> 'parent',
			'type'	=> 'dropdown',
			'options'	=> array(
				'0'	=> '(no parent)',
			)
		) );
		
		$form->display_field( array(
			'label'	=> 'Order',
			'slug'	=> 'order',
			'type'	=> 'text',
			'default'	=> '0'
		) );
		
	});
	
	/* CONTENT BELOW THE SETTINGS SECTION */
	add_action( 'space-question-edit-delete-div', function( $form ){
		
		_e('<a class="submitdelete" href="">Move to Trash</a>');
		
	});
	
	add_action( 'space-question-edit-publish-div', function( $form ){
		
		_e('<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish">');
		
	});
	
	$form->display();
	
?>
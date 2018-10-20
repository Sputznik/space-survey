<?php
	
	$form_fields = array(
		'title'	=> array(
			'placeholder'	=> 'Enter question here',
			'slug'			=> 'title',
			'type'			=> 'big-text',
		),
		'desc'	=> array(
			'placeholder'	=> 'Enter description of the question',
			'slug'			=> 'desc',
			'type'			=> 'textarea',
		),
		'type'	=> array(
			'label'	=> 'Question Type',
			'slug'	=> 'type',
			'type'	=> 'dropdown',
			'options'	=> array(
				'single'	=> 'Single',
				'multiple'	=> 'Multiple'
			)
		),
		'parent' => array(
			'label'	=> 'Parent',
			'slug'	=> 'parent',
			'type'	=> 'dropdown',
			'options'	=> array(
				'0'	=> '(no parent)',
			)
		),
		'order' => array(
			'label'	=> 'Order',
			'slug'	=> 'order',
			'type'	=> 'text',
			'default'	=> '0'
		) 
	);
	
	
	
	$question_db = SPACE_DB_QUESTION::getInstance();
	
	$form = new SPACE_FORM;

	
	
	/*SAVING FORM DATA INTO DB*/
	if( isset( $_POST['publish'] ) ) {
		
		//print_r( $_POST );
		
		$form_data = array(
			'title' 		=> sanitize_text_field($_POST['title']),
			'description'	=> sanitize_text_field($_POST['desc']),
			'rank' 			=> absint( $_POST['order'] ),
			'type' 			=> $_POST['type'],
			'author_id'		=> get_current_user_id(),
			'parent' 		=> absint( $_POST['parent'] ),
		);
		
		// CHECK IF DATA EXISTS THEN IT NEEDS TO BE UPDATED
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			$question_db->update( $_GET['ID'], $form_data );	
		}
		else{
			$question_db->insert( $form_data );	
		}
	}
	
	/* IF ID HAS BEEN PASSED THEN GET DATA FROM THE TABLE */
	if( isset( $_GET['ID'] ) && $_GET['ID'] ){
		$row = $question_db->get_row( $_GET['ID'] );
		
		// FILLING THE DATA FROM THE DB INTO THE FORM FIELDS
		$form_fields['title']['value'] = $row->title;
		$form_fields['desc']['value'] = $row->description;
		$form_fields['type']['value'] = $row->type;
		$form_fields['parent']['value'] = $row->parent;
		$form_fields['order']['value'] = $row->rank;
	}
	
	
	/* CONTENT IN THE MAIN BODY */
	add_action( 'space-question-edit-body-div', function( $data ){
		
		$data['form']->display_field( $data['fields']['title'] );
		
		$data['form']->display_field( $data['fields']['desc'] );
		
	});
	
	/* CONTENT IN THE SETTINGS SECTION */
	add_action( 'space-question-edit-settings-div', function( $data ){
		
		$data['form']->display_field( $data['fields']['type'] );
		
		$data['form']->display_field( $data['fields']['parent'] );
		
		$data['form']->display_field( $data['fields']['order'] );
		
	});
	
	/* CONTENT BELOW THE SETTINGS SECTION */
	add_action( 'space-question-edit-delete-div', function( $data ){
		
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			_e('<a class="submitdelete" href="?page='.$_GET['page'].'&ID='.$_GET['ID'].'&action=trash">Move to Trash</a>');	
		}
	});
	
	add_action( 'space-question-edit-publish-div', function( $data ){
		
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			_e('<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Update">');
		}
		else{
			_e('<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish">');
		}
		
	});
	
	$page_title = isset( $_GET['ID'] ) ? 'Edit Question' : 'Add New Question';
	
	_e("<h1>$page_title</h1>");
	
	$form->display( $form_fields );
	
?>
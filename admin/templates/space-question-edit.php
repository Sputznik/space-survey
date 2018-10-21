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
	
	// CURRENT PAGE - USED WITHIN THE ACTION HOOKS
	$current_page = $_GET['page']; 		
	
	add_action( $current_page.'-form-init', function( $form ){
		
		require_once( plugin_dir_path(__FILE__).'../../db/class-space-db-question.php' );
		$question_db = SPACE_DB_QUESTION::getInstance();
		
		
		/*
		* DELETE ACTION HAS BEEN CHOSEN - DELETE BY ROW ID
		* AFTER DELETING THE ROW, REDIRECT TO THE MAIN LIST
		* REDIRECTING USING JS AS WP REDIRECT WAS CAUSING CONFLICTS WITH HEADER INFORMATION ALREADY BEING SENT
		*/
		if( isset( $_GET['ID'] ) && $_GET['ID'] && isset( $_GET['action'] ) && 'trash' == $_GET['action'] ){
			$question_db->delete_row( $_GET['ID'] );
			_e( "<script>location.href='?page=space-questions';</script>" );
			wp_die();	
		}
		
		/* 
		* SAVING FORM DATA INTO DB WHEN THE METHOD:POST IS INVOKED. 
		* CHOOSE BETWEEN UPDATING OR INSERTING THE ROW IN THE DATABASE BASED ON THE PRESENCE OF THE ID
		*/
		if( isset( $_POST['publish'] ) ) {
			
			$form_data = array(
				'title' 		=> sanitize_text_field($_POST['title']),
				'description'	=> sanitize_text_field($_POST['desc']),
				'rank' 			=> absint( $_POST['order'] ),
				'type' 			=> $_POST['type'],
				'author_id'		=> get_current_user_id(),
				'parent' 		=> 0, //absint( $_POST['parent'] ),
			);
			
			// CHECK IF DATA EXISTS THEN IT NEEDS TO BE UPDATED
			if( isset( $_GET['ID'] ) && $_GET['ID'] ){
				$question_db->update( $_GET['ID'], $form_data );	
			}
			else{
				$question_db->insert( $form_data );	
			}
		}
		
		/*
		* IF ID HAS BEEN PASSED THEN GET DATA FROM THE TABLE/DB AND FILL IT INSIDE THE FORM FIELDS
		*/
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			$row = $question_db->get_row( $_GET['ID'] );
			
			$fields = $form->getFields();
			$fields['title']['value'] = $row->title;
			$fields['desc']['value'] = $row->description;
			$fields['type']['value'] = $row->type;
			$fields['parent']['value'] = $row->parent;
			$fields['order']['value'] = $row->rank;
			$form->setFields( $fields );
		}
		
		
	});
	
	/* CONTENT IN THE MAIN BODY */
	add_action( $current_page.'-body-div', function( $form ){
		
		$form->display_field( $form->fields['title'] );
		
		$form->display_field( $form->fields['desc'] );
		
		require_once( plugin_dir_path(__FILE__).'../../forms/class-space-choice-form.php' );
		
		$choice_form = new SPACE_CHOICE_FORM;
		
		$choice_form->display();
		
	});
	
	/* CONTENT IN THE SETTINGS SECTION */
	add_action( $current_page.'-settings-div', function( $form ){
		
		$form->display_field( $form->fields['type'] );
		
		//$form->display_field( $form->fields['parent'] );
		
		$form->display_field( $form->fields['order'] );
		
	});
	
	/* CONTENT BELOW THE SETTINGS SECTION */
	add_action( $current_page.'-delete-div', function( $form ){
		
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			_e('<a class="submitdelete" href="?page='.$_GET['page'].'&ID='.$_GET['ID'].'&action=trash">Move to Trash</a>');	
		}
	});
	
	/* PUBLISH OR UPDATE BUTTON */
	add_action( $current_page.'-publish-div', function( $form ){
		
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			_e('<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Update">');
		}
		else{
			_e('<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish">');
		}
		
	});
	
	$form = new SPACE_ADMIN_FORM( 
		$form_fields, 										// FORM FIELDS THAT NEEDS TO BE DISPLAYED WITHIN THE FORM PASSED FOR LATER REFERENCE
		isset( $_GET['ID'] ) ? 'Edit Question' : 'Add New Question', 		// PAGE TITLE BEFORE THE FORM BEGINS
		$current_page
	);
	
	// DISPLAY THE 2 COLUMN FORM
	$form->display();
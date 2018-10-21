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
		
		$question_db = SPACE_DB_QUESTION::getInstance();
		
		$choice_db = SPACE_DB_CHOICE::getInstance();
		
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
			
			$question_id = 0;
			
			$question_data = array(
				'title' 		=> sanitize_text_field($_POST['title']),
				'description'	=> sanitize_text_field($_POST['desc']),
				'rank' 			=> absint( $_POST['order'] ),
				'type' 			=> $_POST['type'],
				'author_id'		=> get_current_user_id(),
				'parent' 		=> 0, //absint( $_POST['parent'] ),
			);
			
			// CHECK IF DATA EXISTS THEN IT NEEDS TO BE UPDATED
			if( isset( $_GET['ID'] ) && $_GET['ID'] ){
				$question_id = $_GET['ID'];
				$question_db->update( $question_id, $question_data );	
			}
			else{
				$question_db->insert( $question_data );	
			}
			
			if( $question_id && isset( $_POST[ 'choices' ] ) ){
				
				foreach( $_POST[ 'choices' ] as $choice ){
					
					// CHECK IF DATA IN THE POST MEETS THE MINIMUM REQUIREMENT
					if( isset( $choice['id'] ) && isset( $choice['text'] ) && $choice['text'] ){
						
						// PREPARE THE CHOICE DATA FOR UPDATION OR INSERTION
						$choice_data = array(
							'title'			=> $choice['text'],
							'question_id' 	=> $question_id
						);
						
						// CHECK IF THE DATA NEEDS TO B EUPDATED OR INSERTED
						if( $choice['id'] ){
							$choice_db->update( $choice['id'], $choice_data );
						}
						else{
							$choice_db->insert( $choice_data );
						}
						
					}
				}
			}
			
			// $_POST['choices_delete'] HAS A COMMA SEPERATED STRING OF CHOICE IDs THAT ARE NO LONGER NEEDED
			if( $question_id && isset( $_POST['choices_delete'] ) && $_POST['choices_delete'] ){
				$choice_db->delete_rows( explode(',', $_POST['choices_delete'] ) );
			}
			
			/*
			echo "<pre>";
			print_r( $_POST['choices'] );
			echo "</pre>";
			*/
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
		
		$question_db = SPACE_DB_QUESTION::getInstance();
		
		$choices = $question_db->choices( $_GET['ID'] );
		
		$choice_form = new SPACE_CHOICE_FORM( $choices );
		
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
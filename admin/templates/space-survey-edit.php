<?php

	$form_fields = array(
		'title'	=> array(
			'placeholder'	=> 'Enter survey name',
			'slug'			=> 'title',
			'type'			=> 'big-text',
		),
		'desc'	=> array(
			'placeholder'	=> 'Enter description of the survey',
			'slug'			=> 'desc',
			'type'			=> 'textarea',
		) 
	);

	// CURRENT PAGE - USED WITHIN THE ACTION HOOKS
	$current_page = $_GET['page']; 	


	// CURRENT PAGE - USED WITHIN THE ACTION HOOKS
	$current_page = $_GET['page']; 		
	
	add_action( $current_page.'-form-init', function( $form ){
		
		$survey_db = SPACE_DB_SURVEY::getInstance();
		
		/*
		* DELETE ACTION HAS BEEN CHOSEN - DELETE BY ROW ID
		* AFTER DELETING THE ROW, REDIRECT TO THE MAIN LIST
		* REDIRECTING USING JS AS WP REDIRECT WAS CAUSING CONFLICTS WITH HEADER INFORMATION ALREADY BEING SENT
		*/
		if( isset( $_GET['ID'] ) && $_GET['ID'] && isset( $_GET['action'] ) && 'trash' == $_GET['action'] ){
			$survey_db->delete_row( $_GET['ID'] );
			_e( "<script>location.href='?page=space-survey';</script>" );
			wp_die();	
		}
		
		/* 
		* SAVING FORM DATA INTO DB WHEN THE METHOD:POST IS INVOKED. 
		* CHOOSE BETWEEN UPDATING OR INSERTING THE ROW IN THE DATABASE BASED ON THE PRESENCE OF THE ID
		*/
		if( isset( $_POST['publish'] ) ) {

			/*
			* UPDATE SURVEY MODEL
			* CHECK USING $_GET['ID'] - IF DATA EXISTS THEN IT NEEDS TO BE UPDATED OR IT NEEDS TO BE INSERTED
			*/ 
			$survey_id = 0;
			$survey_data = $survey_db->sanitize( $_POST );
			if( isset( $_GET['ID'] ) && $_GET['ID'] ){
				$survey_id = $_GET['ID'];
				$survey_db->update( $survey_id, $survey_data );	
			}
			else{
				$survey_id = $survey_db->insert( $survey_data );	
			}
			// END OF UPDATING SURVEY MODEL


			/*
			* UPDATE PAGE MODEL
			*/
			if( $survey_id && isset( $_POST[ 'pages' ] ) ){
				// UPDATE OR ADD NEW PAGE
				$survey_db->updatePages( $survey_id, $_POST[ 'pages' ] );
			}
			
			if( $survey_id && isset( $_POST['pages_delete'] ) && $_POST['pages_delete'] ){
				// $_POST['pages_delete'] HAS A COMMA SEPERATED STRING OF PAGE IDs THAT ARE NO LONGER NEEDED
				$survey_db->deletePages( explode(',', $_POST['pages_delete'] ) );
			}
			
			
			if( !isset( $_GET['ID'] ) && $survey_id ){
				// INSERTTION HAS BEEN DONE SO FAR - NEW SURVEY, SO HANDLE REDIRECTION TO EDIT THE NEW SURVEY
				_e( "<script>location.href='?page=space-survey-edit&ID=$survey_id';</script>" );
			}
			
			
			echo "<pre>";
			print_r( $_POST );
			echo "</pre>";
			wp_die();
		}
		
		/*
		* IF ID HAS BEEN PASSED THEN GET DATA FROM THE TABLE/DB AND FILL IT INSIDE THE FORM FIELDS
		*/
		if( isset( $_GET['ID'] ) && $_GET['ID'] ){
			$row = $survey_db->get_row( $_GET['ID'] );
			
			$fields = $form->getFields();
			$fields['title']['value'] = $row->title;
			$fields['desc']['value'] = $row->description;
			$form->setFields( $fields );
		}
		
		
	});


	/* CONTENT IN THE MAIN BODY */
	add_action( $current_page.'-body-div', function( $form ){
		
		$form->display_field( $form->fields['title'] );
		
		$form->display_field( $form->fields['desc'] );


		require_once( plugin_dir_path(__FILE__).'../../forms/class-space-page-form.php' );
		
		$survey_db = SPACE_DB_SURVEY::getInstance();
		
		// GET LIST OF CHOICES FROM THE SURVEY
		if( isset( $_GET['ID'] ) ){
			$pages = $survey_db->listPages( $_GET['ID'] );	
		}
		else {
			$pages = array();
		}
		
		$page_form = new SPACE_PAGE_FORM( $pages );
		
		$page_form->display();

		
	});
	
	/* CONTENT IN THE SETTINGS SECTION */
	add_action( $current_page.'-settings-div', function( $form ){
		
		
		
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
		isset( $_GET['ID'] ) ? 'Edit Survey' : 'Add New Survey', 		// PAGE TITLE BEFORE THE FORM BEGINS
		$current_page
	);
	
	// DISPLAY THE 2 COLUMN FORM
	$form->display();



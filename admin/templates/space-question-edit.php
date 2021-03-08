<?php

	class SPACE_QUESTION_EDIT{

		var $metaFields;
		var $formFields;
		var $currentPage;

		function __construct( $formFields ){

			$this->setCurrentPage( $_GET['page'] );
			$this->setMetaFields( array( 'limitFlag', 'limit', 'limitError', 'otherFlag', 'otherText' ) );
			$this->setFormFields( $formFields );

			// ACTION HOOKS FOR THE SECTIONS IN THE FORM
			add_action( $this->getCurrentPage().'-form-init', array( $this, 'init' ) );
			add_action( $this->getCurrentPage().'-body-div', array( $this, 'body_section' ) );
			add_action( $this->getCurrentPage().'-settings-div', array( $this, 'settings_section' ) );
			add_action( $this->getCurrentPage().'-delete-div', array( $this, 'trash_section' ) );
			add_action( $this->getCurrentPage().'-publish-div', array( $this, 'publish_section' ) );


			$form = new SPACE_ADMIN_FORM(
				$this->getFormFields(), 			// FORM FIELDS THAT NEEDS TO BE DISPLAYED WITHIN THE FORM PASSED FOR LATER REFERENCE
				isset( $_GET['ID'] ) ? 'Edit Question' : 'Add New Question', 		// PAGE TITLE BEFORE THE FORM BEGINS
				$this->getCurrentPage()
			);
			$form->display();							// DISPLAY THE 2 COLUMN FORM

		}

		/* GETTER & SETTER FUNCTIONS */
		function getMetaFields(){ return $this->metaFields; }
		function setMetaFields( $metaFields ){ $this->metaFields = $metaFields; }
		function getFormFields(){ return $this->formFields; }
		function setFormFields( $formFields ){ $this->formFields = $formFields; }
		function getCurrentPage(){ return $this->currentPage; }
		function setCurrentPage( $currentPage ){ $this->currentPage = $currentPage; }
		/* GETTER & SETTER FUNCTIONS */

		/*
		* FOLLOWING IS THE SEQUENCE OF ACTIONS
		* 1. CHECK FOR DELETE ACTION
		* 2. SAVING FORM DATA INTO DB WHEN THE METHOD:POST IS INVOKED.
		* 3. PUSH DATA FROM DB TO FORM FIELDS
		*/
		function init( $form ){
			// 1
			if( isset( $_GET['ID'] ) && $_GET['ID'] && isset( $_GET['action'] ) && 'trash' == $_GET['action'] ){
				$this->deleteAction();
			}
			// 2
			if( isset( $_POST['publish'] ) ) {
				$this->publishAction();
			}
			// 3
			if( isset( $_GET['ID'] ) && $_GET['ID'] ){
				$this->pushDataAction( $_GET['ID'], $form );
			}
		}

		function publishAction(){
			$question_db = SPACE_DB_QUESTION::getInstance();

			/*
			* UPDATE QUESTION MODEL
			* CHECK USING $_GET['ID'] - IF DATA EXISTS THEN IT NEEDS TO BE UPDATED OR IT NEEDS TO BE INSERTED
			*/
			$question_id = 0;
			$question_data = $question_db->sanitize( $_POST );
			if( isset( $_GET['ID'] ) && $_GET['ID'] ){
				$question_id = $_GET['ID'];
				$question_db->update( $question_id, $question_data );
			}
			else{
				$question_id = $question_db->insert( $question_data );
			}
			//print_r( $question_data );
			// END OF UPDATING QUESTION MODEL

			/*
			* UPDATE CHOICE MODEL
			*/
			if( $question_id && isset( $_POST[ 'choices' ] ) ){
				// UPDATE OR ADD NEW CHOICES
				$question_db->updateChoices( $question_id, $_POST[ 'choices' ] );
			}
			if( $question_id && isset( $_POST['choices_delete'] ) && $_POST['choices_delete'] ){
				// $_POST['choices_delete'] HAS A COMMA SEPERATED STRING OF CHOICE IDs THAT ARE NO LONGER NEEDED
				$question_db->deleteChoices( explode(',', $_POST['choices_delete'] ) );
			}
			// END OF UPDATING CHOICE MODEL

			/*
			* INSERTION HAS BEEN DONE SO FAR. IS THIS IS A NEW QUESTION, THEN HANDLE REDIRECTION TO EDIT THE NEW QUESTION
			*/
			if( !isset( $_GET['ID'] ) && $question_id ){
				_e( "<script>location.href='?page=space-question-edit&ID=$question_id';</script>" );
			}
		}

		/*
		* GET DATA FROM THE TABLE/DB USING THE ID AND FILL IT INSIDE THE FORM FIELDS
		*/
		function pushDataAction( $question_id, $form ){
			$question_db = SPACE_DB_QUESTION::getInstance();
			$fields = $form->getFields();

			$row = $question_db->get_row( $question_id );
			$meta_info = $question_db->getMetaInfo( $row );

			$fields['title']['value'] = $row->title;
			$fields['desc']['value'] = $row->description;
			$fields['type']['value'] = $row->type;

			// INCLUDE META INFORMATION
			foreach( $this->getMetaFields() as $metaField ){
				if( isset( $meta_info[$metaField] ) ){
					$fields[$metaField]['value'] = $meta_info[$metaField];
				}
			}

			$form->setFields( wp_unslash( $fields ) );
		}

		/*
		* CONTENT IN THE MAIN BODY
		*/
		function body_section( $form ){
			$form->display_field( $form->fields['title'] );
			$form->display_field( $form->fields['desc'] );

			// GET LIST OF CHOICES FROM THE QUESTION TABLE
			$choices = array();
			if( isset( $_GET['ID'] ) ){
				$choices = SPACE_DB_QUESTION::getInstance()->listChoices( $_GET['ID'] );
			}

			// DISPLAYS THE LIST OF CHOICES IN A REPEATER SECTION
			require_once( plugin_dir_path(__FILE__).'../../forms/class-space-choice-form.php' );
			$choice_form = new SPACE_CHOICE_FORM( $choices );
			$choice_form->display();
		}

		/*
		* SETTINGS SECTION IN THE SIDEBAR
		*/
		function settings_section( $form ){
			$form->display_field( $form->fields['type'] );

			// LIST OF META FIELDS
			foreach( $this->getMetaFields() as $metaField ){
				$form->display_field( $form->fields[ $metaField ] );
			}
		}

		/*
		* SECTION BELOW THE SETTINGS SECTION IN THE SIDEBAR
		*/
		function trash_section( $form ){
			if( isset( $_GET['ID'] ) && $_GET['ID'] ){
				_e('<a class="submitdelete" href="?page='.$_GET['page'].'&ID='.$_GET['ID'].'&action=trash">Move to Trash</a>');
			}
		}

		/*
		* PUBLISH OR UPDATE BUTTON
		*/
		function publish_section( $form ){
			$button_value = ( isset( $_GET['ID'] ) && $_GET['ID'] ) ? "Update" : "Publish";
			$button_class = "button button-primary button-large";
			_e( '<input type="submit" name="publish" id="publish" class="' . $button_class . '" value="' . $button_value . '">' );
		}

		/*
		* DELETE BY ROW ID
		* AFTER DELETING THE ROW, REDIRECT TO THE MAIN LIST
		* REDIRECTING USING JS AS WP REDIRECT WAS CAUSING CONFLICTS WITH HEADER INFORMATION ALREADY BEING SENT
		*/
		function deleteAction(){
			SPACE_DB_QUESTION::getInstance()->delete_row( $_GET['ID'] );
			_e( "<script>location.href='?page=space-questions';</script>" );
			wp_die();
		}

	}

	new SPACE_QUESTION_EDIT( array(
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
			'label'		=> 'Question Type',
			'slug'		=> 'type',
			'type'		=> 'dropdown',
			'options'	=> SPACE_DB_QUESTION::getInstance()->getTypes()
		),

		// METABOX FOR SETTING NUMBER CHECKBOXES THAT CAN BE SELECTED
		'limitFlag'	=> array(
			'container_class'	=> 'space-form-field meta-field question-meta-field',
			'label'		=> 'Enable limited selection',
			'slug'		=> 'limitFlag',
			'type'		=> 'boolean',
			'text'		=> 'Yes'
		),
		'limit'	=> array(
			'container_class'	=> 'space-form-field meta-field limit-sub-field',
			'label'		=> 'The number of selection to be limited',
			'slug'		=> 'limit',
			'type'		=> 'number',
			'help'		=> '',
			'value'		=> '0'
		),
		'limitError'	=> array(
			'container_class'	=> 'space-form-field meta-field limit-sub-field bottom-decoration',
			'label'						=> 'Error message when LIMIT is crossed',
			'slug'						=> 'limitError',
			'type'						=> 'textarea',
			'value'						=> 'Please unselect some choices as you have crossed the maximum number of selection.',
			'rows'						=> '2'
		),
		'otherFlag'	=> array(
			'container_class'	=> 'space-form-field meta-field question-meta-field',
			'label'		=> 'Enable text input for checkboxes',
			'slug'		=> 'otherFlag',
			'type'		=> 'boolean',
			'text'		=> 'Yes'
		),
		'otherText'	=> array(
			'container_class'	=> 'space-form-field meta-field other-text-field',
			'label'						=> 'Placeholder text for OTHER input field',
			'slug'						=> 'otherText',
			'type'						=> 'text',
			'value'						=> 'Other'
		),
	) );

<?php
	
	
	class SPACE_FORM{
		
		var $fields;
		var $page;
		var $pageTitle;
		
		function __construct( $fields, $pageTitle, $page ){
			
			// SET FIELDS
			$this->setFields( $fields );
			
			// SET PAGE
			$this->setPage( $page );
			
			// SET PAGE TITLE
			$this->setPageTitle( $pageTitle );
			
			// FORM INIT ACTION HOOK - WHERE ALL THE DATABASE RELATED QUERIES ARE DONE
			do_action( $this->getPage(). '-form-init', $this );
			
		}
		
		/* GETTER AND SETTER FUNCTIONS */
		function setFields( $fields ){ $this->fields = $fields; }
		function getFields(){ return $this->fields; }
		
		function setPage( $page ){ $this->page = $page; }
		function getPage(){ return $this->page; }
		
		function setPageTitle( $pageTitle ){ $this->pageTitle = $pageTitle; }
		function getPageTitle(){ return $this->pageTitle; }
		/* GETTER AND SETTER FUNCTIONS */
		
		function display(){
			include 'templates/form.php';
		}
		
		// WRAPPER FOR WP DO_ACTION
		function do_action( $action_hook ){
			do_action( $this->getPage().'-'.$action_hook, $this );
		}
		
		function display_field( $field ){
			
			if( !isset( $field['slug'] ) || !isset( $field['type'] ) ){
				return 0;
			}
			
			$field['value'] = isset( $field['value'] ) ? $field['value'] : ( isset( $field['default'] ) ? $field['default'] : '' );
			
			if( isset( $field['label'] ) ){
				_e('<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="'.$field['slug'].'">'.$field['label'].'</label></p>');
			}
			
			if( $field['type'] == 'dropdown' && isset( $field['options'] ) ){
				_e('<select name="'.$field['slug'].'" id="'.$field['slug'].'">');
				foreach( $field['options'] as $option_slug => $option ){
					$optionIsSelected = $field['value'] == $option_slug ? true : false;
					
					_e("<option value='$option_slug'");
					
					if( $optionIsSelected ){
						_e(" selected='selected'");	
					}
					
					_e(">$option</option>");
				}
				_e('</select>');
									
			}
			
			if( $field['type'] == 'text' ){
				_e('<input name="'.$field['slug'].'" type="text" id="'.$field['slug'].'" value="'.$field['value'].'">');
			}
			
			if( $field['type'] == 'big-text' ){
				_e('<input type="text" class="big-text" placeholder="'.$field['placeholder'].'" name="'.$field['slug'].'" size="30" value="'.$field['value'].'" id="title" spellcheck="true" autocomplete="off">');
			}
			
			if( $field['type'] == 'textarea' ){
				_e('<textarea placeholder="'.$field['placeholder'].'" name="'.$field['slug'].'"  style="width:100%;padding:10px;" rows="10">'.$field['value'].'</textarea>');
			}
		}
		
	}
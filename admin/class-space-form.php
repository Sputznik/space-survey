<?php
	
	
	class SPACE_FORM{
		
		function display(){
			include 'templates/form.php';
		}
		
		function display_field( $field ){
			
			if( !isset( $field['slug'] ) || !isset( $field['type'] ) ){
				return 0;
			}
			
			$field['value'] = isset( $field['value'] ) ? $field['value'] : isset( $field['default'] ) ? $field['default'] : '';
			
			if( isset( $field['label'] ) ){
				_e('<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="'.$field['slug'].'">'.$field['label'].'</label></p>');
			}
			
			if( $field['type'] == 'dropdown' && isset( $field['options'] ) ){
				_e('<select name="'.$field['slug'].'" id="'.$field['slug'].'">');
				foreach( $field['options'] as $option_slug => $option ){
					_e('<option value="'.$option_slug.'">'.$option.'</option>');
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
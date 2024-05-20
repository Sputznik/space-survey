<?php
/*
* BASE CLASS THAT DISPLAYS FIELD BASED ON SOME PARAMETERS GIVEN
*/


class SPACE_FORM{

	// OVERRIDEN BY CHILD CLASSES FOR IMPLEMENTATION
	function display(){}

	function json( $type, $data ){
		SPACE_UTIL::getInstance()->browserData( $type, $data );
	}

	function display_field( $field ){

		if( !isset( $field['slug'] ) || !isset( $field['type'] ) ){
			return 0;
		}

		$container_class = isset( $field['container_class'] ) ? $field['container_class'] : "space-form-field";

		_e('<div class="'.$container_class.'">');
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

		if( $field['type'] == 'file' ){

			_e('<input name="'.$field['slug'].'" type="file" id="'.$field['slug'].'" value="'.$field['value'].'">');

		}

		if( $field['type'] == 'text' ){
			_e('<input name="'.$field['slug'].'" type="text" id="'.$field['slug'].'" value="'.$field['value'].'">');
		}


		if( $field['type'] == 'boolean' ){

			_e('<label>');
			_e('<input name="'.$field['slug'].'" type="checkbox" id="'.$field['slug'].'" value="1"');
			if( $field['value'] == '1' ){
				_e(' checked="checked" ');
			}
			_e(' />');
			_e('&nbsp;' . $field['text']);
			_e('</label>');
		}


		if( $field['type'] == 'number' ){
			//print_r( $field );
			_e('<input name="'.$field['slug'].'" type="number" id="'.$field['slug'].'" value="'.$field['value'].'">');
		}

		if( $field['type'] == 'big-text' ){
			_e('<input type="text" class="big-text" placeholder="'.$field['placeholder'].'" name="'.$field['slug'].'" size="30" value="'.$field['value'].'" id="title" spellcheck="true" autocomplete="off">');
		}

		if( $field['type'] == 'textarea' ){
			_e('<textarea ');
			if( isset( $field['placeholder'] ) ){
				_e(' placeholder="'.$field['placeholder'].'" ');
			}
			if( isset( $field['rows'] ) ){
				_e(' rows="'.$field['rows'].'" ');
			}
			_e(' name="'.$field['slug'].'"  style="width:100%;padding:10px;">'.$field['value'].'</textarea>');
		}

		if( $field['type'] == 'autocomplete' ){
			_e("<div data-behaviour='space-autocomplete' data-field='".wp_json_encode( $field )."'></div>");
		}

		if( isset( $field['help'] ) && $field['help'] ){
			_e( '<p class="help">'.$field['help'].'</p>' );
		}

		_e('</div>');

	}

}

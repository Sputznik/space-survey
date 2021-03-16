<?php

  $fields_arr = array(
		'first-btn-text' => array(
      'label' => 'First Button Text',
      'type'  => 'text',
			'default'	=> 'Next'
    ),
		'prev-text' => array(
      'label' => 'Previous Button Text',
      'type'  => 'text',
			'default'	=> 'Previous'
    ),
    'next-text' => array(
      'label' => 'Next Button Text',
      'type'  => 'text',
			'default'	=> 'Next'
    ),
		'last-btn-text' => array(
      'label' => 'Last Button Text',
      'type'  => 'text',
			'default'	=> 'Finish'
    ),
		'error-missing-text' => array(
      'label' 	=> 'Error message for required fields',
      'type'  	=> 'textarea',
			'default'	=> 'Some required fields have not been filled. Please fill them and move to the next slide.'
    ),
    'disable-cookie' => array(
      'label' => 'Disable cookies to allow user to submit multiple times',
      'type'  => 'boolean',
			'default'	=> 0
    )
  );

  global $post;

	function displayLabel( $label ){
		_e('<p><label>'.$label.'</label></p>');
	}

  function displayTextField( $field ){
    _e('<input class="" type="text" name="' . $field['name'] . '" value="' . $field['value'] . '" />');
  }

	function displayTextArea( $field ){
		_e('<textarea class="large-text" name="' . $field['name'] . '">' . $field['value'] . '</textarea>');
	}

  function displayBooleanField( $field ){
    $flag = false;
    if( $field['value'] ){ $flag = true; }

    $booleanField = '<input type="checkbox" name="' . $field['name'] . '" value="1"';
    if( $flag ){
      $booleanField .= 'checked="checked"';
    }
    $booleanField .= ' />';

    _e( $booleanField );
    _e( $field['label'] );
  }

  $data = get_post_meta( $post->ID, 'survey_settings', true );

  foreach( $fields_arr as $slug => $field ){

    $field['name'] = "survey_settings[".$slug."]";
    $field['value'] = isset( $data[ $slug ] ) && !empty( $data[ $slug ] ) ? $data[ $slug ] : $field['default'];

		_e('<div style="margin-bottom: 20px;">');

    switch ($field['type']) {
      case 'text':
				displayLabel( $field['label'] );
        displayTextField( $field );
        break;

			case 'textarea':
				displayLabel( $field['label'] );
	      displayTextArea( $field );
	      break;

      default:
        displayBooleanField( $field );
        break;
    }

    _e('</div>');
  }

?>

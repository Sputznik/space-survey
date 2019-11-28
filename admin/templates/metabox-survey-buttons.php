<?php

  $fields_arr = array(
    'prev-text' => array(
      'label' => 'Previous',
      'type'  => 'text'
    ),
    'next-text' => array(
      'label' => 'Next',
      'type'  => 'text'
    ),
    'disable-cookie' => array(
      'label' => 'Disable cookies to allow user to submit multiple times',
      'type'  => 'boolean'
    )
  );

  global $post;

  function displayTextField($name_attr, $value_attr, $label){
    _e('<input type="text" name="' . $name_attr . '" placeholder="' . $label . '" value="' . $value_attr . '" />');
  }

  function displayBooleanField($name_attr, $value_attr, $label){
    $flag = false;
    if($value_attr){ $flag = true; }

    $field = '<input type="checkbox" name="' . $name_attr . '" value="1"';
    if( $flag ){
      $field .= 'checked="checked"';
    }
    $field .= ' />';

    _e($field);
    _e( $label );
  }

  $data = get_post_meta( $post->ID, 'survey_settings', true );

  foreach( $fields_arr as $slug => $field ){

    $name_attr = "survey_settings[".$slug."]";
    $value_attr = isset( $data[ $slug ] ) ? $data[ $slug ] : '';

    _e('<div style="margin-bottom: 20px;">');

    switch ($field['type']) {
      case 'text':
        displayTextField($name_attr, $value_attr, $field['label']);
        break;

      default:
        displayBooleanField($name_attr, $value_attr, $field['label']);
        break;
    }

    _e('</div>');
  }

?>

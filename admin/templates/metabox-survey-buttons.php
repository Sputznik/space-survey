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
  );

  global $post;



  $data = get_post_meta( $post->ID, 'survey_settings', true );

  foreach( $fields_arr as $slug => $field ){

    $name_attr = "survey_settings[".$slug."]";
    $value_attr = isset( $data[ $slug ] ) ? $data[ $slug ] : '';

    _e('<div style="margin-bottom: 20px;">');
    _e( '<label style="display:block">' . $field['label'] . '</label>' );
    ?>
    <input type="text" name="<?php _e( $name_attr );?>" value="<?php _e( $value_attr );?>" />
    <?php
    _e('</div>');
  }

?>

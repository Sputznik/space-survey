<?php
/*
* IMPORT CSV CLASS
*/


class SPACE_CSV extends SPACE_BASE{

  // UPLOAD CSV FILE
  function upload( $input_file ){
    // INCLUDE THE NECESSARY FILES FOR UPLOAD
    if ( !function_exists('wp_handle_upload') ) { require_once ABSPATH . 'wp-admin/includes/file.php'; }

    return wp_handle_upload( $input_file, array('test_form' => false) );
  }

  // CONVERT CSV FILE TO ARRAY
  function convertToArray( $file_path ){

    $arrayCsv = array();
    $file     = fopen(  $file_path, "r" );

    // ITERATE THROUGH THE FILE TO READ
    while ( !feof( $file ) ) {
      $fpTotal = fgetcsv( $file );
      array_push( $arrayCsv, $fpTotal );
    }
    fclose($file);
		
    return $arrayCsv;
  }


}


SPACE_CSV::getInstance();

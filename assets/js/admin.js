jQuery( document ).ready(function(){

  jQuery( '.question-meta-field' ).each( function(){

    var $el      = jQuery( this ),
      $dropdown = jQuery( "select#type" );

    //Display's Metabox
    function showMeta( $dropdown ){
      // Hides the metabox when the dropdown option gets changed
      $el.hide();

      // Gets the value of the selected field
      var get_selected_child = $dropdown.children( "option:selected" ).val().toLowerCase();
      var checkbox_index = get_selected_child.indexOf( 'checkbox' )

      // Shows the metabox when the dropdown option value is checkbox~
      if( checkbox_index != -1 && checkbox_index == 0  ){ $el.show(); }
    }

    $dropdown.change(function(){ showMeta( $dropdown ); });
    showMeta( $dropdown );

 } );

});

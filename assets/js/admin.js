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

 jQuery('[data-behaviour~=space-form-table]').each( function(){
    var $form = jQuery( this );

    /*
    * ADDING CLASSES TO THE PAGINATE BUTTON TO STYLE IT MORE LIKE THE BUTTONS IN THE PAGES SECTION
    */
    $form.find('.tablenav .tablenav-pages .pagination-links .tablenav-pages-navspan').addClass('button disabled');
    $form.find('.tablenav .tablenav-pages .pagination-links .next-page').addClass('button');
    $form.find('.tablenav .tablenav-pages .pagination-links .prev-page').addClass('button');
    $form.find('.tablenav .tablenav-pages .pagination-links .last-page').addClass('button');
    $form.find('.tablenav .tablenav-pages .pagination-links .first-page').addClass('button');

 } );



});

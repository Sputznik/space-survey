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

 jQuery('[data-behaviour~=responses-filter-btn').each( function(){
   var $btn = jQuery( this );

   $btn.click( function( ev ){
     ev.preventDefault();

     jQuery( '.filters-box' ).toggleClass('hide');
   });
 });

 jQuery('[data-behaviour~=space-survey-import-export').each( function(){
	 var $el 			= jQuery( this ),
	 		$form			= $el.closest('form'),
	 		survey_id = $el.data('survey'),
	 		$btn			= $el.find('button'),
			$loader		= $el.find('.space-loader');

		// DOWNLOAD JSON FILE
		function downloadJSON( jsonData ) {
			disableLoader();

			//Convert JSON Array to string.
			var json = JSON.stringify( jsonData );

			//Convert JSON string to BLOB.
			json = [json];
			var blob1 = new Blob(json, { type: "text/plain;charset=utf-8" });

			//Check the Browser.
			var isIE = false || !!document.documentMode;
			if (isIE) {
				window.navigator.msSaveBlob(blob1, "survey.json");
			}
			else {
				var url = window.URL || window.webkitURL;
				link = url.createObjectURL(blob1);
				var a = jQuery("<a />");
				a.attr("download", "survey.json");
				a.attr("href", link);
				jQuery("body").append(a);
				a[0].click();
				jQuery("body").remove(a);
			}
    }

		// DISPLAY THE LOADER
		function enableLoader(){
			$btn.hide();
			$loader.show();
		}

		// HIDE THE LOADER
		function disableLoader(){
			$btn.show();
			$loader.hide();
		}

		function init(){
			disableLoader();
			$form.attr( 'enctype', 'multipart/form-data' );
			//console.log( $form.attr('action') );
		}

		init();

		// EXPORT BUTTON CLICK HANDLE EVENT
		$btn.click( function(){
			enableLoader();

			// AJAX REQUEST
			jQuery.ajax({
				dataType	: 'json',
				url				: space_settings['ajax_url'],
				data			: {
					'survey_id' : survey_id,
					'action'		:	'space_survey_settings_json'
				},
				success		: downloadJSON
			});
		});

 });

});

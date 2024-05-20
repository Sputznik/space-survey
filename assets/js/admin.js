var CONDITIONAL_METAFIELD = function( options ){
	var self = {
		options : jQuery.extend( {
			$el							: null,
			listen					: function(){ self.display(); },
			checkCondition	: function(){ return false; },
		}, options )
	};

	self.display = function(){
		self.options.$el.hide();
		if( self.options.checkCondition() ){
			self.options.$el.show();
		}
	};

	self.init = function(){
		self.options.listen();
		self.display();
	};

	self.init();
	return self;
}

function questionMetaField( $el, questionType ){
	var metaField = CONDITIONAL_METAFIELD( {
		$el : $el,
		checkCondition: function(){
			var $dropdown 				= jQuery( "select#type" ),
				get_selected_child 	= $dropdown.children( "option:selected" ).val().toLowerCase(),
				checkbox_index 			= get_selected_child.indexOf( questionType );

			// Shows the metabox when the dropdown option value is checkbox~
			if( checkbox_index != -1 && checkbox_index == 0  ){ return true; }
			return false;
		},
		listen : function(){
			jQuery( "select#type" ).change( function(){
				metaField.display();
			} );
		}
	} );
	return metaField;
}

function subQuestionMetaField( $el, questionType, $checkboxFlag ){
	var metaField = CONDITIONAL_METAFIELD( {
		$el : $el,
		checkCondition: function(){
			var $dropdown 				= jQuery( "select#type" ),
				get_selected_child 	= $dropdown.children( "option:selected" ).val().toLowerCase(),
				checkbox_index 			= get_selected_child.indexOf( questionType );

			if( checkbox_index != -1 && checkbox_index == 0 && $checkboxFlag.prop('checked') ){
				return true;
			}
			return false;
		},
		listen : function(){
			$checkboxFlag.click( function(){
				metaField.display();
			} );
			jQuery( "select#type" ).change( function(){
				metaField.display();
			} );
		}
	} );
	return metaField;
}

jQuery( document ).ready(function(){

	/*
	* QUESTION EDIT FORM
	*/
	jQuery( '.checkbox-meta-field' ).each( function(){
		questionMetaField( jQuery(this), 'checkbox' );
	} );

	jQuery( '.dropdown-meta-field' ).each( function(){
		questionMetaField( jQuery(this), 'dropdown' );
	} );

	jQuery('.other-text-field').each( function(){
 	 	subQuestionMetaField( jQuery( this ), 'checkbox', jQuery( 'input[name="otherFlag"]' ) );
	} );

	jQuery('.limit-sub-field').each( function(){
		subQuestionMetaField( jQuery( this ), 'checkbox', jQuery( 'input[name="limitFlag"]' ) );
	} );
	/*
	* QUESTION EDIT FORM
	*/



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

	/*
 jQuery('[data-behaviour~=space-ajax-form').each( function(){

	 var $form 		= jQuery( this ).closest( 'form' ),
	 	$submit_btn = $form.find( 'input[type=submit]' ),
	 	$loader			= null,
		autosave		= null;

	 function showLoader(){
		 $loader 		= jQuery( document.createElement( 'span' ) );
		 $loader.addClass( 'spinner is-active' );
		 $loader.appendTo( $submit_btn.parent() );
		 $submit_btn.attr( 'disabled', true );
	 }

	 function hideLoader(){
		 $loader.remove();
		 $submit_btn.attr( 'disabled', false );
	 }

	 function getFormData(){
		 var formData = $form.serializeArray();
		 formData.push( {
			 	'name'	: $submit_btn.attr( 'name' ),
			 	'value'	: $submit_btn.val()
		 	} );
			return formData;
	 }

	 function save(){
		 // SHOW LOADER
		 showLoader();

		 // AJAX CALL
		 jQuery.ajax( {
				'type' 		: $form.attr( 'method' ),
				'url'			:	$form.attr( 'action' ),
				'data'		: getFormData(),
				'success'	:	function( data ){
					hideLoader();

					var $script = jQuery( data ).find('script#redirect');
					if( $script.length ){
						eval( $script.html() );
					}
				}
			} );
	 }

	 $form.submit( function( ev ){
		 ev.preventDefault();
		 autosave.save();
		} );

		autosave = SPACE_AUTOSAVE({
			duration	: 5000,
			save			: save
		} );

	} );
	*/
} );

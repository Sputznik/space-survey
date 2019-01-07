/*
* AUTOCOMPLETE WRAPPER BUILT ON JQUERY AUTOCOMPLETE
*/
jQuery.fn.space_autocomplete = function(){

	return this.each(function() {
		
		var $el 	= jQuery( this ),
			$hidden = jQuery( document.createElement('input') ),
			field 	= $el.attr( 'data-field' ),						
			$input 	= jQuery( document.createElement('input') );
			
		// JSON PARSE FROM STRING
		field = typeof field != 'object' ? JSON.parse( field ) : [];
		
		var init = function(){
			$hidden.attr( 'type', 'hidden' );
			if( field['slug'] != undefined ){
				$hidden.attr( 'name', field['slug'] );
			}
			if( field['value'] != undefined ){
				$hidden.val( field['value'] );
			}
			$hidden.appendTo( $el );
			
			$input.attr( 'type', 'text' );
			if( field['placeholder'] != undefined ){
				$input.attr( 'placeholder', field['placeholder'] );
			}
			if( field['autocomplete_value'] != undefined ){
				$input.val( field['autocomplete_value'] );
			}
			$input.appendTo( $el );
			
			$input.autocomplete({
				minLength: 1,
				delay: 500,
				source: function( request, response ){
					
					// AJAX REQUEST
					jQuery.ajax({ url: field['url'], dataType: "json", data:{ term: request.term },
						success: function( data ){
							response(data);
						}
					});
				}, 
				select: function( event, ui ){
					$hidden.val( ui.item.id );
				},
				change: function( event, ui ){
					if( !ui.item ){
						$hidden.val( '0');
					}
				}
			});
		
		};
		
		
		init();
		
	});
};
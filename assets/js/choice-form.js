jQuery.fn.space_autoresize = function(){

	return this.each(function() {
		   
		var $el = jQuery(this);
		
		$el.attr('rows', 1);
		
		$el.autosize();
		
	});
};

jQuery.fn.space_choices = function(){

	return this.each(function() {
		   
		var $el 			= jQuery(this),
			count 			= 0,												// INCREMENTING VARIABLE WHEN CHOICE IS ADDED
			choices 		= $el.attr( 'data-choices' ),						// CHOICES FROM THE DB
			$list 			= jQuery( document.createElement( 'ul' ) ),			// PARENT LIST THAT HOLDS THE CHOICES
			$btn			= jQuery( document.createElement( 'button' ) ),		// BUTTON THAT ADDS MORE BLANK CHOICES TO THE LIST
			$hidden_delete	= jQuery( document.createElement( 'input' ) ),
			deleted_list 	= [];												// LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED 
		
		// JSON PARSE FROM STRING
		choices = typeof choices != 'object' ? JSON.parse( choices ) : [];
		
		// ADD LIST ITEM TO THE UNLISTED LIST WITH A TEXTAREA
		var addChoice = function( choice_text, choice_id = 0 ){
			
			// CREATE PARENT LIST ITEM: LI
			var $list_item = jQuery( document.createElement( 'li' ) );
			$list_item.addClass( 'space-choice-item' );
			$list_item.appendTo( $list );
			
			// CREATE TEXTAREA THAT WILL HOLD THE CHOICE TEXT
			var $textarea = jQuery( document.createElement('textarea') );
			$textarea.attr( 'data-behaviour', 'space-autoresize' );
			$textarea.attr( 'placeholder', 'Type your choice here'  );
			$textarea.attr( 'name', 'choices[' + count + '][text]' );
			$textarea.appendTo( $list_item );
			$textarea.space_autoresize();
			if( choice_text ){ $textarea.val( choice_text ); }
			
			// CREATE HIDDEN FIELD THAT WILL HOLD THE CHOICE ID
			var $hidden = jQuery( document.createElement('input') );
			$hidden.attr( 'type', 'hidden' );
			$hidden.val( choice_id );
			$hidden.attr( 'name', 'choices[' + count + '][id]' );
			$hidden.appendTo( $list_item );
			
			// CLOSE BUTTON - TO REMOVE THE LIST ITEM
			var $button = jQuery( document.createElement('button') );
			$button.addClass( 'space-close-btn' );
			$button.html( '&times;' );
			$button.appendTo( $list_item );
			
			$button.click( function( ev ){
				ev.preventDefault();
				
				if( choice_id ){
					deleted_list.push( choice_id );
					$hidden_delete.val( deleted_list.join() );
				}
				$list_item.remove();
			});
			
			count++;
		};
		
		// INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE CHOICE AND THE BUTTON
		var init = function(){
			$list.attr('id', 'space-choices-list');
			$list.appendTo( $el );
		
			$btn.addClass('button');
			$btn.html( '+ Add Choice' );
			$btn.appendTo( $el );
			
			$btn.click( function( ev ){
				ev.preventDefault();
				addChoice();
			});
			
			$hidden_delete.attr( 'type', 'hidden' );
			$hidden_delete.attr( 'name', 'choices_delete' );
			$hidden_delete.appendTo( $el );
			
			// ITERATE THROUGH EACH CHOICES IN THE DB
			jQuery.each( choices, function( i, choice ){
				if( choice['title'] != undefined && choice['ID'] != undefined ){
					addChoice( choice['title'], choice['ID'] );
				}
			});
			
		};
		
		init();
		
	});
};

jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=space-autoresize]').space_autoresize();
	
	jQuery('[data-behaviour~=space-choices]').space_choices();
	
} );
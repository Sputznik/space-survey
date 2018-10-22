jQuery.fn.space_autoresize = function(){
	return this.each(function() {
		var $el = jQuery(this);
		$el.attr('rows', 1);
		$el.autosize();
	});
};

jQuery.fn.space_choices = function(){

	return this.each(function() {
		
		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 			= jQuery(this),
			count 			= 0,												// INCREMENTING VARIABLE WHEN CHOICE IS ADDED
			choices 		= $el.attr( 'data-choices' ),						// CHOICES FROM THE DB
			$list 			= jQuery( document.createElement( 'ul' ) ),			// PARENT LIST THAT HOLDS THE CHOICES
			$btn			= jQuery( document.createElement( 'button' ) ),		// BUTTON THAT ADDS MORE BLANK CHOICES TO THE LIST
			$hidden_delete	= jQuery( document.createElement( 'input' ) ),		// HIDDEN FIELD THAT WILL KEEP A LIST OF ALL THE DELETED IDs
			deleted_list 	= [];												// LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED 
		
		// JSON PARSE FROM STRING
		choices = typeof choices != 'object' ? JSON.parse( choices ) : [];
		
		
		/*
		* ADD LIST ITEM TO THE UNLISTED LIST 
		* TEXTAREA: CHOICE TITLE
		* HIDDEN: CHOICE ID
		* HIDDEN: CHOICE COUNT
		*/ 
		var addChoice = function( choice_text, choice_id = 0 ){
			
			// CREATE PARENT LIST ITEM: LI
			var $list_item = jQuery( document.createElement( 'li' ) );
			$list_item.addClass( 'space-choice-item' );
			$list_item.appendTo( $list );
			
			// CREATE TEXTAREA THAT WILL HOLD THE CHOICE TEXT
			var $textarea = jQuery( document.createElement('textarea') );
			$textarea.attr( 'data-behaviour', 'space-autoresize' );
			$textarea.attr( 'placeholder', 'Type your choice here'  );
			$textarea.attr( 'name', 'choices[' + count + '][title]' );
			$textarea.appendTo( $list_item );
			$textarea.space_autoresize();
			if( choice_text ){ $textarea.val( choice_text ); }
			
			// CREATE HIDDEN FIELD THAT WILL HOLD THE CHOICE ID
			var $hiddenID = jQuery( document.createElement('input') );
			$hiddenID.attr( 'type', 'hidden' );
			$hiddenID.val( choice_id );
			$hiddenID.attr( 'name', 'choices[' + count + '][id]' );
			$hiddenID.appendTo( $list_item );
			
			// CREATE HIDDEN FIELD THAT WILL HOLD THE CHOICE RANK
			var $hiddenRank = jQuery( document.createElement('input') );
			$hiddenRank.attr( 'type', 'hidden' );
			$hiddenRank.attr( 'data-behaviour', 'space-rank' );
			$hiddenRank.val( '0' );
			$hiddenRank.attr( 'name', 'choices[' + count + '][rank]' );
			$hiddenRank.appendTo( $list_item );
			
			// CLOSE BUTTON - TO REMOVE THE LIST ITEM
			var $button = jQuery( document.createElement('button') );
			$button.addClass( 'space-close-btn' );
			$button.html( '&times;' );
			$button.appendTo( $list_item );
			
			$button.click( function( ev ){
				ev.preventDefault();
				// IF CHOICE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
				if( choice_id ){
					deleted_list.push( choice_id );
					$hidden_delete.val( deleted_list.join() );
				}
				$list_item.remove();
			});
			
			// INCREMENT THE COUNT TO MAINTAIN THE ARRAY OF INPUT NAMES
			count++;
		};
		
		var reorder = function(){
			$rank = 0;
			$list.find( '[data-behaviour~=space-rank]' ).each( function(){
				var $hiddenRank = jQuery( this );
				$hiddenRank.val( $rank );
				$rank++;
			});
		};
		
		/*
		* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE CHOICE, HIDDEN FIELD AND THE ADD BUTTON
		*/ 
		var init = function(){
			
			// MAIN LIST THAT HOLDS THE LIST OF CHOICES
			$list.attr('id', 'space-choices-list');
			$list.appendTo( $el );
			$list.sortable({
				stop: function( event, ui ){
					reorder();
				}
			});
		
			// ADD CHOICE BUTTON
			$btn.addClass('button');
			$btn.html( '+ Add Choice' );
			$btn.appendTo( $el );
			$btn.click( function( ev ){
				ev.preventDefault();
				addChoice();
			});
			
			// HIDDEN FIELD THAT KEEPS A RECORD OF CHOICE IDs WHICH NEEDS TO BE DELETED
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
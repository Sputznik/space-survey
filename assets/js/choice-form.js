jQuery.fn.space_autoresize = function(){
	return this.each(function() {
		var $el = jQuery(this);
		$el.attr('rows', 1);
		$el.autosize();
	});
};

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

var SPACE_REPEATER = function( options ){
	
	var self = {
		count	: 0,
		$list	: jQuery( document.createElement( 'ul' ) ),			// PARENT LIST THAT HOLDS THE CHOICES
		$btn 	: jQuery( document.createElement( 'button' ) ),		// BUTTON THAT ADDS MORE BLANK CHOICES TO THE LIST
		options : jQuery.extend( {
			$el		: undefined,
			btn_text: '+ Add Item',
			addItem	: function( item_data ){} 
		}, options )
	};
	
	self.init = function(){
		
		// MAIN LIST THAT HOLDS THE LIST OF CHOICES
		self.$list.attr('id', 'space-choices-list');
		self.$list.appendTo( self.options.$el );
		self.$list.sortable({
			stop: function( event, ui ){
				self.reorder();
			}
		});
		
		self.$btn.addClass('button');
		self.$btn.html( self.options.btn_text );
		self.$btn.appendTo( self.options.$el );
		self.$btn.click( function( ev ){
			ev.preventDefault();
			self.addItem();
		});
		
		self.options.init( self );
		
	};
	
	self.createField = function( field ){
		
		var $form_field = jQuery( document.createElement( field['element'] ) );
		
		for( attr in field['attr'] ){
			$form_field.attr( attr, field['attr'][attr] );
		}
		
		if( field['append'] ){
			$form_field.appendTo( field['append'] );
		}
		
		if( field['html'] ){
			$form_field.html( field['html'] );
		}
		
		return $form_field;
		
	};
	
	/*
	* ADD LIST ITEM TO THE UNLISTED LIST 
	*/
	self.addItem = function( $data ){
		
		// CREATE PARENT LIST ITEM: LI
		var $list_item = self.createField({
			element	: 'li',
			attr	:{
				'class'	: 'space-choice-item'
			},
			append	: self.$list
		});
		
		// CLOSE BUTTON - TO REMOVE THE LIST ITEM
		var $button = self.createField({
			element	: 'button',
			attr	:{
				'class'	: 'space-close-btn'
			},
			html	: '&times;',
			append	: $list_item
		});
		
		self.options.addItem( self, $list_item, $button, $data );
		
		// INCREMENT COUNT AFTER AN ITEM HAS BEEN ADDED TO MAINTAIN THE ARRAY OF INPUT NAMES
		self.count++;
		
	};
	
	self.reorder = function(){
		self.options.reorder( self );
	};
	
	self.init();
	
	return self;
};

jQuery.fn.space_choices = function(){

	return this.each(function() {
		
		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 			= jQuery(this),
			choices 		= $el.attr( 'data-choices' ),						// CHOICES FROM THE DB
			deleted_list 	= [];												// LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED 
		
		// JSON PARSE FROM STRING
		choices = typeof choices != 'object' ? JSON.parse( choices ) : [];
		
		var repeater = SPACE_REPEATER( {
			$el		: $el,
			btn_text: '+ Add Choice',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE CHOICE, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
				// HIDDEN FIELD THAT KEEPS A RECORD OF CHOICE IDs WHICH NEEDS TO BE DELETED
				var $hidden_delete	= repeater.createField({
					element: 'input',
					attr: {
						type: 'hidden',
						name: 'choices_delete'
					},	
					append: repeater.options.$el
				});
				
				// ITERATE THROUGH EACH CHOICES IN THE DB
				jQuery.each( choices, function( i, choice ){
					
					if( choice['title'] != undefined && choice['ID'] != undefined ){
						repeater.addItem( choice );
					}
				});
			},
			addItem	: function( repeater, $list_item, $closeButton, choice ){
				
				/*
				* ADD LIST ITEM TO THE UNLISTED LIST 
				* TEXTAREA: CHOICE TITLE
				* HIDDEN: CHOICE ID
				* HIDDEN: CHOICE COUNT
				*/ 
				if( choice == undefined || choice['ID'] == undefined ){
					choice = { ID : 0 };
				}
				
				// CREATE TEXTAREA THAT WILL HOLD THE CHOICE TEXT
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	: 'Type your choice here',
						'name'			: 'choices[' + repeater.count + '][title]',
					},
					append	: $list_item
				});
				$textarea.space_autoresize();
				if( choice['title'] ){ $textarea.val( choice['title'] ); }
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE CHOICE ID
				var $hiddenID = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'	: 'hidden',
						'value'	: choice['ID'] ? choice['ID'] : 0,
						'name'	: 'choices[' + repeater.count + '][id]'
					},
					append	: $list_item
				});
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE CHOICE RANK
				var $hiddenRank = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'				: 'hidden',
						'value'				: choice['rank'] ? choice['rank'] : 0,
						'data-behaviour' 	: 'space-rank',
						'name'				: 'choices[' + repeater.count + '][rank]'
					},
					append	: $list_item
				});
				
				$closeButton.click( function( ev ){
					ev.preventDefault();
					
					// IF CHOICE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
					if( choice['ID'] ){
						deleted_list.push( choice['ID'] );
						$hidden_delete.val( deleted_list.join() );
					}
					$list_item.remove();
				});
			},
			reorder: function( repeater ){
				/*
				* REORDER LIST 
				*/
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=space-rank]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );
		
	});
};


jQuery.fn.space_pages = function(){
	return this.each(function(){

		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 		 = jQuery(this),
			pages 		 = $el.attr( 'data-pages' ), // PAGES FROM THE DB
			deleted_list = [];						 // LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED 
		
		// JSON PARSE FROM STRING
		pages = typeof pages != 'object' ? JSON.parse( pages ) : [];
		
		var repeater = SPACE_REPEATER( {
			$el		: $el,
			btn_text: '+ Add Page',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE PAGE, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
				// HIDDEN FIELD THAT KEEPS A RECORD OF PAGE IDs WHICH NEEDS TO BE DELETED
				var $hidden_delete	= repeater.createField({
					element: 'input',
					attr: {
						type: 'hidden',
						name: 'pages_delete'
					},	
					append: repeater.options.$el
				});
				
				// ITERATE THROUGH EACH PAGES IN THE DB
				jQuery.each( pages, function( i, page ){
					
					if( page['title'] != undefined && page['ID'] != undefined ){
						repeater.addItem( page );
					}
				});
			},
			addItem	: function( repeater, $list_item, $closeButton, page ){
				
				/*
				* ADD LIST ITEM TO THE UNLISTED LIST 
				* TEXTAREA: PAGE TITLE
				* HIDDEN: PAGE ID
				* HIDDEN: PAGE COUNT
				*/ 
				if( page == undefined || page['ID'] == undefined ){
					page = { ID : 0 };
				}
				
				// CREATE TEXTAREA THAT WILL HOLD THE PAGE TEXT
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	: 'Type here',
						'name'			: 'pages[' + repeater.count + '][title]',
					},
					append	: $list_item
				});
				$textarea.space_autoresize();
				if( page['title'] ){ $textarea.val( page['title'] ); }
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE PAGE ID
				var $hiddenID = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'	: 'hidden',
						'value'	: page['ID'] ? page['ID'] : 0,
						'name'	: 'pages[' + repeater.count + '][id]'
					},
					append	: $list_item
				});
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE PAGE RANK
				var $hiddenRank = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'				: 'hidden',
						'value'				: page['rank'] ? page['rank'] : 0,
						'data-behaviour' 	: 'space-rank',
						'name'				: 'pages[' + repeater.count + '][rank]'
					},
					append	: $list_item
				});
				
				$closeButton.click( function( ev ){
					ev.preventDefault();
					
					// IF PAGE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
					if( page['ID'] ){
						deleted_list.push( page['ID'] );
						$hidden_delete.val( deleted_list.join() );
					}
					$list_item.remove();
				});
			},
			reorder: function( repeater ){
				/*
				* REORDER LIST 
				*/
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=space-rank]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );

	});	
};


jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=space-autoresize]').space_autoresize();
	
	jQuery('[data-behaviour~=space-choices]').space_choices();
	
	jQuery('[data-behaviour~=space-autocomplete]').space_autocomplete();

	jQuery('[data-behaviour~=space-pages]').space_pages();
	
} );
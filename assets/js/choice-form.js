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
		
		var $hidden_delete; // INITIALIZED LATER WITHIN THE INIT FUNCTION
		
		var repeater = SPACE_REPEATER( {
			$el		: $el,
			btn_text: '+ Add Choice',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE CHOICE, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
				// HIDDEN FIELD THAT KEEPS A RECORD OF CHOICE IDs WHICH NEEDS TO BE DELETED
				$hidden_delete	= repeater.createField({
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

jQuery.fn.space_questions = function( parent_name ){
	return this.each(function() {
		

		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 			= jQuery(this),
			questions 		= $el.attr( 'data-questions' ),	// QUESTIONS FROM THE DB
			deleted_list 	= []; // LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED 
		
		// JSON PARSE FROM STRING
		questions = typeof questions != 'object' ? JSON.parse( questions ) : [];
		
		var $hidden_delete; // INITIALIZED LATER WITHIN THE INIT FUNCTION
		
		var repeater = SPACE_REPEATER( {
			$el				: $el,
			btn_text		: '+ Add Question',
			list_id			: 'space-questions-list',
			list_item_id	: 'space-question-item',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE QUESTION, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
				// HIDDEN FIELD THAT KEEPS A RECORD OF QUESTION IDs WHICH NEEDS TO BE DELETED
				$hidden_delete	= repeater.createField({
					element: 'input',
					attr: {
						type: 'hidden',
						name: 'questions_delete'
					},	
					append: repeater.options.$el
				});
				
				// ITERATE THROUGH EACH QUESTIONS IN THE DB
				jQuery.each( questions, function( i, question ){
					
					if( question['title'] != undefined && question['ID'] != undefined ){
						repeater.addItem( question );
					}
				});
			},
			addItem	: function( repeater, $list_item, $closeButton, question ){
				
				/*
				* ADD LIST ITEM TO THE UNLISTED LIST 
				* TEXTAREA: QUESTION TITLE
				* HIDDEN: QUESTION ID
				* HIDDEN: QUESTION COUNT
				*/
				
				if( question == undefined || question['ID'] == undefined ){
					question = { ID : 0 };
				}
				
				// CREATE TEXTAREA THAT WILL HOLD THE QUESTION TEXT
				var $question_div = repeater.createField({
					element	: 'div',
					attr	: {
						'data-behaviour': 'space-autocomplete',	
						'data-field'	: JSON.stringify( {
							slug				: parent_name + '[question]['+ repeater.count +']',
							type				: 'autocomplete',
							placeholder			: "Type title of the question here",
							url					: "",
							value				: "",
							autocomplete_value	: ""
						} ),
					},
					append	: $list_item
				});
				$question_div.space_autocomplete();
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE QUESTION ID
				var $hiddenID = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'	: 'hidden',
						'value'	: question['ID'] ? question['ID'] : 0,
						'name'	: parent_name+'[questions][' + repeater.count + '][id]'
					},
					append	: $list_item
				});
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE QUESTION RANK
				var $hiddenRank = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'				: 'hidden',
						'value'				: question['rank'] ? question['rank'] : 0,
						'data-behaviour' 	: 'space-rank',
						'name'				: parent_name+'[questions][' + repeater.count + '][rank]'
					},
					append	: $list_item
				});
				
				$closeButton.click( function( ev ){
					ev.preventDefault();
					
					// IF QUESTION ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
					if( question['ID'] ){
						deleted_list.push( question['ID'] );
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

	return this.each(function() {
		
		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 			= jQuery(this),
			pages 			= $el.attr( 'data-pages' ),	// PAGES FROM THE DB
			deleted_list 	= []; // LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED 
		
		// JSON PARSE FROM STRING
		pages = typeof pages != 'object' ? JSON.parse( pages ) : [];
		
		var $hidden_delete; // INITIALIZED LATER WITHIN THE INIT FUNCTION
		
		var repeater = SPACE_REPEATER( {
			$el		: $el,
			btn_text: '+ Add page',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE PAGE, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
				// HIDDEN FIELD THAT KEEPS A RECORD OF PAGE IDs WHICH NEEDS TO BE DELETED
				$hidden_delete	= repeater.createField({
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
				* TEXTAREA: page TITLE
				* HIDDEN: page ID
				* HIDDEN: page COUNT
				*/
				
				if( page == undefined || page['ID'] == undefined ){
					page = { ID : 0 };
				}
				
				// CREATE NEAT HEADER AREA FOR THE PAGE ITEM
				var $header = repeater.createField({
					element	: 'div',
					attr	: {
						'class'	: 'list-header'
					},
					append	: $list_item
				});
				
				// CREATE TEXTAREA THAT WILL HOLD THE PAGE TEXT AND APPEND TO THE HEADER
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	: 'Type Page Title Here',
						'name'			: 'pages[' + repeater.count + '][title]',
						'value'			: 'Page ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				$textarea.space_autoresize();
				if( page['title'] ){ $textarea.val( page['title'] ); }
				
				// CREATE NEAT CONTENT AREA FOR THE PAGE ITEM
				var $content = repeater.createField({
					element	: 'div',
					attr	: {
						'class'	: 'list-content'
					},
					append	: $list_item
				});
				
				// CREATE TEXTAREA FOR HOLDING PAGE DESCRIPTION
				var $textarea_desc = repeater.createField({
					element	: 'textarea',
					attr	: {
						'placeholder'	: 'Type Page Description Here',
						'name'			: 'pages[' + repeater.count + '][description]',
						'rows'			: '3',
						'class' 		: 'form_page_desc',
					},
					append	: $content
				});
				if( page['description'] ){ $textarea_desc.val( page['description'] ); }
	
				//ADD BUTTON FOR QUESTION REPEATER
				var $question_repeater = repeater.createField({
					element : 'div',
					attr 	: {
						'data-questions' : '[]',
						'data-behaviour' : 'space-questions',
						'class' : 'space-box'
					},
					append 	: $content
				});
				$question_repeater.space_questions( 'pages[' + repeater.count + ']' );

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
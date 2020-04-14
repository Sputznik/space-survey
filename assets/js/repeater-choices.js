jQuery.fn.space_choices = function(){

	return this.each(function() {

		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 				= jQuery(this),
			choices 			= window.browserData['choices'] != undefined ? window.browserData['choices'] : [],		// CHOICES FROM THE DB
			deleted_list 	= [];												// LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED

		var $hidden_delete; 												// INITIALIZED LATER WITHIN THE INIT FUNCTION

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

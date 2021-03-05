jQuery.fn.space_pages = function(){

	return this.each(function() {

		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 				= jQuery(this),
			pages 				= window.browserData['pages'] != undefined ? window.browserData['pages'] : [],	// PAGES FROM THE DB
			deleted_list 	= []; // LIST OF ID THAT HAVE BEEN REMOVED WHEN THE CLOSE BUTTON IS CLICKED


		var $hidden_delete; // INITIALIZED LATER WITHIN THE INIT FUNCTION

		var repeater = SPACE_REPEATER( {
			$el				: $el,
			btn_text		: '+ Add page',
			list_id			: 'space-pages-list',
			list_item_id	: 'space-page-item',
			close_btn_text	: 'Delete Page',
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

				// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );

				// PAGE TITLE
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

				// CREATE BOOLEAN FIELD FOR REQUIRED
				var checked_flag = false;
				if( page['description'] && page['description'].length ){
					checked_flag = true;
				}
				var $desc_flag = repeater.createBooleanField({
					attr	:  {
						name	: 'has_desc[]',
						checked : checked_flag
					},
					append	: $content,
					label	: 'Has Description'
				});

				// PAGE DESCRIPTION
				var $textarea_desc = repeater.createRichText({
					attr	: {
						'id'			: 'pages-' + repeater.count,
						'name'			: 'pages[' + repeater.count + '][description]',
						'class' 		: 'form_page_desc',
						'value'			: page['description'] ? page['description'] : ''
					},
					append	: $content
				});

				//ADD BUTTON FOR QUESTION REPEATER
				var $question_repeater = repeater.createField({
					element : 'div',
					attr 	: {
						'data-questions' : page['questions'] ? JSON.stringify( page['questions'] ) : "[]",
						'data-behaviour' : 'space-questions',
						'class' 		: 'space-box'
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
						'type'						: 'hidden',
						'value'						: page['menu_rank'] ? page['menu_rank'] : 0,
						'data-behaviour' 	: 'space-pages-rank',
						'name'						: 'pages[' + repeater.count + '][menu_rank]'
					},
					append	: $list_item
				});

				$closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						// IF PAGE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
						if( page['ID'] ){
							deleted_list.push( page['ID'] );
							$hidden_delete.val( deleted_list.join() );
						}
						$list_item.remove();
					}
				});


				// CHECK IF THE REQUIRED CHECKBOX IS CHECKED OR NOT TO HIDE/SHOW THE RULES REPEATER
				var $desc_flag_checkbox = $desc_flag.find('input[type=checkbox]');
				$desc_flag_checkbox.change( function(){
					var $editor = $content.find('.wp-editor-wrap');
					// IF REQUIRED THEN HIDE THE RULES OTHERWHISE SHOW THEM
					if( $desc_flag_checkbox.prop('checked') ){
						$editor.show();
					}
					else{
						$editor.hide();
					}
				});
				$desc_flag_checkbox.change(); // TO CHECK FOR THE FIRST TIME



			},
			reorder: function( repeater ){
				/*
				* REORDER LIST
				*/
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=space-pages-rank]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );

		//console.log(  $el.closest('form').length );

		// REORDER THE ENTIRE FORM BEFORE THE FORM IS UPDATED
		$el.closest('form').submit( function( ev ){
			repeater.reorder();
		});


	});
};

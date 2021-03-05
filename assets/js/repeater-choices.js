var IMPORT_BUTTON = function( options ){

	var self = {
		$importDiv		: null,
		options : jQuery.extend( {
			$el						: null,
			ajax_action		: '',
			ajaxResponse 	: function(){},
			btn_text			: 'Import',
			form_title		: 'Import from CSV File',
			append_to			: '#wpbody'
		}, options )
	};

	self.init = function(){
		var $div = jQuery( document.createElement('div') );
		$div.css({
			display				: 'inline',
			marginLeft		: '15px',
			paddingTop		: '5px',
			verticalAlign	: 'middle'
		});
		$div.appendTo( self.options.$el );
		$div.html('Or&nbsp;&nbsp;')

		var $importBtn = jQuery(document.createElement('a'));
		$importBtn.prop( 'href', '#' );
		$importBtn.html( self.options.btn_text );
		$importBtn.appendTo( $div );

		$importBtn.click( function( ev ){
			ev.preventDefault();
			self.showImportScreen();
		} );
	}

	self.ajaxUpload = function(){
		var fd = new FormData();
		var file = self.$importDiv.find('input[type="file"]');
		var individual_file = file[0].files[0];
		fd.append("file", individual_file);

		jQuery.ajax({
				type: 'POST',
				url: ajaxurl + '?action=' + self.options.ajax_action,
				data: fd,
				dataType: 'json',
				contentType: false,
				processData: false,
				success: function( response ){
					self.options.ajaxResponse( response );
					self.remove();
				}
		});
	}

	self.remove = function(){
		self.$importDiv.remove();
	}

	self.showImportScreen = function(){
		self.$importDiv = jQuery( document.createElement('div') );
		self.$importDiv.addClass('space-import-screen');
		self.$importDiv.appendTo( self.options.append_to );

		var $form = jQuery( document.createElement( 'form' ) );
		$form.html('<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="file">' + self.options.form_title + '</label></p><p><input name="file" type="file" id="file" value=""></p>');
		$form.appendTo( self.$importDiv );

		var $closeBtn = jQuery( document.createElement('button') );
		$closeBtn.addClass('space-close-btn');
		$closeBtn.html( '&times;' );
		$closeBtn.appendTo( $form );
		$closeBtn.click( function( ev ){
			ev.preventDefault();
			self.remove();
		} );

		var $button = jQuery( document.createElement('button') );
		$button.addClass('button');
		$button.html( 'Import' );
		$button.appendTo( $form );
		$button.click( function( ev ){
			ev.preventDefault();
			self.ajaxUpload();
		} );

		// SCROLL TO THAT POSITION OF THE IMPORT SCREEN
		jQuery('html, body').animate({
			'scrollTop' : self.$importDiv.offset().top - 150
		});
	}

	self.init();

	return self;

};

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
						'type'						: 'hidden',
						'value'						: choice['menu_rank'] ? choice['menu_rank'] : 0,
						'data-behaviour' 	: 'space-rank',
						'name'						: 'choices[' + repeater.count + '][menu_rank]'
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

		IMPORT_BUTTON({
			$el					: $el,
			repeater		: repeater,
			btn_text		: 'Import Choices',
			ajax_action	: 'space_import_choices_csv',
			ajaxResponse : function( choicesArray ){
				for( i=0; i<choicesArray.length; i++ ){
					repeater.addItem( { ID:0, title: choicesArray[i] } );
				}
			}
		});

	});
};

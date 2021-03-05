var IMPORT_BUTTON = function( options ){

	var self = {
		$importDiv		: null,
		options : jQuery.extend( {
			$el						: null,
			ajax_url			: '',
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

	self.getAjaxURL = function(){
		return ajaxurl + '?action=' + self.options.ajax_action;
	}

	self.ajaxUpload = function(){

		console.log( self.options.ajax_url );

		var fd = new FormData();
		var file = self.$importDiv.find('input[type="file"]');
		var individual_file = file[0].files[0];
		fd.append("file", individual_file);

		jQuery.ajax({
				type				: 'POST',
				url					: self.options.ajax_url,
				data				: fd,
				dataType		: 'json',
				contentType	: false,
				processData	: false,
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

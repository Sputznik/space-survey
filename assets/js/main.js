// ADD IMAGE ICON TO THE WP EDITOR 
jQuery( document ).on( 'tinymce-editor-setup', function( event, editor ) {
				
	editor.settings.toolbar1 += ',image';
		
	editor.addButton( 'image', {
		text	: '',
		icon	: 'image',
		tooltip	: '',
		onclick	: function (event) {
			
			/* ON CLICK OF THE BUTTON, OPEN THE MODAL */
			var elem = $( event.currentTarget ),
				editor = elem.data('editor'),
				options = {
					frame:    'post',
					state:    'insert',
					title:    wp.media.view.l10n.addMedia,
					multiple: true
				};

			event.preventDefault();

			wp.media.editor.open( editor, options );
						
		}
	});
});


jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=space-autoresize]').space_autoresize();
	
	jQuery('[data-behaviour~=space-choices]').space_choices();
	
	jQuery('[data-behaviour~=space-autocomplete]').space_autocomplete();

	jQuery('[data-behaviour~=space-pages]').space_pages();
	
} );
/*
* BASE CLASS FOR REPEATER FIELD
* @dependency: JQUERY
* LIST ELEMENT: THAT CONTAINS ALL THE REPEATER ITEMS
* BUTTON: THAT ADDS REPEATER ITEMS TO THE LIST
*/

var SPACE_REPEATER = function( options ){

	var self = {
		count	: 0,		// KEEP A COUNT OF THE ITEMS THAT HAVE BEEN ADDED
		$list	: null,		// PARENT LIST THAT HOLDS THE CHOICES
		$btn 	: null,		// BUTTON THAT ADDS MORE BLANK CHOICES TO THE LIST
		options : jQuery.extend( {
			$el				: null,
			btn_text		: '+ Add Item',
			close_btn_text	: '&times;',
			list_id			: 'space-choices-list',
			list_item_id	: 'space-choice-item',
			init			: function(){},
			addItem			: function(){},
			reorder 		: function(){}
		}, options )
	};

	self.init = function(){

		// MAIN LIST THAT HOLDS THE LIST OF CHOICES
		self.$list = self.createField({
			element: 'ul',
			attr:{
				id	: self.options.list_id
			},
			append	: self.options.$el
		});
		self.$list.sortable({
			stop: function( event, ui ){
				self.reorder();
			}
		});

		// BUTTON THAT ADDS THE REPEATER ITEM
		self.$btn = self.createField({
			element	: 'button',
			attr: {
				class: 'button'
			},
			html	: self.options.btn_text,
			append	: self.options.$el
		});

		self.$btn.click( function( ev ){
			ev.preventDefault();
			self.addItem();
		});

		// FOR CUSTOM FUNCTIONALITY
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

		if( field['prepend'] ){
			$form_field.prependTo( field['prepend'] );
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
				'class'	: self.options.list_item_id
			},
			append	: self.$list
		});

		// CLOSE BUTTON - TO REMOVE THE LIST ITEM
		var $button = self.createField({
			element	: 'button',
			attr	:{
				'class'	: 'space-close-btn'
			},
			html	: self.options.close_btn_text,
			append	: $list_item
		});

		self.options.addItem( self, $list_item, $button, $data );

		// INCREMENT COUNT AFTER AN ITEM HAS BEEN ADDED TO MAINTAIN THE ARRAY OF INPUT NAMES
		self.count++;

	};

	/*
	* CREATE BASIC MARKUP FOR COLLAPSIBLE ITEM
	* HEADER & CONTENT AREA
	*/
	self.addCollapsibleItem = function( $list_item, $closeButton ){

		// CREATE NEAT HEADER AREA FOR THE ITEM
		var $header = self.createField({
			element	: 'div',
			attr	: {
				'class'	: 'list-header'
			},
			append	: $list_item
		});

		// CREATE NEAT CONTENT AREA FOR THE ITEM
		var $content = self.createField({
			element	: 'div',
			attr	: {
				'class'	: 'list-content'
			},
			append	: $list_item
		});

		// APPEND THE CLOSE BUTTON TO THE LIST CONTENT
		$closeButton.appendTo( $content );

		// BUTTON THAT COLLAPSES THE ENTIRE LIST
		var $collapseBtn = self.createField({
			element	: 'button',
			attr	: {
				class : 'space-collapse'
			},
			append 	: $header
		});

		// ON CLICK OF COLLAPSE BUTTON, TOGGLE THE CONTENT AREA
		$collapseBtn.click( function( ev ){
			ev.preventDefault();
			$content.slideToggle();
		});
		$collapseBtn.click();

	};

	/*
	* TINYMCE EDITOR
	*/
	self.createRichText = function( field ){

		field['element'] = 'textarea';
		field['attr'] = field['attr'] ? field['attr'] : {};
		field['attr']['id'] = field['attr']['id'] ? field['attr']['id'] : 'sample-id';

		var $textarea = self.createField( field );

		// INITIALIZE WP EDITOR FOR THE TEXTAREA
		wp.editor.initialize( field['attr']['id'], { tinymce: {height: 300}, quicktags: true } );

		return $textarea;

	};

	/*
	* BOOLEAN FIELD
	*/
	self.createBooleanField = function( field ){

		var $label = self.createField({
			element	: 'label',
			append	: field['append'],
			html	: field['label']
		});

		var $booleanField = self.createField({
			element	: 'input',
			attr	: {
				type	: 'checkbox',
				name	: field['attr']['name'],
				checked	: field['attr']['checked'],
				value	: 1
			},
			prepend	: $label
		});

		return $label;

	};

	/*
	* DROPDOWN FIELD
	*/
	self.createDropdownField = function( field ){

		var $wrapper = self.createField({
			element	: 'div',
			attr	: {
				class : 'space-dropdown'
			},
			append	: field['append']
		});

		var $label = self.createField({
			element	: 'label',
			append	: $wrapper,
			html	: field['label']
		});

		var $select = self.createField({
			element	: 'select',
			attr	: {
				name : field['attr']['name']
			},
			append	: $wrapper,
		});

		if( field['multiple'] ){
			$select.attr( 'multiple', 'true' );
		}

		// ADD ONE OPTION TO THE SELECT DROPDOWN
		$wrapper.addOption = function( slug, value ){

			var $option = self.createField({
				element	: 'option',
				attr	: {
					value : slug
				},
				html	: value,
				append	: $select
			});

		};

		// SET THE ENTIRE OPTIONS FOR THE SELECT DROPDOWN
		$wrapper.setOptions = function( options ){
			// FIRST REMOVE ALL THE CURRENT OPTIONS THAT ARE THERE
			$select.find('option').remove();
			for( slug in options ){
				$wrapper.addOption( slug, options[slug] );
			}
		}

		$wrapper.selectOption = function( slug ){
			$select.val( slug );
		};

		if( field['options'] ){
			$wrapper.setOptions( field['options'] );
		}


		if( field['value'] ){
			$wrapper.selectOption( field['value'] );
		}

		return $wrapper;
	};

	self.reorder = function(){
		self.options.reorder( self );
	};

	self.init();

	return self;
};

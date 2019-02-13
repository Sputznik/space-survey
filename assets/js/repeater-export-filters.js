jQuery.fn.space_export_filters = function(){
	
	return this.each(function() {
		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el = jQuery(this);
			
		var repeater = SPACE_REPEATER( {
			$el				: $el,
			btn_text		: '+ Add Filter',
			list_id			: 'space-filters',
			list_item_id	: 'space-filter-item',
			addItem	: function( repeater, $list_item, $closeButton, rule ){
				
				/*
				* ADD LIST ITEM TO THE UNLISTED LIST 
				* DROPDOWN		: ACTIONS (SHOW, HIDE)
				* AUTOCOMPLETE	: QUESTIONS
				* DROPDOWN		: CHOICES
				*/
				
				if( rule == undefined || rule['action'] == undefined ){
					rule = {};
				}
				
				// WRAPPER THAT ENSURES THE FIELDS WILL BE IN GRID FORMAT
				var $ruleGridWrapper = repeater.createField({
					element	: 'div',
					attr	: {
						class : 'space-rule-grid'
					},
					append	: $list_item
				});
				
				// CREATE AUTOCOMPLETE THAT WILL HOLD THE QUESTION TEXT
				var $question_div = repeater.createField({
					element	: 'div',
					attr	: {
						'data-behaviour': 'space-autocomplete',	
						'data-field'	: JSON.stringify( {
							slug				: 'rules['+ repeater.count +'][question]',
							type				: 'autocomplete',
							placeholder			: "Type title of the question here",
							url					: space_settings['ajax_url'] + '?action=space_questions',
							value				: "",
							autocomplete_value	: "",
							label				: 'When Question'
						} ),
					},
					append	: $ruleGridWrapper
				});
				$question_div.space_autocomplete();
				
				// VALUE OF QUESTION TO BE SELECTED FOR THE RULE
				var $value = repeater.createDropdownField({
					attr	:  {
						name	: 'rules['+ repeater.count +'][value]',
					},
					append	: $ruleGridWrapper,
					label	: 'Has value'
				});
				
				
				// HIDDEN TEXTAREA THAT WILL HOLD THE ADDITIONAL INFORMATION ABOUT THE QUESTION
				var $textarea = repeater.createField({
					element : 'textarea',
					attr 	: {
						name	: 'rules['+ repeater.count +'][data]',
					},
					html	: '',
					append	: $list_item,
				});
				$textarea.hide();
				
				// UPDATE THE OPTIONS WHEN ITEM FROM THE AUTOCOMPLETE HAS BEEN SELECTED
				$question_div.on('space_autocomplete:select', function( ev, question_data ){
					$textarea.html( JSON.stringify( question_data ) );
					if( question_data['choices'] ){
						$value.setOptions( question_data['choices'] );
					}
				});
				
				// HANDLE CLOSE BUTTON EVENT
				$closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						$list_item.remove();
					}
				});
				
			},
			
		} );
	});
};
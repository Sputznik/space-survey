jQuery.fn.space_rules = function( parent_name ){

	return this.each(function() {
		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 	= jQuery(this),
			rules 	= $el.attr( 'data-rules' );	// RULES FROM THE DB

		// JSON PARSE FROM STRING
		rules = typeof rules != 'object' ? JSON.parse( rules ) : [];

		var repeater = SPACE_REPEATER( {
			$el				: $el,
			btn_text		: '+ Add Rule',
			list_id			: 'space-rules-list',
			list_item_id	: 'space-rule-item',
			init	: function( repeater ){

				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE QUESTION, HIDDEN FIELD AND THE ADD BUTTON
				*/

				// ITERATE THROUGH EACH QUESTIONS IN THE DB
				jQuery.each( rules, function( i, rule ){

					if( rule['question'] != undefined && rule['value'] != undefined && rule['data'] != undefined ){
						rule['data'] = typeof rule['data'] != 'object' ? JSON.parse( rule['data'] ) : {};
						repeater.addItem( rule );
					}
				});
			},
			addItem	: function( repeater, $list_item, $closeButton, rule ){

				/*
				* ADD LIST ITEM TO THE UNLISTED LIST
				* DROPDOWN		: ACTIONS (SHOW, HIDE)
				* AUTOCOMPLETE	: QUESTIONS
				* DROPDOWN		: CHOICES
				*/
				console.log( rule );
				if( rule == undefined ){
					rule = { data : {} };
				}

				// WRAPPER THAT ENSURES THE FIELDS WILL BE IN GRID FORMAT
				var $ruleGridWrapper = repeater.createField({
					element	: 'div',
					attr	: {
						class : 'space-rule-grid'
					},
					append	: $list_item
				});

				/*
				var $action = repeater.createDropdownField({
					attr	:  {
						name	: parent_name + '[rules]['+ repeater.count +'][action]',
					},
					options : {
						show	: 'Show',
						//hide	: 'Hide'
					},
					value	: rule['action'],
					append	: $ruleGridWrapper,
					label	: 'Action'
				});
				*/

				// CREATE AUTOCOMPLETE THAT WILL HOLD THE QUESTION TEXT
				var $question_div = repeater.createField({
					element	: 'div',
					attr	: {
						'data-behaviour'		: 'space-autocomplete',
						'data-field'				: JSON.stringify( {
							slug							: parent_name + '[rules]['+ repeater.count +'][question]',
							type							: 'autocomplete',
							placeholder				: "Type title of the question here",
							url								: space_settings['ajax_url'] + '?action=space_questions',
							value							: rule['question'] ? rule['question'] : "",
							autocomplete_value: rule['data']['label'] ? rule['data']['label'] : "",
							label							: 'Show when question'
						} ),
					},
					append	: $ruleGridWrapper
				});
				$question_div.space_autocomplete();

				// VALUE OF QUESTION TO BE SELECTED FOR THE RULE
				var $value = repeater.createDropdownField({
					attr	:  {
						name	: parent_name + '[rules]['+ repeater.count +'][value][]',
					},
					append		: $ruleGridWrapper,
					multiple	: true,
					label			: 'Has value'
				});
				if( rule['data']['choices'] ){ // RESET OPTIONS WITHIN THE DROPDOWN
					$value.setOptions( rule['data']['choices'] );
				}
				if( rule['value'] ){	// SELECT THE OPTION THAT COMES FROM THE DB
					$value.selectOption( rule['value'] );
				}

				// HIDDEN TEXTAREA THAT WILL HOLD THE ADDITIONAL INFORMATION ABOUT THE QUESTION
				var $textarea = repeater.createField({
					element : 'textarea',
					attr 	: {
						name	: parent_name + '[rules]['+ repeater.count +'][data]',
					},
					html	: JSON.stringify( rule['data'] ),
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

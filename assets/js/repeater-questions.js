jQuery.fn.space_questions = function( parent_name ){
	
	return this.each(function() {
		/*
		* VARIABLES ASSIGNMENT
		*/
		var $el 			= jQuery(this),
			questions 		= $el.attr( 'data-questions' );	// QUESTIONS FROM THE DB
			
		// JSON PARSE FROM STRING
		questions = typeof questions != 'object' ? JSON.parse( questions ) : [];
		
		var repeater = SPACE_REPEATER( {
			$el				: $el,
			btn_text		: '+ Add Question',
			list_id			: 'space-questions-list',
			list_item_id	: 'space-question-item',
			close_btn_text	: 'Remove Question',
			init	: function( repeater ){
				
				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE QUESTION, HIDDEN FIELD AND THE ADD BUTTON
				*/
				
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
				* AUTOCOMPLETE: QUESTION TITLE
				* HIDDEN: QUESTION ID
				* HIDDEN: QUESTION COUNT
				*/
				
				if( question == undefined || question['ID'] == undefined ){
					question = { ID : 0 };
				}
				
				// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT 
				repeater.addCollapsibleItem( $list_item, $closeButton );
				
				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );
				
				// CREATE AUTOCOMPLETE THAT WILL HOLD THE QUESTION TEXT
				var $question_div = repeater.createField({
					element	: 'div',
					attr	: {
						'data-behaviour': 'space-autocomplete',	
						'data-field'	: JSON.stringify( {
							slug				: parent_name + '[questions]['+ repeater.count +'][id]',
							type				: 'autocomplete',
							placeholder			: "Type title of the question here",
							url					: space_settings['ajax_url'] + '?action=space_questions',
							value				: question['ID'] ? question['ID'] : "",
							autocomplete_value	: question['title'] ? question['title'] : "",
						} ),
					},
					append	: $header
				});
				$question_div.space_autocomplete();
				
				// CREATE BOOLEAN FIELD FOR REQUIRED
				var checked_flag = false;
				if( space_settings.required_questions && space_settings.required_questions.length && question['ID'] ){
					if( space_settings.required_questions.indexOf( question['ID'] ) > -1 ){
						checked_flag = true;
					}
				}
				var $required = repeater.createBooleanField({
					attr	:  {
						name	: parent_name + '[questions]['+ repeater.count +'][required]',
						checked : checked_flag
					},
					append	: $content,
					label	: 'Required'
				});
				
				// GET RULES FROM DB
				var rules_for_question = [];
				if( space_settings['rules'] && space_settings['rules'][ question['ID'] ] ){
					rules_for_question = space_settings.rules[ question['ID'] ];
				}
				
				//ADD BUTTON FOR RULES REPEATER
				var $rules_repeater = repeater.createField({
					element : 'div',
					attr 	: {
						'data-rules' 	 : JSON.stringify( rules_for_question ),
						'data-behaviour' : 'space-rules',
						'class' 		 : 'space-rules-box'
					},
					append 	: $content
				});
				$rules_repeater.space_rules( parent_name + '[questions]['+ repeater.count +']' );
				
				
				// CHECK IF THE REQUIRED CHECKBOX IS CHECKED OR NOT TO HIDE/SHOW THE RULES REPEATER
				var $required_checkbox = $required.find('input[type=checkbox]');
				$required_checkbox.change( function(){
					
					// IF REQUIRED THEN HIDE THE RULES OTHERWHISE SHOW THEM
					if( $required_checkbox.prop('checked') ){	$rules_repeater.hide(); }
					else{ $rules_repeater.show(); }
					
				});
				$required_checkbox.change(); // TO CHECK FOR THE FIRST TIME
				
				
				// CREATE HIDDEN FIELD THAT WILL HOLD THE QUESTION RANK
				var $hiddenRank = repeater.createField({
					element	: 'input', 
					attr	: {
						'type'				: 'hidden',
						'value'				: question['rank'] ? question['rank'] : repeater.count,
						'data-behaviour' 	: 'space-rank',
						'name'				: parent_name+'[questions][' + repeater.count + '][rank]'
					},
					append	: $content
				});
				
				// HANDLE CLOSE BUTTON EVENT
				$closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						$list_item.remove();
					}
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
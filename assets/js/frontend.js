jQuery.fn.space_slides = function(){

	return this.each(function() {

		var $el = jQuery( this ), autosave, slides;

		/*
		*	GET UNIQUE GUEST ID BY USING COOKIES
		*/
		function guestData( callbackFunction ){

			jQuery.ajax({
				type			:	'GET',
				url				: space_settings.ajax_url,
				dataType	: 'json',
				data:	{
					action		: 'space_survey_guest',
					survey_id : getSurveyID()
				},
				success	: function( response ){

					// SET GUEST ID WITHIN THE FORM
					setGuestID( response.guest_id );

					//console.log( response.responses );

					// SET ANSWERS OF THE GUEST THAT WERE RECORDED PREVIOUSLY
					jQuery.each( response.responses, function( i, row ){
						setAnswerForQuestion( row );
					});

					// CALLBACK FUNCTION AFTER THE GUEST DATA HAS BEEN SET IN THE FORM
					callbackFunction();

				},
				error: function(response) {
					console.log( response.responseText );
					console.log( 'Cookie could not be created' );
				}
			});

		}

		// MAP GUEST RESPONSE ON THE FORM SELECTION
		function setAnswerForQuestion( response ){

			var $questionDiv = $el.find( '#q' + response.question_id ),
				questionType = $questionDiv.data('type');

			//console.log( response );

			switch( questionType ){

				case 'radio':

				case 'checkbox-ranking':

				case 'checkbox':
					var $questionInput = $questionDiv.find('input[value=' + response.choice_id + ']');
					$questionInput.prop("checked", false);  // UNCHECK CHECKBOX IN CASE IT IS CHECKED BECAUSE OF CACHE
					$questionInput.click();

					if( response.choice_text ){
						var $questionInput = $questionDiv.find('input[type=text]');
						$questionInput.val( response.choice_text );
						$questionInput.change();
					}

					break;

				case 'dropdown':
					var $questionInput = $questionDiv.find('select');
					$questionInput.val( response.choice_id );
					$questionInput.change();
					break;

				case 'text':
					var $questionInput = $questionDiv.find('input[type=text]');
					$questionInput.val( response.choice_text );
					$questionInput.change();
					break;

			}

		}

		function getSurveyID(){
			return parseInt( $el.find('input[name=survey_id]').val() );
		}

		function getGuestID(){
			return parseInt( $el.find('input[name=guest_id]').val() );
		}

		function setGuestID( guest_id ){
			$el.find('input[name=guest_id]').val( guest_id );
		}


		function saveGuestData(){

			console.log('save guest data');

			var form = $el.find('form');
			jQuery.ajax({
				type		: 'POST',
				url			: space_settings.ajax_url + '?action=' + 'space_survey_save',
				dataType: 'json',
				data		: form.serialize(),
				success	: function( response ){
					console.log( response );
				},
				error 	: function(response) {
					console.log( response );
				}
			});
		}



		function validate(){

			hideErrorMessage();

			var $slide 		= slides.getCurrentSlide(),
				$nextSlide	= slides.getNextSlide(),
				totalQuest	= $slide.find('.space-question.required:not(.hide)').length,
				doneQuest	= $slide.find('.space-question.required.done:not(.hide)').length;

			// REMOVE ERROR CLASS FROM FIELDS THAT ARE DONE AND REQUIRED
			$slide.find('.space-question.required.done:not(.hide)').removeClass('error');

			if( totalQuest == doneQuest ){
				// AUTOSAVE ONLY IF THE QUESTIONS IN THE CURRENT SLIDE ARE DONE
				if( autosave ){ autosave.save(); }
				else{ console.log( "Autosave has not been initialised properly. Check the flow for autosave." ); }
			}
			else{
				var pendingQuests = $slide.find( '.space-question.required:not(.done):not(.hide)' );
				if( pendingQuests.length ){
					pendingQuests.addClass( 'error' );
					var firstPendingQuest = pendingQuests.first();
					jQuery( 'html, body' ).animate( { scrollTop : firstPendingQuest.offset().top-200 }, 1000 );
				}

				var errorMessage = 'Some required fields have not been filled';
				if( window.browserData['space_survey_settings']['error-missing-text'] != undefined ){
					errorMessage = window.browserData['space_survey_settings']['error-missing-text'];
				}

				showErrorMessage( errorMessage );

				throw "Required form fields are not filled.";
			}

		}

		function init(){

			guestData( function(){
				// SET UP AUTOSAVE AFTER THE GUEST DATA IS SET IN THE FORM
				autosave = SPACE_AUTOSAVE( {
					duration: 4000,
					save		: saveGuestData
				} );
			} );

			slides = SPACE_SLIDES( {
				$el : $el,
				preNextTransition	: validate,
				prePrevTransition : hideErrorMessage
			} );

		}

		function showErrorMessage( message ){
			var $error = $el.find('.space-error');
			$error.html( message );
			$error.show();
		}

		function hideErrorMessage(){
			var $error = $el.find('.space-error');
			$error.hide();
		}


		// HANDLE CHANGE ON INPUTS TO MARK THE PARENT CLASS WITH 'done'
		$el.find('.space-question').each( function(){

			var $questionDiv = jQuery( this ),
				questionType = $questionDiv.data('type');

			switch( questionType ){

				case 'radio':
					var $questionInput = $questionDiv.find('input[type="radio"]');

					// ON CLICK OF THE RADIO BUTTON IT GETS SELECTED
					$questionInput.click( function( ev ){
						$questionDiv.addClass('done');
					});
					break;

				case 'checkbox-ranking':

				case 'checkbox':
					var $questionInputCheckbox 	= $questionDiv.find('input[type="checkbox"]'),
							$questionInputText 			= $questionDiv.find('input[type="text"]');

					function validate_checkbox_other(){
						if( ( $questionInputText.length && $questionInputText.val().length ) || $questionDiv.find('input[type="checkbox"]:checked').length ){
							$questionDiv.addClass('done');
						}
						else{
							$questionDiv.removeClass('done');
						}
					}

					// TRACK ON CHECKBOX CLICK - IF THE INLINE NUMBER OF CHECKED CHECKBOXES ARE MORE THAN ZERO
					$questionInputCheckbox.click( function( ev ){ validate_checkbox_other(); });
					$questionInputText.change( function( ev ){ validate_checkbox_other(); });

					break;

				case 'dropdown':
					var $questionInput = $questionDiv.find('select');

					$questionInput.change( function( ev ){
						$questionDiv.addClass('done');
						if( !$questionInput.val() || $questionInput.val().length == 0 ){
							$questionDiv.removeClass('done');
						}
					});
					break;

				case 'text':
					var $questionInput = $questionDiv.find('input[type="text"]');

					$questionInput.change( function( ev ){
						if( $questionInput.val().length ){
							$questionDiv.addClass('done');
						}
						else{
							$questionDiv.removeClass('done');
						}
					});

					break;

			}
		});

		/*
		* FORM CHANGE: CONDITIONAL DISPLAY
		*/
		$el.find('form').change( function( ev ){

			$el.find('form .space-question').each( function(){

				var $questionDiv 	= jQuery( this ),
					rules						= $questionDiv.data('rules'),
					flag 						= false;

				jQuery.each( rules, function( i, rule ){

					if( rule['question'] && rule['value'] ){

						var $parentQuestionDiv 	= jQuery( '#q' + rule['question'] ),
							parentType						= $parentQuestionDiv.data('type');

						switch( parentType ){

							case 'radio':

							case 'checkbox':
								// CHECK IF THE INPUT THAT HAS BEEN SELECTED HAS THE SAME VALUE IN THE RULE
								var $input = $parentQuestionDiv.find('input:checked');
								if( $input.length ){

									$input.each( function(){
										var selectedValue = jQuery(this).val().toString();
										// CHECK IF THE SELECTED VALUE IS IN THE RULE ARRAY
										if( jQuery.inArray( selectedValue, rule['value'] ) != -1 ){
											flag = true;
										}
									});

								}
								break;

							case 'dropdown':
								var $input = $parentQuestionDiv.find('select');
								if( jQuery.inArray( $input.val().toString(), rule['value'] ) != -1 ){
									flag = true;
								}
								break;
						} 				// END OF SWITCH CASE
					}					  // END OF IF CONDITION

				});						// END OF ITERATION OF RULES

				if( rules.length ){
					// IF THE FLAG IS TRUE THEN SHOW THE QUESTION OTHERWISE HIDE IT
					if( flag ){ $questionDiv.removeClass('hide'); }
					else{
						$questionDiv.addClass( 'hide' );

						// RESET THE QUESTIONS AFTER THEY ARE HIDDEN SO THAT THE VALUES ARE SAVED PROPERLY IN THE DB
						$questionDiv.find( 'input[type=checkbox]' ).prop( 'checked', false );
						$questionDiv.find( 'input[type=text]' ).val('');
						$questionDiv.find( 'select' ).prop('selectedIndex',0);
					}
				}


			});							// END OF ITERATION OF FORM FIELDS THAT ARE NOT REQUIRED
		});								// END OF EVENT HANDLING WHEN FORM CHANGES


		// TRIGGER INITIALIZATION
		init();


	});

};

jQuery( document ).ready( function(){

	jQuery('[data-behaviour~=space-slides]').space_slides();
	
});

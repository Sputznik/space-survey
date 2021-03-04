
jQuery.fn.space_slides = function(){

	return this.each(function() {

		var $el = jQuery( this );
		var timeoutID;

		/*
		*	GET UNIQUE GUEST ID BY USING COOKIES
		*/
		function guestData(){

			jQuery.ajax({
				type:'GET',
				url	: space_settings.ajax_url,
				dataType: 'json',
				data:{
					action		: 'space_survey_guest',
					survey_id 	: getSurveyID()
				},
				success: function( response ){

					// SET GUEST ID WITHIN THE FORM
					setGuestID( response.guest_id );

					//console.log( response.responses );

					// SET ANSWERS OF THE GUEST THAT WERE RECORDED PREVIOUSLY
					jQuery.each( response.responses, function( i, row ){
						setAnswerForQuestion( row );
					});

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

			console.log( response );


			switch( questionType ){

				case 'checkbox-other':
					if( response.choice_text ){
						var $questionInput = $questionDiv.find('input[type=text]');
						$questionInput.val( response.choice_text );
						$questionInput.change();
					}


				case 'radio':

				case 'checkbox-ranking':

				case 'checkbox':
					var $questionInput = $questionDiv.find('input[value=' + response.choice_id + ']');
					$questionInput. prop("checked", false);  // UNCHECK CHECKBOX IN CASE IT IS CHECKED BECAUSE OF CACHE
					$questionInput.click();
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

			console.log('save');

			var form = $el.find('form');

			//console.log( form.serialize() );

			jQuery.ajax({
				type	: 'POST',
				url		: space_settings.ajax_url + '?action=' + 'space_survey_save',
				dataType: 'json',
				data	: form.serialize(),
				success	: function( response ){
					console.log( response );
				},
				error: function(response) {
					console.log( response );
				}
			});

			//console.log( form.serialize() );

		}

		/*
		* FUNCTIONS REQUIRED FOR SLIDE TRANSITION
		*/
		function totalSlides(){
			return parseInt( $el.find('.space-slide').length );
		}

		function getCurrentSlide(){
			return $el.find('.space-slide.active');
		}

		function getNextSlide(){
			var $currentSlide 		= getCurrentSlide(),
				currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
				nextSlideNumber 	= currentSlideNumber + 1;

			if( nextSlideNumber >= totalSlides() ){ nextSlideNumber = 0; }

			return $el.find( '[data-slide~=' + nextSlideNumber + ']' );
		}

		function getPreviousSlide(){
			var $currentSlide 		= getCurrentSlide(),
				currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
				prevSlideNumber 	= currentSlideNumber - 1;

			if( prevSlideNumber < 0 ){ prevSlideNumber = totalSlides() - 1; }

			return $el.find( '[data-slide~=' + prevSlideNumber + ']' );
		}

		/*
		* SLIDE TRANSITION FROM CURRENT TO THE NEXT ONE
		* SAVE DATA BEFORE TRANSITION
		* TRIGGER EVENT AFTER THE TRANSITION
		*/
		function transitionSlide( $slide, $nextSlide ){
			saveGuestData();
			$slide.removeClass('active');
			$nextSlide.addClass('active');
			$nextSlide.trigger('space_survey:slideEnters');
			jQuery( 'html, body' ).animate( {scrollTop : 0}, 1000 );
		}

		function startTimer() {
			// wait 4 seconds before saving the guest data
    	timeoutID = window.setTimeout( saveGuestData, 4000 );
		}

		function resetTimer(e) {
    	window.clearTimeout( timeoutID );
 			startTimer();
		}

		function init(){

			guestData();

			/* ADD EVENT LISTENERS TO RESET TIMERS WHEN THE SURVEY FORM BECOMES ACTIVE */
			this.addEventListener( "mousemove", resetTimer, false );
			this.addEventListener( "mousedown", resetTimer, false );
			this.addEventListener( "keypress", resetTimer, false );
			this.addEventListener( "DOMMouseScroll", resetTimer, false );
			this.addEventListener( "mousewheel", resetTimer, false );
			this.addEventListener( "touchmove", resetTimer, false );
			this.addEventListener( "MSPointerMove", resetTimer, false );

    	startTimer();

			$el.find('.space-slide').each( function( i, slide ){

				var $slide = jQuery( slide );
				$slide.attr( 'data-slide', i );

			});
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


		/*
		* EVENT HANDLER FOR THE NEXT BUTTON
		*/
		$el.find('[data-behaviour~=space-slide-next]').click( function( ev ){

			ev.preventDefault();

			var $slide 		= getCurrentSlide(),
				$nextSlide	= getNextSlide(),
				totalQuest	= $slide.find('.space-question.required:not(.hide)').length,
				doneQuest	= $slide.find('.space-question.required.done:not(.hide)').length;

			// REMOVE ERROR CLASS FROM FIELDS THAT ARE DONE AND REQUIRED
			$slide.find('.space-question.required.done:not(.hide)').removeClass('error');

			if( totalQuest == doneQuest ){
				hideErrorMessage();
				transitionSlide( $slide, $nextSlide );
			}
			else{
				var pendingQuests = $slide.find( '.space-question.required:not(.done):not(.hide)' );
				if( pendingQuests.length ){
					pendingQuests.addClass( 'error' );
					var firstPendingQuest = pendingQuests.first();
					jQuery( 'html, body' ).animate( { scrollTop : firstPendingQuest.offset().top-200 }, 1000 );
				}

				showErrorMessage( 'Some required fields have not been filled' );

			}

		});

		/*
		* EVENT HANDLER FOR THE PREVIOUS BUTTON
		*/
		$el.find('[data-behaviour~=space-slide-prev]').click( function( ev ){

			ev.preventDefault();

			var $slide 		= getCurrentSlide(),
				$nextSlide	= getPreviousSlide();

			transitionSlide( $slide, $nextSlide );

		});

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

				case 'checkbox-other':
					var $questionInputText 	= $questionDiv.find('input[type="text"]'),
					 $questionInputCheckbox = $questionDiv.find('input[type="checkbox"]');

					function validate_checkbox_other(){
						if( $questionInputText.val().length || $questionDiv.find('input[type="checkbox"]:checked').length ){
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


				case 'checkbox-ranking':

				case 'checkbox':
					var $questionInput = $questionDiv.find('input[type="checkbox"]');

					// TRACK ON CHECKBOX CLICK - IF THE INLINE NUMBER OF CHECKED CHECKBOXES ARE MORE THAN ZERO
					$questionInput.click( function( ev ){

						var num_checked = $questionDiv.find('input[type="checkbox"]:checked').length;
						if( num_checked > 0 ){
							$questionDiv.addClass('done');
						}
						else{
							$questionDiv.removeClass('done');
						}

					});
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
					else{ $questionDiv.addClass('hide'); }
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

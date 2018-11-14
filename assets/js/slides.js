jQuery.fn.space_slides = function(){
	
	return this.each(function() {
		
		var $el 		= jQuery( this );
			
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
					
					setGuestID( response.guest_id );
					
					jQuery.each( response.responses, function( i, row ){
						setAnswerForQuestion( row );
					});
					
				},
				error: function(response) {
					console.log( 'Cookie could not be created' );
				}
			});
			
		}
		
		function setAnswerForQuestion( response ){
			
			var $questionDiv = $el.find( '#q' + response.question_id );
			
			var questionType = $questionDiv.data('type');
			
			switch( questionType ){
				
				case 'radio':
				
				case 'checkbox':
					var $questionInput = $questionDiv.find('input[value=' + response.choice_id + ']');
					$questionInput.click();
					break;
				
				case 'dropdown':
					var $questionInput = $questionDiv.find('select');
					$questionInput.val( response.choice_id );
					break;
				
				case 'text':
					var $questionInput = $questionDiv.find('input[type=text]');
					$questionInput.val( response.choice_text );
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
			
			var form = $el.find('form');
			
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
			
			console.log( form.serialize() );
			
		}
		
		
		function totalSlides(){
			return parseInt( $el.find('.space-slide').length );
		}
		
		function getCurrentSlide(){
			return $el.find('.space-slide.active');
		}
		
		
		
		function getNextSlide(){
			var $currentSlide 		= $el.find('.space-slide.active'),
				currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
				nextSlideNumber 	= currentSlideNumber + 1;
			
			if( nextSlideNumber >= totalSlides() ){ nextSlideNumber = 0; }
			
			return $el.find( '[data-slide~=' + nextSlideNumber + ']' );
		}
		
		function getPreviousSlide(){
			var $currentSlide 		= $el.find('.space-slide.active'),
				currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
				prevSlideNumber 	= currentSlideNumber - 1;
				
			if( prevSlideNumber < 0 ){ prevSlideNumber = totalSlides() - 1; }	
			
			return $el.find( '[data-slide~=' + prevSlideNumber + ']' );
		}
		
		function init(){
			
			guestData();
			
			$el.find('.space-slide').each( function( i, slide ){
				
				var $slide = jQuery( slide );
				$slide.attr( 'data-slide', i );
				
			});
		}
		
		$el.find('[data-behaviour~=space-slide-next]').click( function( ev ){
			
			ev.preventDefault();
			
			saveGuestData();
			
			var $slide 		= getCurrentSlide(),
				$nextSlide	= getNextSlide();
			
			$slide.removeClass('active');
			$nextSlide.addClass('active');
			
		});
		
		$el.find('[data-behaviour~=space-slide-prev]').click( function( ev ){
			
			ev.preventDefault();
			
			saveGuestData();
			
			var $slide 		= getCurrentSlide(),
				$prevSlide	= getPreviousSlide();
			
			$slide.removeClass('active');
			$prevSlide.addClass('active');
			
		});
		
		init();
		
		
	});

};

jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=space-slides]').space_slides();
	
	
	
	
} );
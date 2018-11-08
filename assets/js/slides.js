jQuery.fn.space_slides = function(){
	
	return this.each(function() {
		
		var $el = jQuery( this );
		
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
			return $el.find( '[data-slide~=' + prevSlideNumber + ']' );
		}
		
		function init(){
			$el.find('.space-slide').each( function( i, slide ){
				
				var $slide = $( slide );
				$slide.attr( 'data-slide', i );
				
			});
		}
		
		$el.find('[data-behaviour~=space-slide-next]').click( function( ev ){
			
			ev.preventDefault();
			
			var $slide 		= getCurrentSlide(),
				$nextSlide	= getNextSlide();
			
			$slide.removeClass('active');
			$nextSlide.addClass('active');
			
		});
		
		init();
		
		
	});

};

jQuery( document ).on( 'ready', function(){
	
	jQuery('[data-behaviour~=space-slides]').space_slides();
	
} );
var SPACE_SLIDES = function( options ){

	var self = {
		options: jQuery.extend( {
			$el 							: null,
			prePrevTransition	: function(){},
			preNextTransition	: function(){}
		}, options )
	};

	/*
	* FUNCTIONS REQUIRED FOR SLIDE TRANSITION
	*/
	self.totalSlides = function(){
		return parseInt( self.options.$el.find( '.space-slide' ).length );
	};

	self.getCurrentSlide = function(){
		return self.options.$el.find( '.space-slide.active' );
	};

	self.getNextSlide = function(){
		var $currentSlide 		= self.getCurrentSlide(),
			currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
			nextSlideNumber 	= currentSlideNumber + 1;

		if( nextSlideNumber >= self.totalSlides() ){ nextSlideNumber = 0; }
		return self.options.$el.find( '[data-slide~=' + nextSlideNumber + ']' );
	};

	self.getPreviousSlide = function(){
		var $currentSlide 		= self.getCurrentSlide(),
			currentSlideNumber 	= parseInt( $currentSlide.data('slide') ),
			prevSlideNumber 	= currentSlideNumber - 1;

		if( prevSlideNumber < 0 ){ prevSlideNumber = self.totalSlides() - 1; }
		return self.options.$el.find( '[data-slide~=' + prevSlideNumber + ']' );
	};

	/*
	* SLIDE TRANSITION FROM CURRENT TO THE NEXT ONE
	* SAVE DATA BEFORE TRANSITION
	* TRIGGER EVENT AFTER THE TRANSITION
	*/
	self.transitionSlide = function( $slide, $nextSlide ){
		$slide.removeClass('active');
		$nextSlide.addClass('active');
		$nextSlide.trigger('space_survey:slideEnters');
		jQuery( 'html, body' ).animate( {scrollTop : 0}, "fast" );
	};

		/*
	function transitionSlide( $slide, $nextSlide ){

	}*/


	/*
	* EVENT HANDLER FOR THE PREVIOUS BUTTON
	*/
	self.previousButtonClicked = function( ev ){
		ev.preventDefault();
		try{
			self.options.prePrevTransition();
			var $slide 		= self.getCurrentSlide(),
				$nextSlide	= self.getPreviousSlide();
			self.transitionSlide( $slide, $nextSlide );
		} catch( e ){
			console.log( e );
		}
	};

	/*
	* EVENT HANDLER FOR THE NEXT BUTTON
	*/
	self.nextButtonClicked = function( ev ){
		ev.preventDefault();
		try{
			self.options.preNextTransition();
			var $slide 		= self.getCurrentSlide(),
				$nextSlide	= self.getNextSlide();
			self.transitionSlide( $slide, $nextSlide );
		}catch(e){
			console.log(e);
		}
	};

	self.init = function(){

		self.options.$el.find('.space-slide').each( function( i, slide ){
			var $slide = jQuery( slide );
			$slide.attr( 'data-slide', i );
		} );

		self.options.$el.find('[data-behaviour~=space-slide-prev]').click( self.previousButtonClicked );
		self.options.$el.find('[data-behaviour~=space-slide-next]').click( self.nextButtonClicked );

	};

	self.init();

	return self;

};

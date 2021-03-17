/*
* EXAMPLE OF THE IMPLEMENTATION:
* SPACE_AUTOSAVE({
*		duration: 2000,
* 	save: saveCallbackFunction
* } );
*/

var SPACE_AUTOSAVE = function( options ){

	var self = {
		timeoutID : null,		// TIMEOUT ID
		options 	: jQuery.extend( {
			duration	: 4000,
			save			: function(){}
		}, options )
	};

	// wait for the duration before saving the guest data
	self.startTimer = function(){
		self.timeoutID = window.setTimeout( self.save, self.options.duration );
	}

	self.resetTimer = function( ev ){
		window.clearTimeout( self.timeoutID );
		self.startTimer();
	};

	self.save = function(){
		self.resetTimer();
		self.options.save();
	};

	self.init = function(){
		/*
		* ADD EVENT LISTENERS TO RESET TIMERS WHEN THE SURVEY FORM BECOMES ACTIVE
		*/
		window.addEventListener( "mousemove", self.resetTimer, false );
		window.addEventListener( "mousedown", self.resetTimer, false );
		window.addEventListener( "keypress", self.resetTimer, false );
		window.addEventListener( "DOMMouseScroll", self.resetTimer, false );
		window.addEventListener( "mousewheel", self.resetTimer, false );
		window.addEventListener( "touchmove", self.resetTimer, false );
		window.addEventListener( "MSPointerMove", self.resetTimer, false );

		// START TIMER
		self.startTimer();
	};

	self.init();

	return self;

};

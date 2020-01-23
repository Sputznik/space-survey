jQuery.fn.space_batch_process = function(){

	return this.each(function(){

		var el 			= jQuery(this),
			batches 	= el.data('batches'),
			batch_step 	= 1,
			params 		= el.data('params');

		/* HIDE ELEMENTS */
		el.find('.space-progress-container').hide();
		el.find('.logs-container').hide();

		/* ADD LOG */
		el.addLog = function( log ){
			var li = jQuery( document.createElement('li') );
			li.html( log );
			li.appendTo( el.find('.logs') );
		};

		/* CSS PROGRESS */
		el.updateProgress = function(){
			var width = ( ( batch_step-1 ) / batches ) * 100;

			if( width > 100 ){ width = 100;}
			if( width < 0 ){ width = 0; }

			if( width == 100 ){
				el.find('.result').html('Entire process has been completed');
			}

			el.find('.space-progress').animate({ width: width + '%' });
		};

		/* AJAX CALL */
		el.ajaxCall = function(){

			// PREPARE THE DATA THAT NEEDS TO BE PASSES THROUGH THE AJAX CALL
			var data = params;
			data['space_batch_action'] 	= el.data('action');
			data['space_batches']		= batches;
			data['space_batch_step']	= batch_step;

			// UPDATE THE PROGRESS IN THE BUTTON HTML
			el.find('button').html( el.data('btn') + " " + ( batch_step - 1 ) + "/" + batches );

			jQuery.ajax({
				'url'		: el.data('url'),
				'error'		: function(){ alert( 'Error has occurred' ); },
				'data'		: data,
				'success'	: function( html ){

					/* CHECK IF BATCH STEP INCREMENT IS ITERATED */
					if( batch_step <= batches ){

						batch_step++;			// INCREMENT BATCH STEP

						el.addLog( html );		// ADD TO THE LOG FROM THE AJAX HTML RESPONSE

						el.updateProgress();	// UPDATE PROGRESS BAR

						el.ajaxCall();			// EXECUTE THE NEXT BATCH CALL

					}
				}
			});
		};

		/* button click */
		el.find('button').click( function(){
			el.ajaxCall();
			el.find('button').attr('disabled', 'disabled');

			/* SHOW ELEMENTS */
			el.find('.space-progress-container').show();
			el.find('.logs-container').show();
		});

	});
}

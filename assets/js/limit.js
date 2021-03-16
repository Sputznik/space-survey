jQuery.fn.space_null_choices = function(){
	return this.each(function() {

		var $quest	 		= jQuery( this ).closest('.space-question'),
			$all_choices 	= $quest.find( 'input[type=checkbox]' ),
			$null_choice 	= $all_choices.first();

		$quest.find('input[type=checkbox]').click( function( ev ){
			var $current_choice = jQuery( this );
			if( $current_choice.is( ":checked" ) ){
				if( $current_choice.val() == $null_choice.val() ){
					$all_choices.prop( 'checked', false );
					$null_choice.prop( 'checked', true );
				}
				else{
					$null_choice.prop( 'checked', false );
				}
			}
		} );
	} );
};
jQuery.fn.space_limit_choices = function(){

	return this.each(function() {

    var $quest 			= jQuery( this ).closest('.space-question'),
			$all_choices 	= $quest.find( 'input[type=checkbox]' ),
      meta     			= $quest.data('meta') ? $quest.data('meta') : { limit: 0 },
      limit    			= meta['limit'],
			errorMsg 			= meta['limitError'];

    function getCheckedNum(){
			var total = $quest.find('input[type=checkbox]:checked').length;
			if( $quest.find('.space-choice-other input[type=text]').length &&
				$quest.find('.space-choice-other input[type=text]').val() ){
					total += 1;
			}
      return total;
    }

    if( limit > 0 ){

			// CHECK IF NULL CHOICES IS ENABLED, IF YES THEN REMOVE THE FIRST CHOICE FROM THE LIMIT
			if( $quest.find('[data-behaviour~=space-null-choices]') ){
				$all_choices 	= $quest.find( 'input[type=checkbox]:not(:first)' );
			}

			$all_choices.click( function( ev ){
        if( getCheckedNum() > limit ){
          jQuery( this ).prop( 'checked', false );
          alert( errorMsg );
        }
      } );
			$quest.find('.space-choice-other input[type=text]').focus( function( ev ){
				if( getCheckedNum() > (limit-1) ){
					alert( errorMsg );
					jQuery( this ).prop( 'value', '' );
				}
			} );
    }

  });
};


jQuery( document ).ready(function(){

	jQuery('[data-behaviour~=space-limit-choices]').space_limit_choices();
	jQuery('[data-behaviour~=space-null-choices]').space_null_choices();

  //jQuery('.space-question[data-type~=checkbox]').space_limit_choices();
  //jQuery('.space-question[data-type~=checkbox-other]').space_limit_choices();
	jQuery('.space-question[data-type~=checkbox-ranking]').space_limit_choices();

});

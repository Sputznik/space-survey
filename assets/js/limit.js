jQuery.fn.space_limit_choices = function(){

	return this.each(function() {

    var $quest = jQuery( this ),
      meta     = $quest.data('meta') ? $quest.data('meta') : { limit: 0 },
      limit    = meta['limit'],
			errorMsg = meta['limitError'];

    function getCheckedNum(){
			var total = $quest.find('input[type=checkbox]:checked').length;
			if( $quest.find('.space-choice-other input[type=text]').length &&
				$quest.find('.space-choice-other input[type=text]').val() ){
					total += 1;
			}
      return total;
    }

    if( limit > 0 ){
      $quest.find('input[type=checkbox]').click( function( ev ){
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

  jQuery('.space-question[data-type~=checkbox]').space_limit_choices();
  jQuery('.space-question[data-type~=checkbox-other]').space_limit_choices();
	jQuery('.space-question[data-type~=checkbox-ranking]').space_limit_choices();

});

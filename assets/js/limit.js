jQuery.fn.space_limit_choices = function(){

	return this.each(function() {

    var $quest = jQuery( this ),
      meta     = $quest.data('meta') ? $quest.data('meta') : { limit: 0 },
      limit    = meta['limit'];

    function getCheckedNum(){
      return $quest.find('input[type=checkbox]:checked').length;
    }

    if( limit > 0 ){
      $quest.find('input[type=checkbox]').click( function( ev ){
        if( getCheckedNum() > limit ){
          jQuery( this ).prop( 'checked', false );
          alert( 'Please unselect some choices as you have crossed the maximum number of selection.' );
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

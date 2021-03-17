jQuery.fn.space_rank_choices = function(){

	return this.each(function() {

		var $quest 	= jQuery( this ),
			lastRank	= 1,
			listSelectedChoices = [],
			rank			= 0;									// Rank counter

		// TRACK CLICK EVENT OF CHECKBOXES WITHIN THIS QUESTION
		$quest.find('input[type=checkbox]').click( function( ev ){ updateRanksForAll( ev ); });

		// GET THE LABEL THAT SHOWS THE RANK ELEMENT OF A PARTICULAR CHECKBOX
		//function getRankElement( $choiceEl ){ return $choiceEl.closest( 'li.space-choice.rank-field' ).find( 'label.rank' ); }

		var inc = 0;
		$quest.find('input[type=checkbox]').each( function(){
			var $checkbox = jQuery( this );
			$checkbox.attr( 'data-i', inc );
			$checkbox.attr( 'data-default', $checkbox.val() );
			inc++;
		} );

		function updateRanksForAll( ev ){
			var $selectedChoice 		= jQuery( ev.target ),	// SELECTED CHOICE ELEMENT
				indexOf								= listSelectedChoices.indexOf( $selectedChoice.attr('data-i') );

			if( $selectedChoice.prop('checked') && indexOf == -1 ){
				listSelectedChoices.push( $selectedChoice.attr('data-i') );
			}
			else{
				if( indexOf > -1 ){
					listSelectedChoices.splice( indexOf, 1 );
				}
			}
			sortRanks();
		}

		function sortRanks(){
			var lastRank = 1;

			// RESET ALL THE RANKS
			$quest.find('label.rank span').html( '#' );

			for( var i=0; i<listSelectedChoices.length; i++ ){
				var selected_i = listSelectedChoices[i];
				var $selectedChoice = $quest.find('input[type=checkbox][data-i=' + selected_i + ']');
				updateRankForOne( $selectedChoice, lastRank );
				lastRank++;
			}
		}

		function updateRankForOne( $choice, rank ){
			$choice.val( $choice.attr( 'data-default' ) + ";" + rank );
			$choice.closest( 'li.rank-field' ).find('label.rank span').html( rank );
		}


  });
};

jQuery( document ).ready(function(){
	jQuery('.space-question[data-type~=checkbox-ranking]').space_rank_choices();
});

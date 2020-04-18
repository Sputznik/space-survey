jQuery.fn.space_rank_choices = function(){

	return this.each(function() {

		var $quest 	= jQuery( this ),
			rank			= 0;									// Rank counter

		// TRACK CLICK EVENT OF CHECKBOXES WITHIN THIS QUESTION
		$quest.find('input[type=checkbox]').click( function( ev ){ getSelectedChoice(ev); });

		// GET THE LABEL THAT SHOWS THE RANK ELEMENT OF A PARTICULAR CHECKBOX
		function getRankElement( $choiceEl ){ return $choiceEl.closest( 'li.space-choice.rank-field' ).find( 'label.rank' ); }

    // Gets the selected choice
    function getSelectedChoice( choice ){

			var meta 									= $quest.data('meta'),													// META INFORMATION
				limit 									= meta['limit'],																// LIMIT
				selectedChoice 					= choice.target,																// SELECTED CHOICE ELEMENT
				isSelectedChoiceChecked	= selectedChoice.checked,												// CHECKED ATTRIBUTE
				selectedChoiceValue 		= jQuery( selectedChoice ).attr('data-id'),			// VALUE ATTRIBUTE
				selectedChoiceRank 			= getRankElement( jQuery( selectedChoice ) ),		// GET RANK ELEMENT
				selectedChoiceDataRank 	= selectedChoiceRank.attr('data-rank'); 				// Set the data-rank to 0 for all elements

			if( isSelectedChoiceChecked ){

				/* If checkbox is checked
				* change the background of the rank field
				* and add rank to the field.
				*/

				rank++;					// Increment the rank counter

				selectedChoiceRank.css({
					'background-color': '#000',
					'color':  '#fff'
				});

				// Set the data rank for the rank field
				selectedChoiceRank.attr( 'data-rank', rank );

				jQuery( selectedChoice ).val( jQuery( selectedChoice ).attr('data-id') + ";" + rank );

				//Update the rank field UI
				selectedChoiceRank.find('span').text(rank);
			}
			else if( !( isSelectedChoiceChecked ) && ( getCheckedNum() == limit ) &&  ( limit != '' ) ){
				/*
				* If the checked limit exceeds the limit, set the rank = limit
				*/
				rank = limit;
			}
			else{
				/*
				* If the checkbox is unchecked, set the unckecked checkbox rank to zero
				*/

				// console.log( 'inside else selectedChoiceDataRank: '+ selectedChoiceDataRank );

				// Dynamically sort the rank fields of checked checkboxes in ascending order
				changeRank( selectedChoiceDataRank );

				// Decrement the global rank counter when a checkbox is unchecked
				rank--;

				selectedChoiceRank.css({
					'background-color': '#fff',
					'color':  '#000'
				});

				// Sets the unchecked checkboxes data rank to 0
				selectedChoiceRank.attr('data-rank', 0);

				// Sets the unchecked checkboxes rank field value to default value
				selectedChoiceRank.find('span').text('#');
			}
			//console.log('Rank:'+rank+' '+ 'Meta Limit: ' + limit);
		}

		// Get the total number of checked checkboxes on every click
		function getCheckedNum(){ return $quest.find('input[type=checkbox]:checked').length; }

		// Changes the rank dynamically
		function changeRank( uncheckedIndex ){

			/*
			* Stores the key, value pair of all the checked checkboxes
			*/
			var checkedList 	= [],
				checkedElements	= $quest.find('input[type=checkbox]:checked');

			/*
			* Loops through all the checked checkboxes
			* extracts their id and data-rank
			* and adds to checkedList = []
			*/
			checkedElements.each(function(index){
				var checkedElement = getRankElement( jQuery( checkedElements[index] ) );
				checkedList.push({'id':'#'+checkedElement.attr('id'), 'dataRank':checkedElement.attr('data-rank')});
			});

			// Unchecked Element's Index
			var uncheckedElementIndex = uncheckedIndex;

			/*
			* Finds the index of the unchecked element and
			* starts sorting the elements from the next highest index
			*/
			jQuery.each( checkedList, function(key,value){
				if( checkedList[key].dataRank > uncheckedElementIndex  ){
					jQuery( checkedList[key].id ).attr('data-rank', checkedList[key].dataRank - 1  );
					jQuery( checkedList[key].id ).find('span').text(checkedList[key].dataRank - 1);
				}
				// console.log(checkedList[key].id);
				// console.log(checkedList[key].dataRank);
			});
		}

  });
};

jQuery( document ).ready(function(){
	jQuery('.space-question[data-type~=checkbox-ranking]').space_rank_choices();
});

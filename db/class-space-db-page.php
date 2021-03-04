<?php
/*
* PAGE MODEL ( REFERENCED IN SURVEY MODEL )
*/

class SPACE_DB_PAGE extends SPACE_DB_BASE{

	var $relation_db;

	function __construct(){
		$this->setTableSlug( 'page' );
		parent::__construct();

		require_once('class-space-db-page-question-relation.php');
		$this->setRelationDB( SPACE_DB_PAGE_QUESTION_RELATION::getInstance() );

	}

	function setRelationDB( $relation_db ){ $this->relation_db = $relation_db; }
	function getRelationDB(){ return $this->relation_db; }

	function create(){

		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			description TEXT,
			menu_rank INT DEFAULT 0,
			survey_id BIGINT(20) NOT NULL,
			PRIMARY KEY(ID)
		) $charset_collate;";

		$this->query( $sql );

	}

	function sanitize( $data ){

		$pageData = array(
			'title' 		=> sanitize_text_field( $data['title'] ),
			'description' 	=> isset( $data['description'] ) ? stripslashes( $data['description'] ):'', // Storing HTML
			'rank' 			=> isset( $data['rank'] ) ? absint( $data['rank'] ) : 0,
			'survey_id'		=> absint( $data['survey_id'] ),
		);
		return $pageData;
	}

	function listForSurvey( $survey_id ){


		$pages = $this->filter(
			array(
				'survey_id'	=> '%d'
			),
			array( (int) $survey_id ),
			'menu_rank'
		);

		foreach( $pages as $page ){

			$page->questions = $this->getRelationDB()->listForPage( $page->ID );

		}

		return $pages;
	}

	/*
	function listQuestions( $page_id ){



		return $this->getRelationDB()->filter(
			array(
				'page_id' => '%d'
			),
			array( (int)$page_id ),
			'rank',
			'ASC'
		);

	}
	*/

	function updateForSurvey( $page ){

		// PREPARE THE PAGE DATA FOR UPDATION OR INSERTION
		$data = $this->sanitize( $page );

		// CHECK IF THE DATA NEEDS TO BE UPDATED OR INSERTED
		if( $page['id'] ){
			$this->update( $page['id'], $data, array( '%s', '%s', '%d', '%d' ) );
		}
		else{
			$page['id'] = $this->insert( $data );
		}

		/*
		*	UPDATE THE RELATIONS TABLE FOR CORRESPONDING QUESTIONS ONLY IF THE page_id IS VALID
		*/
		if( $page['id'] ){

			// DELETE ALL ITEMS
			$this->getRelationDB()->deleteForPage( $page['id'] );

			// INSERT ITEMS
			if( isset( $page['questions'] ) && is_array( $page['questions'] ) ){
				foreach( $page['questions'] as $question ){

					if( isset( $question['id'] ) && $question['id'] && isset( $question['rank'] ) ){

						$relationData = array(
							'page_id'			=> $page['id'],
							'question_id'	=> $question['id'],
							'rank'				=> $question['rank']
						);

						$relationData = $this->getRelationDB()->sanitize( $relationData );

						$this->getRelationDB()->insert( $relationData );
					}
				}
			}
		}
	}

	function alter_table(){
		$table = $this->getTable();
		$sql = "ALTER TABLE $table CHANGE `rank` `menu_rank` INT DEFAULT 0;";
		echo "Renamed rank columm in $table <br>";
		return $this->query( $sql );
	}

}

SPACE_DB_PAGE::getInstance();

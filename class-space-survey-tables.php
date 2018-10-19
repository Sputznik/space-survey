<?php

class YKA_SURVEY_TABLES {
	private $question_tbl;

	function __construct(){

		$prefix = $this->get_table_prefix();

		$this->question_tbl = $prefix . 'question';

		$this->setup_tables();
		
	}


	function setup_tables(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$question_tbl = $this->question_tbl;		
		// qery TO setup question TABLE 
		$sql = "CREATE TABLE IF NOT EXISTS $question_tbl ( 
					ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				    title VARCHAR(255),
				    description VARCHAR(255),
				    rank INT,
				    TYPE VARCHAR(20),
				    page_id BIGINT(20),
				    parent BIGINT(20),
				    PRIMARY KEY(ID)
				) $charset_collate;";
		
		$result = $wpdb->query($sql);


	}


	function get_table_prefix(){
		global $wpdb;

		return $wpdb->prefix.'yka_survey_'; 
	}


}

$test_obj = new YKA_SURVEY_TABLES();
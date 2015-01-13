<?php

	/**
	 * checks to see if any files from ../emoticons_list.php
	 * are missing in ../img/
	 * lists all missing files
	 */
	 
	set_time_limit(0);
	require_once('../emoticons_list.php');
	
	$result = array();
	
	foreach($emoticon_list as $v) {
		if (!file_exists('../img/' . str_replace(':', '-', $v) . '.png')) {
			array_push($result, $v);
		}
	}
	
	print_r($result);
	
?>
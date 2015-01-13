<?php

	/**
	 * downloads emoticon imgs listed in ../emoticons_list.php
	 * from steamcommunity.com to ../img/
	 */
	
	require_once('../emoticons_list.php');

	set_time_limit(0);
	$req_wait_interval = 0;
	
	$files = 0;
	$err_list = array();
	$url = 'http://cdn.steamcommunity.com//economy/emoticon/';
	$img = null;
	
	foreach($emoticon_list as $v) {
		$retries = 3;
		do {
			$img = @file_get_contents($url . $v);
			if ($img) {
				if ($img[1] == 'P' && $img[2] == 'N' && $img[3] == 'G') {
					file_put_contents('../img/' . str_replace(':', '-', $v) . '.png', $img);
					$files++;
					$img = null;
					break;
				}
			}
			$retries--;
			if (!in_array($v, $err_list)) array_push($err_list, $v);
			if ($req_wait_interval) sleep($req_wait_interval);
		} while($retries > 0);
		if ($req_wait_interval) sleep($req_wait_interval);
		//break;
	}
	
	print('fin [downloaded: ' . $files . '] [errors: ' . count($err_list) . ']');
	if (count($err_list) > 0) print_r($err_list);
	
?>
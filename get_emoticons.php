<?php

	/**
	 * return list of emoticons available to the user
	 * as per their steam account, OR as per the string
	 * of emoticons given as input
	 *
	 * returns json list
	 * only returns valid emoticons as listed in emoticons_list.php
	 * attempts to download missing emoticons when detected via
	 * access to a steam users inventory
	 */
	 
	require('emoticons_list.php');

	define("ERR_NONE", 0); /* success */
	define("ERR_NO_EMOTE", 1); /* no/not enough emoticons */
	define("ERR_PRIVATE", 2); /* inventory is private */
	
	$url = 'http://cdn.steamcommunity.com//economy/emoticon/';
	$result = array();
	$result['error'] = ERR_NONE;
	$result['emoticons'] = array();
	
	if (!isset($_GET['steamid'])) {
		$result['error'] = ERR_NO_EMOTE;
		print(json_encode($result));
		exit(0);
	}
	
	/* look for emoticons in input string */
	$preg_out = array();
	@preg_match_all('/:[a-zA-Z0-9_-]+:/', $_GET['steamid'], $preg_out);
	if (count($preg_out) > 0) {
		if (count($preg_out[0]) > 1) {
			/* return list of these emoticons if they exist in the local list */
			$valid_emotes = array();
			foreach($preg_out[0] as $v) {
				if (in_array($v, $emoticon_list)) array_push($valid_emotes, $v);
			}
			if (count($valid_emotes) > 1) $result['emoticons'] = $valid_emotes;
			else $result['error'] = ERR_NO_EMOTE;
			print(json_encode($result));
			
			exit(0); /* make sure not to lookup steam inventory */
		}
		else if (count($preg_out[0]) > 0) {
			/* single emoticon - cant work */
			$result['error'] = ERR_NO_EMOTE;
			print(json_encode($result));
			
			exit(0); /* make sure not to lookup steam inventory */
		}	
	}
	
	/* else look for emoticons in steam inventory of input as steam vanity url */
	$preg_out = array();
	$vanity_steamid = preg_replace("/(steamcommunity.com\/id|www\.|http:\/\/|\/)/", "", $_GET['steamid']);
	$page = @file_get_contents('http://steamcommunity.com/id/' . htmlspecialchars($_GET['steamid']) . '/inventory/json/753/6/');
	@preg_match_all('/cdn\\.steamcommunity\\.com\\\\\\/\\\\\\/economy\\\\\\/emoticon\\\\\\/([a-zA-Z0-9\\:\\-\\_]+)/', $page, $preg_out);

	if (count($preg_out) > 1) {
		if (count($preg_out[1]) > 1) {
			/* users steam inventory contains enough emoticons */
			$replaced_missing = false;
			foreach($preg_out[1] as $v) {
				if (!in_array($v, $emoticon_list)) {
					/* try to download missing emoticons and  update the local emoticon list */
					$retries = 3;
					do {
						$img = @file_get_contents($url . $v);
						if ($img) {
							if ($img[1] == 'P' && $img[2] == 'N' && $img[3] == 'G') {
								@file_put_contents('img/' . str_replace(':', '-', $v) . '.png', $img);
								$img = null;
								array_push($emoticon_list, $v);
								$replaced_missing = true;
								array_push($result['emoticons'], $v);
								break;
							}
						}
						$retries--;
					} while($retries > 0);
				}
				else array_push($result['emoticons'], $v);
			}
			if ($replaced_missing) {
				$s = "<?php\n\$emoticon_list = array(\n";
				$i = 0;
				foreach($emoticon_list as $v) {
					$s .= "\t'$v',\n";
					$i++;
				}
				$s .= ");\n?>";
				@file_put_contents('emoticons_list.php', $s);
			}
		}
		else {
			$private = @preg_match('/Error\\"\\:\\"This profile is private.\\"/', $page);
			if ($private) $result['error'] = ERR_PRIVATE;
			else $result['error'] = ERR_NO_EMOTE;
		}
	}
	else {
		$private = @preg_match('/Error\\"\\:\\"This profile is private.\\"/', $page);
		if ($private) $result['error'] = ERR_PRIVATE;
		else $result['error'] = ERR_NO_EMOTE;
	}
	
	print(json_encode($result));

?>

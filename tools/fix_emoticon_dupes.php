<?php

	/**
	 * fixes the duplicate entries in ../emoticons_list.php
	 * note: fixed in seperate file to avoid having to crawl
	 * steamcommunity.com again
	 *
	 * also fixes bad data manually.. there is
	 * only a few occurances of bad data i hope no more!
	 */
	 
	set_time_limit(0);
	require_once('../emoticons_list.php');
	
	$url = 'http://cdn.steamcommunity.com//economy/emoticon/';
	
	$bad_data = array(
		':bpescape:' => ':bpflash:',
		':bpregeneration:' => ':bpvitality:',
		':bpfist:' => ':bpcharge:',
		':bphero:' => ':bpswim:',
		':bphealt:' => ':bpheal:',
		':Givemeyourmoney:' => ':Neko:',
		':gangerousplanet:' => ':dangerousplanet:',
		':iaito:' => ':iaito:',
		':ranger:' => ':ranger:',
	);
	
	$result = array();
	$pure_list = array();
	
	foreach($emoticon_list as $v) {
		if (!in_array($v, $pure_list)) array_push($pure_list, $v);
		else array_push($result, $v);
	}
	
	foreach($bad_data as $k => $v) {
		if (in_array($k, $pure_list) && $k != $v) {
			if (($k2 = array_search($k, $pure_list)) !== false) {
				unset($pure_list[$k2]);
				print('[remove bad ' . $k . ' for ' . $v . ']');
			}
			if (!in_array($v, $pure_list)) {
				print('[add ' . $v . ']');
				array_push($pure_list, $v);
				$retries = 3;
				do {
					$img = @file_get_contents($url . $v);
					if ($img) {
						if ($img[1] == 'P' && $img[2] == 'N' && $img[3] == 'G') {
							print('[download ' . $v . ']');
							file_put_contents('../img/' . str_replace(':', '-', $v) . '.png', $img);
							$img = null;
							break;
						}
					}
					$retries--;
				} while($retries > 0);
			}
			else print('[exists ' . $v . ']');
		}
		else {
			$retries = 3;
			do {
				$img = @file_get_contents($url . $v);
				if ($img) {
					if ($img[1] == 'P' && $img[2] == 'N' && $img[3] == 'G') {
						print('[download ' . $v . ']');
						file_put_contents('../img/' . str_replace(':', '-', $v) . '.png', $img);
						$img = null;
						break;
					}
				}
				$retries--;
			} while($retries > 0);
		}
	}
	
	print('[array of ' . count($emoticon_list) . ' is now ' . count($pure_list) . '] duplicates ==> ');
	print_r($result);
	
	$s = "<?php\n\$emoticon_list = array(\n";
	$i = 0;
	foreach($pure_list as $v) {
		$s .= "\t'$v',\n";
		$i++;
	}
	$s .= ");\n?>";
	file_put_contents('../emoticons_list.php', $s);
	print('fin; see emoticons.txt!');
	
	
?>
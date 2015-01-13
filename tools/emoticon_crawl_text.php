<?php

	/**
	 * crawls the steamcommunity market for emoticons
	 * compiles a list of emoticons and generates a php string
	 * array containing them (../emoticons_list.php)
	 */

	set_time_limit(0);
	$req_wait_interval = 5;

	$result = array();
	
	$url = 'http://steamcommunity.com/market/search/render/?query=emoticon';
	
	// recalculated when known
	$total = 7607;
	$start = 0;
	$pagesize = 100;
	
	print('[');
	for (;$start <= $total; $start += $pagesize) {
		print($start . ':');
		$page = @file_get_contents($url . '&start=' . $start . '&count=' . $pagesize);
		if ($start == 0) {
			$preg_out = array();
			@preg_match_all("/pagesize\\\"\\:[0-9]+\\,\\\"total_count\\\"\\:([0-9]+)\\,/", $page, $preg_out);
			if (count($preg_out[1]) > 0) $total = intval($preg_out[1][0]);
		}
		$preg_out = array();
		@preg_match_all("/market\\\\\\/listings\\\\\\/[0-9]+\\\\\\/[^\%]+\\%3A([^\\%]+)\\%3A/", $page, $preg_out);
		if (count($preg_out[1]) < 1 && $start < $total) {
			print('err/0 -> ');
			// try again
			$start -= 100;
			if ($req_wait_interval) sleep($req_wait_interval);
			continue;
		}
		foreach($preg_out[1] as $v) {
			array_push($result, $v);
		}
		print(strval(count($preg_out[1])) . ' -> ');
		if ($req_wait_interval) sleep($req_wait_interval);
	}
	
	$s = "<?php\n\$emoticon_list = array(\n";
	$i = 0;
	foreach($result as $v) {
		$s .= "\t':$v:',\n";
		$i++;
	}
	$s .= ");\n?>";
	file_put_contents('../emoticons_list.php', $s);
	print('fin; see emoticons.txt! [last: ' . $start . ']');

?>
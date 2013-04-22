<?php
	/**
	 * Pinterest Scraping Service
	 * GET Params: user, boards, limit, format
	 *
	 * This if just a user name is provided, the service will
	 * return all pins and data from that user. The return list
	 * can also be filtered by a comma separated list of boards
	 * and a limit of pins that can be returned. Format can be
	 * xml or json.
	 **/

	// set up the simple dom object
	require_once 'pinterest.php';

	// get the parameters that have been passed in
	$params = array();
	$params['user'] = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : NULL;
	$params['boards'] = isset($_GET['boards']) ? explode(',',htmlspecialchars($_GET['boards'])) : NULL;
	$params['limit'] = isset($_GET['boards']) ? htmlspecialchars($_GET['limit']) : 0;

	// format can be one of xml, json or jsonp. Defaults to jsonp
	if (strtolower($_GET['format']) == "xml") {
		$format = "xml";
	} elseif (strtolower($_GET['format']) == "jsonp") {
		$format = "jsonp";
	} else {
		$format = "json";
	}

	$pinterest = new Pinterest($params);
	$result = $pinterest->get();

	// output depending on the format that was defined
	switch($format) {
		case "jsonp":
			header('Content-Type: text/javascript; charset=utf8');
			echo json_pretty($_GET['callback'].'('.json_encode($result).');');
			break;
		case "xml":
			header('Content-Type: text/xml; charset=utf8');
			$xml = new SimpleXMLElement('<root/>');
			array_walk_recursive($result, array ($xml, 'addChild'));
			print $xml->asXML();
			break;
		case "json":
		default:
			header('Content-Type: application/json; charset=utf8');
			echo json_pretty(json_encode($result));
			break;
	}

	/*
	 * Utility Functions for Prettier Output
	 */
	function json_pretty($json, $html = false) {
		$out = ''; $nl = "\n"; $cnt = 0; $tab = 4; $len = strlen($json); $space = ' ';
		if($html) {
			$space = '&nbsp;';
			$nl = '<br/>';
		}
		$k = strlen($space)?strlen($space):1;
		for ($i=0; $i<=$len; $i++) {
			$char = substr($json, $i, 1);
			if($char == '}' || $char == ']') {
				$cnt --;
				$out .= $nl . str_pad('', ($tab * $cnt * $k), $space);
			} else if($char == '{' || $char == '[') {
				$cnt ++;
			}
			$out .= $char;
			if($char == ',' || $char == '{' || $char == '[') {
				$out .= $nl . str_pad('', ($tab * $cnt * $k), $space);
			}
			if($char == ':') {
				$out .= ' ';
			}
		}
		return $out;
	}
?>
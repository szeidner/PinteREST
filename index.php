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

	require_once 'lib/pinterest.php';
	require_once 'lib/utility.php';

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

			// convert object to array
			$array = objectToArray($result);
			//echo "<pre>".print_r($array,true)."</pre>";

			// create XML element
			$xml = new SimpleXMLElement("<root/>");
			array_to_xml($array,$xml);

			// output XML
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			echo $dom->saveXML();

			break;
		case "json":
		default:
			header('Content-Type: application/json; charset=utf8');
			//echo "<pre>".print_r($result)."</pre>";
			echo json_format(json_encode($result));
			break;
	}
?>
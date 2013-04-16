<?php
	// set up the simple dom object
	require_once 'lib/simple_html_dom.php';
	$html = new simple_html_dom();
	$data = array();


	if(isset($_GET['user']) && intval($_GET['user'])) {
		$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml';
		$user_id = intval($_GET['user']);

			foreach ($boards as $board) {
				$pagenum = 1;
				$pins = array();
				do {

					$html = file_get_html("http://pinterest.com/$user/$board/?lazy=1&page=$pagenum");

					// break if last page
					if(!$html->find('div[class=pin]')) {
						break;
					}

					foreach($html->find('div[class=pin]') as $pin) {
						$item['pin_id'] = $pin->{'data-id'};
						$item['pin_image'] = $pin->{'data-closeup-url'};
						$item['pin_description'] = $pin->find('.description', 0)->plaintext;
						if ($pin->find('.attribution .NoImage a',0)) {
							$item['pin_link'] = $pin->find('.attribution .NoImage a',0)->href;
						} else {
							$item['pin_link'] = "";
						}
						if ($pin->find('.stats .RepinsCount',0)) {
							$item['pin_repins'] = preg_replace("/[^0-9,.]/", "", trim($pin->find('.stats .RepinsCount',0)->plaintext));
						} else {
							$item['pin_repins'] = 0;
						}

						$pins[] = $item;
					}

					$pagenum++;

				} while (true);

				$board_contents['board_name'] = $board;
				$board_contents['board_pins'] = $pins;
				$data[] = $board_contents;
			}

			$html->clear();
			unset($html);

			echo "<pre>".print_r($data, true)."</pre>";

	}


	else {
		echo "'user' is a required parameter.";
	}

?>
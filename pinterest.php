<?php
	/**
	 * Pinterest Class
     *
	 * This class depends on Simple HMTL DOM
	 **/

	require_once 'lib/simple_html_dom.php';

	class Pinterest {
		/**
		 * Constructor Function
		 * @params = Array of parameters
		 * 	 $user = Required username (String)
		 *   @boards = Array of boards to return pins and data from.
		 *   $limit = Limit of # of pins to return. 0 will return all of the pins.
		 **/
		public function __construct($params) {
			if (is_array($params)) {
				$this->user = isset($params['user']) ? $params['user'] : NULL;
				$this->boards = isset($params['boards']) ? $params['boards'] : NULL;
				$this->limit = isset($params['limit']) ? $params['limit'] : NULL;
			}

			// initialize response object
			$this->response = (object) array(
				'status'=>'',
				'message'=>'',
				'data'=> array()
			);

			// set up the simple dom object
			$this->html = new simple_html_dom();
		}

		public function get() {
			if (is_null($this->user)) {
				$this->response->status = "ERROR";
				$this->response->message = "'user' is a required parameter";
			}
			if (is_null($this->boards)) {
				$this->allPins();
			}
			return $this->response;
		}

		private function allPins() {

		}

	}





	// if no user parameter has been passed
	// if (!$user) {
	// 	$response->status = "ERROR";
	// 	$response->message = "'user' is a required parameter";
	// }

	// else {
	// 		foreach ($boards as $board) {
	// 			$pagenum = 1;
	// 			$pins = array();
	// 			do {

	// 				$html = file_get_html("http://pinterest.com/$user/$board/?lazy=1&page=$pagenum");

	// 				// break if last page
	// 				if(!$html->find('div[class=pin]')) {
	// 					break;
	// 				}

	// 				foreach($html->find('div[class=pin]') as $pin) {
	// 					$item['pin_id'] = $pin->{'data-id'};
	// 					$item['pin_image'] = $pin->{'data-closeup-url'};
	// 					$item['pin_description'] = $pin->find('.description', 0)->plaintext;
	// 					if ($pin->find('.attribution .NoImage a',0)) {
	// 						$item['pin_link'] = $pin->find('.attribution .NoImage a',0)->href;
	// 					} else {
	// 						$item['pin_link'] = "";
	// 					}
	// 					if ($pin->find('.stats .RepinsCount',0)) {
	// 						$item['pin_repins'] = preg_replace("/[^0-9,.]/", "", trim($pin->find('.stats .RepinsCount',0)->plaintext));
	// 					} else {
	// 						$item['pin_repins'] = 0;
	// 					}

	// 					$pins[] = $item;
	// 				}

	// 				$pagenum++;

	// 			} while (true);

	// 			$board_contents['board_name'] = $board;
	// 			$board_contents['board_pins'] = $pins;
	// 			$data[] = $board_contents;
	// 		}

	// 		$html->clear();
	// 		unset($html);

	// 		echo "<pre>".print_r($data, true)."</pre>";

	// }



	// function output($format) {

	// }


?>
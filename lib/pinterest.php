<?php
	/**
	 * Pinterest Class
	 *
	 * This class depends on Simple HMTL DOM
	 **/

	require_once 'simple_html_dom.php';

	class Pinterest {
		// initialize error codes
		public static $errorCodes = array(
			10001 => "'user' is a required parameter",
			10002 => "'boards' was not formatted properly",
			404 => "User or board URL does not exist.",
		);

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
			$this->response = (object) array();

			// set up the simple dom object
			$this->html = new simple_html_dom();

		}

		/**
		 * Destructor
		 * The html and response objects can grow large, so let's unset them.
		 **/
		public function __destruct() {
			unset($this->html, $this->user, $this->boards, $this->limit, $this->response);
		}

		/**
		 * Get
		 * Determines what type of call (all user pins or boards) returns the response array
		 **/
		public function get() {
			// Case where user was not defined
			if (is_null($this->user)) {
				$this->error(10001);
			}
			// Case where boards was not defined, so let's get all of the user's pins
			elseif (is_null($this->boards)) {
				$this->getAllPins();
			}
			// Case where boards was defined
			elseif (is_array($this->boards)) {
				$this->getBoards();
			}
			// There was an error with the format of boards
			else {
				$this->error(10002);
			}

			return $this->response;
		}

		/**
		 * getAllPins
		 * Get all pin data for the user
		 **/
		private function getAllPins() {
			$pincount = 0;

			// loop through all of the pins
			$pagenum = 1;
			$pins = array();
			do {
				// check to make sure the url is valid
				$url = "http://pinterest.com/$this->user/pins/?lazy=1&page=$pagenum";
				if (!$this->urlExists($url)) {
					$this->error(404);
					break;
				}

				$html = file_get_html($url);

				// break if last page
				if(!$html->find('div[class=pin]')) {
					break;
				}

				// parse the page
				foreach($html->find('div[class=pin]') as $pin) {
					// parse the pin data
					$pins[] = $this->parsePin($pin);

					// check to see if we have reached our limit
					if ($this->limit > 0) {
						$pincount++;
						if ($pincount >= $this->limit) {
							break;
						}
					}
				}

				$pagenum++;

			} while ( ($pincount < $this->limit) || ($this->limit == 0) );

			$this->response->pins = $pins;
		}

		/**
		 * getBoards
		 * Get pin data for specified boards and update response object
		 **/
		private function getBoards() {
			$pincount = 0;

			// loop through each of the boards
			foreach ($this->boards as $board) {
				$pagenum = 1;
				$pins = array();
				do {
					// check to make sure the url is valid
					$url = "http://pinterest.com/$this->user/$board/?lazy=1&page=$pagenum";
					if (!$this->urlExists($url)) {
						$this->error(404);
						break;
					}

					// get contents of the url
					$html = file_get_html($url);

					// break if last page
					if(!$html->find('div[class=pin]')) {
						break;
					}

					// parse the page
					foreach($html->find('div[class=pin]') as $pin) {
						// parse the pin data
						$pins[] = $this->parsePin($pin);

						// check to see if we have reached our limit
						if ($this->limit > 0) {
							$pincount++;
							if ($pincount >= $this->limit) {
								break;
							}
						}
					}

					$pagenum++;

				} while ( ($pincount < $this->limit) || ($this->limit == 0) );

				$board_contents = (object) array();
				$board_contents->name = $board;
				$board_contents->pins = $pins;
				$this->response->boards[] = $board_contents;
			}
		}

		/**
		 * parsePin
		 * @pin = Simple HTML DOM pin object
		 * Parse all the Pin data from the DOM and return an array of PINs
		 **/
		private function parsePin($pin) {
			// get the pin ID
			$item['id'] = $pin->{'data-id'};

			// thumbnail image
			$item['thumb'] = $pin->{'data-closeup-url'};

			// description
			$item['description'] = $pin->find('.description', 0)->plaintext;

			// link to the original page this was pinned from
			if ($pin->find('.attribution .NoImage a',0)) {
				$item['link'] = $pin->find('.attribution .NoImage a[rel=nofollow]',0)->href;
			} else {
				$item['link'] = "";
			}

			// repin count
			$item['repins'] = intval(preg_replace("/[^0-9,.]/", "", trim($pin->find('.stats .RepinsCount',0)->plaintext)));

			// likes count
			$item['likes'] = intval(preg_replace("/[^0-9,.]/", '', trim($pin->find('.stats .LikesCount',0)->plaintext)));

			// comments count
			$item['comments'] = intval(preg_replace("/[^0-9,.]/", '', trim($pin->find('.stats .CommentsCount',0)->plaintext)));

			return $item;
		}

		/**
		 * urlExists
		 * @url = A URL to test
		 * Return boolean to determine a URLs existence
		 **/
		private function urlExists($url){
			if ((strpos($url, "http")) === false) $url = "http://" . $url;
			$headers = get_headers($url);
			if (is_array(@get_headers($url)) && $headers[0] == 'HTTP/1.1 200 OK')
				 return true;
			else
				 return false;
		}

		/**
		 * error
		 * @code = numeric error code
		 * Add error to response object
		 **/
		private function error($code) {
			// create error object
			$error = (object) array(
				"message" => self::$errorCodes[$code],
				"code" => $code
			);
			// add to response
			$this->response->errors[] = $error;
		}

	}

?>
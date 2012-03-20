<?php

class PVResponse {

	protected static $_statusMessages;

	public function init($config = array()) {
		
		$default = array('status_messages' => self::getDefaultStatusMessages());
		
		$config += $default;
		
		self::$_statusMessages = $config['status_messages'];
	}

	/**
	 * Creates an html response to display to the. The response can be override by setting the body
	 * to a value other than an empty string.
	 *
	 * @param int $status The status is the status code that will be sent as a header
	 * @param string $body The body of that will be displayed to the user. If no body is set, a default html template will be display with the status code
	 * @param array $options An array of options that define how content will be displayed
	 * 			-'content_type' _string_: The type of content that will be displayed. Default is text/html
	 * 			-'message' _string_: A message that can be displayed us no body is set. Default is empty string.
	 *
	 * @return string $response A response generated based on the variables
	 * @access public
	 */
	public static function createResponse($status, $body = '', $options = array()) {

		$defaults = array('content_type' => 'text/html', 'message' => '', 'status_header' => 'HTTP/1.1 ' );

		$options += $defaults;
		extract($options);

		$status_header = $options['status_header'] . $status . ' ' . self::getStatusMessage($status);

		header($status_header);
		header('Content-type: ' . $content_type);

		if ($body != '') {
			return $body;
		} else {

			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

			$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
						<html>
							<head>
								<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
								<title>' . $status . ' ' . self::getStatusMessage($status) . '</title>
							</head>
							<body>
								<h1>' . self::getStatusMessage($status) . '</h1>
								<p>' . $message . '</p>
								<hr />
								<address>' . $signature . '</address>
							</body>
						</html>';

			return $body;
		}
	}

	/**
	 * Set message to be used. This will ovveride the default ones that are currently
	 * being used.
	 *
	 * @param array $messages An array that the key is the interger that is a status and the value the description of the code
	 * @param array $options An array that can be used to define how the status codes are added
	 * 			-'use_message_defaults' _boolean_: Default is true and if set to true, will be the status codes that come with the class
	 *
	 * @return void
	 * @access public
	 */
	public static function setStatusMessages($messages, $options = array()) {

		$defaults = array('use_message_defaults' => true);
		$options += $defaults;

		if ($options['use_message_defaults'])
			$messages += self::getDefaultStatusMessages();

		$this -> _statusMessages = $messages;
	}

	/**
	 * Returns the message to a status based on the code that is passed.
	 *
	 * @param int $status An interger value representing the status code
	 *
	 * @return string $message Returns a message if found, otherwise returns false
	 * @access public
	 */
	public static function getStatusMessage($status) {

		return (isset($this -> _statusMessages[$status])) ? $this -> _statusMessages[$status] : '';
	}
	
	public static function writeHeader($headers = array()) {
		
		foreach($headers as $key => $value) {
			header($value);
		}
		
	}

	/**
	 * Get the default status messages associated with a status.
	 *
	 * @return array $messages Return an array in wich the key is the status and the value is a message
	 * @access public
	 */
	protected static function getDefaultStatusMessages() {

		$status = Array(100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => '(Unused)', 307 => 'Temporary Redirect', 400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported');

		return $status;
	}

}

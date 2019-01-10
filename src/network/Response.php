<?php
namespace prodigyview\network;

use prodigyview\design\StaticObject;

/**
 * Response is responsible for sending HTTP responses back to a client.
 *
 * The class takes into consideration the many generic responses HTTP has ranging from 200 to 500,
 * and helps make it easy to output the correct response with headers.
 *
 * Example:
 * 
 * ```php
 * //Init the class
 * Response::init();
 *
 * //Successful Response
 * Response::createResponse(200, 'Hello Word!');
 *
 * //Page Not Found
 * Response::createResponse(404, 'The page you are looking for cannot be found');
 * ```
 *
 * @package network
 */
class Response {

	use StaticObject;
	
	/**
	 * A list of generic HTTP Statuses
	 */
	protected static $_statusMessages;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * The initalizer for the static class
	 *
	 * @param array $config A list of options for initalizing the class
	 * 				- array 'status_messages' This is the ability to add in custom status messages
	 */
	public function init($config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$default = array('status_messages' => self::getDefaultStatusMessages());
	
			$config += $default;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$_statusMessages = $config['status_messages'];
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}

	/**
	 * Creates an html response to display to the. The response can be override by setting the body
	 * to a value other than an empty string.
	 *
	 * @param int $status The status is the status code that will be sent as a header
	 * @param string $content The content of that will be displayed to the user. If no body is set, a
	 * default html template will be display with the status code
	 * @param array $options An array of options that define how content will be displayed
	 * 			-'content_type' _string_: The type of content that will be displayed. Default is text/html
	 * 			-'message' _string_: A message that can be displayed us no body is set. Default is empty
	 * string.
	 *
	 * @return string $response A response generated based on the variables
	 * @access public
	 */
	public static function createResponse($status, $content = '', $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $status, $body, $options);

		$defaults = array(
			'content_type' => 'text/html',
			'message' => '',
			'status_header' => 'HTTP/1.1 '
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'status' => $status,
			'content' => $content,
			'options' => $options
		), array('event' => 'args'));
		
		$status = $filtered['status'];
		$content = $filtered['content'];
		$options = $filtered['options'];
		extract($options);

		$status_header = $options['status_header'] . $status . ' ' . self::getStatusMessage($status);

		header($status_header);
		header('Content-type: ' . $content_type);

		if ($content === '') {

			$signature = (isset($_SERVER['SERVER_SIGNATURE']) && $_SERVER['SERVER_SIGNATURE'] === '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : '';

			$content = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
		}

		$content = self::_applyFilter(get_class(), __FUNCTION__, $content, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $content, $status, $options);

		return $content;
	}

	/**
	 * Set message to be used. This will ovveride the default ones that are currently
	 * being used.
	 *
	 * @param array $messages An array that the key is the interger that is a status and the value the
	 * description of the code
	 * @param array $options An array that can be used to define how the status codes are added
	 * 			-'use_message_defaults' _boolean_: Default is true and if set to true, will be the status codes
	 * that come with the class
	 *
	 * @return void
	 * @access public
	 */
	public static function setStatusMessages($messages, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $messages, $options);

		$defaults = array('use_message_defaults' => true);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'messages' => $messages,
			'content' => $content,
			'options' => $options
		), array('event' => 'args'));
		
		$messages = $filtered['messages'];
		$options = $filtered['options'];

		if ($options['use_message_defaults'])
			$messages += self::getDefaultStatusMessages();

		$this->_statusMessages = $messages;
		self::_notify(get_class() . '::' . __FUNCTION__, $messages, $options);
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

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $status);

		$status = self::_applyFilter(get_class(), __FUNCTION__, $status, array('event' => 'args'));

		$message = (isset(self::$_statusMessages[$status])) ? self::$_statusMessages[$status] : '';

		$message = self::_applyFilter(get_class(), __FUNCTION__, $message, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $message, $status);

		return $message;
	}

	/**
	 * Writes PHP headers.Should be called before any content is outputted.
	 *
	 * @param array $headers An array of headers in $key value format or an array of arrays. If array of
	 * arrays is passed,
	 * 				the values in the array should be this:
	 * 				-'header' _string_: The string to be passed as the header.
	 * 				-'http_response_code' _int_: The http response code, default value is null.
	 * 				-'replace' _boolean_: Indicates if the header passed should replace a previously passed
	 * header. Default is true.
	 *
	 * @return void
	 * @access public
	 */
	public static function writeHeader($headers = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $headers);

		$headers = self::_applyFilter(get_class(), __FUNCTION__, $headers, array('event' => 'args'));

		$header_defaults = array(
			'http_response_code' => null,
			'replace' => true
		);
		
		foreach ($headers as $key => $value) {
			if (is_array($value)) {
				$value += $header_defaults;
				header($value['header'], $value['replace'], $value['http_response_code']);
			} else {
				header($value);
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $headers);
	}

	/**
	 * Get the default status messages associated with a status.
	 *
	 * @return array $messages Return an array in wich the key is the status and the value is a message
	 * @access public
	 */
	protected static function getDefaultStatusMessages() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$status = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);

		$status = self::_applyFilter(get_class(), __FUNCTION__, $status, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $status);

		return $status;
	}

}

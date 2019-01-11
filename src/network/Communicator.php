<?php
namespace prodigyview\network;

use prodigyview\design\StaticInstance;

/**
 * Communicator opens up communication with other services via Curl, SOAP, or Sockets.
 *
 * With the rise of RESTFUL API and Microservices, this class was designed to allow easy
 * communication with those services without having to rewrite the underlying commands.
 *
 * Example:
 * ```php
 * //CURL GET
 * $url = 'http://api.wunderground.com/api/Your_Key/conditions/q/CA/San_Francisco.json';
 *
 * $communicator = new Communicator();
 * $communicator->send('get',  $url);
 * print_r($communicator ->getResponseBody());
 *
 * //CURL POST
 * $url = 'http://api.example.com/createuser';
 * $data = array('name' =>'John Doe', 'email' => 'johndoe@example.com')
 * $communicator = new Communicator();
 * $communicator->send('POST',  $url, );
 * print_r($communicator ->getResponseBody());
 * ```
 *
 * @package network
 * @deprecated No longer need with CURL and Socket
 */
class Communicator {
	
	use StaticInstance;

	/**
	 * The handler for executing the communication and changes depending on type.
	 */
	protected $_handler = null;

	/**
	 * Headers to pass to the destination
	 */
	protected $_headers = array();

	/**
	 * Files to send to the destination
	 */
	protected $_files = array();

	/**
	 * Detailed information about the destinations response
	 */
	protected $_response_info = '';

	/**
	 * The response body in fill
	 */
	protected $_response = '';

	/**
	 * The protocol to use, default is curl but can use SOAP or sockets
	 */
	protected $_protocol = 'curl';

	/**
	 * The data to send to send
	 */
	protected $_data = null;

	/**
	 * An error response if communication fales
	 */
	protected $_error = null;

	/**
	 * The check to see if the connection has already been opened to prevent multiple connections from
	 * opening
	 */
	public $connectionActive = false;

	/**
	 * If the boolean set if the response has an error
	 */
	public $hasError = false;

	/**
	 * Sets the protocol, right now either being defaulting
	 * to curl, but SOAP and SOCKET can be set
	 *
	 * @param string $protocol The protocol, default is curl
	 *
	 * @return void
	 */
	public function __construct($protocol = null) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $protocol);

		if ($protocol) {
			$this->_protocol = strtolower(trim($protocol));
		}

	}

	/**
	 * Sends data over to an api endpoint
	 *
	 * @param string $method For curl, should either be POST, PUT, GET or DELETE. For soap, this should
	 * be the name of the method being called.
	 * @param string $url The endpoint of the api for REST or the wsdl for SOAP
	 * @param array $data The data to be passed to the end endpoint
	 * @param array $options Speciaized options to configure the sending client
	 * 				- timeout: The timeout in seconds
	 * 				- secure: Will require ssl for secure connection
	 * 				- enable_proxy : Will utilize a proxy for connecting to the endpoint
	 * 				- verbose: Display detailed bug log
	 *
	 * @return $response
	 */
	public function send($method, $url, $data = array(), $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $method, $url, $data, $options);

		$defaults = array(
			'timeout' => 500,
			'secure' => false,
			'enable_proxy' => false,
			'verbose' => false
		);

		$options += $defaults;

		$url = trim($url);

		if (!$this->connectionActive) {
			$this->openConnection($url, $options);
		}

		if ($this->_protocol === 'soap') {
			return $this->_sendSoap($method, $data);
		} else if ($this->_protocol === 'socket') {
			$this->_prepareData($data);
			$this->_sendSocket();
		} else {
			$this->setTimeout($options['timeout']);
			$this->secureSendingOnly($options['secure']);
			$this->enableProxy($options['enable_proxy']);
			$this->debug($options['verbose']);

			$method = strtolower(trim($method));

			if ($method === 'post') {
				return $this->_post($data);
			} else if ($method === 'get') {
				return $this->_get($url, $data);
			} else if ($method === 'put') {
				return $this->_put($data);
			} else if ($method === 'delete') {
				return $this->_delete($data);
			} else {
				curl_setopt($this->_handler, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				$this->_prepareData($data);
				return $this->_sendCurl($url);
			}
		}

		$this->connectionActive = false;

	}

	/**
	 * Adds a customer header to the request being sent. For example sending in TOKEN and 1234
	 * will produce the customer header- 'TOKEN : 1234'
	 *
	 * @param string $key The identifer for header, ie TOKEN
	 * @param string $value The value corresponding with the identfier, ie 1234
	 *
	 * @return void
	 */
	public function addHeader($key, $value) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $value);

		$this->_headers[$key] = $value;
	}

	/**
	 * The location of the file LOCALLY to be send
	 *
	 * @param string $file_location The location of the file
	 *
	 * @return void
	 */
	public function addFile($file_location) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file_location);

		$this->_files[] = $file_location;
	}

	/**
	 * Opens the connection for sending
	 *
	 * @param string $url The endpoint or url of the $wsdl
	 * @param array $options Optional parameters for the soap client
	 *
	 * @return void
	 */
	public function openConnection($url, $options = array()) {

		if ($this->_protocol === 'soap') {
			$this->_handler = new SoapClient($url, $options);
		} elseif ($this->_protocol === 'socket') {
			$protocol = 'tcp';

			if (isset($options['protocol'])) {
				$protocol = $options['protocol'];
			}

			$this->_handler = stream_socket_client($protocol . '://' . $url, $errno, $errorMessage);

		} else {
			$this->_handler = curl_init($url);
			curl_setopt($this->_handler, CURLOPT_URL, $url);
		}

		$this->connectionActive = true;
	}

	/**
	 * Sends a POST to an endpoint using curl
	 *
	 * @param array $data Data to be sent
	 */
	protected function _post($data = array()) {
		$this->_prepareData($data);
		curl_setopt($this->_handler, CURLOPT_POST, 1);

		return $this->_sendCurl();
	}

	/**
	 * Sends a PUT to an endpoint using curl
	 *
	 * @param array $data Data to be sent
	 */
	protected function _put($data = array()) {
		$this->_prepareData($data);

		curl_setopt($this->_handler, CURLOPT_PUT, 1);
		curl_setopt($this->_handler, CURLOPT_CUSTOMREQUEST, 'PUT');

		return $this->_sendCurl();
	}

	/**
	 * Sends a GET to an endpoint using curl
	 *
	 * @param string $url The url to curl
	 * @param array $data Data to be sent
	 */
	protected function _get($url, $data = array()) {
		$url .= '?' . http_build_query($data);
		curl_setopt($this->_handler, CURLOPT_URL, $url);
		return $this->_sendCurl();
	}

	/**
	 * Sends a DELETE to an endpoint using curl
	 *
	 * @param array $data Data to be sent
	 */
	protected function _delete($data = array()) {
		$this->_prepareData($data);

		curl_setopt($this->_handler, CURLOPT_CUSTOMREQUEST, 'DELETE');

		return $this->_sendCurl();
	}

	/**
	 * Prepares the data to be sent to the client, including file ata
	 *
	 * @param array $data The data to send
	 *
	 * @return void
	 */
	protected function _prepareData($data = array()) {
		if ($this->_protocol === 'socket') {
			if (is_array($data)) {
				$data = implode(' ', $data);
			}

			$this->_data = $data;
		} else {
			if ($this->_files) {
				$files = array();

				foreach ($this -> _files as $key => $file) {
					$file_key = 'blob[' . $key . ']';
					$file[$file_key] = '@' . realpath($file);
				}

				curl_setopt($this->_handler, CURLOPT_POSTFIELDS, $files);

				$this->_data = $files;
			} else if ($data) {
				$this->_data = http_build_query($data);

				curl_setopt($this->_handler, CURLOPT_POSTFIELDS, $this->_data);
			}
		}
	}

	/**
	 * Adds any special authentication required.
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @return void
	 */
	public function setAuthentication($username, $password) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $username, $password);

		curl_setopt($this->_handler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->_handler, CURLOPT_USERPWD, $username . ':' . $password);
	}

	/**
	 * Will require all communication to be secure over ssl
	 *
	 * @param boolean $secure Setting to true forces security protocals to be enabled
	 *
	 * @return void
	 */
	public function secureSendingOnly($secure = false) {
		curl_setopt($this->_handler, CURLOPT_SSL_VERIFYPEER, $secure);
		curl_setopt($this->_handler, CURLOPT_SSL_VERIFYHOST, $secure);
	}

	/**
	 * Set any timeouts for long request or responses
	 *
	 * @param $timeout The timeout in milliseconds
	 *
	 * @return void
	 */
	public function setTimeout($timeout) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $timeout);

		curl_setopt($this->_handler, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($this->_handler, CURLOPT_TIMEOUT, $timeout);
	}

	/**
	 * Enable/Disable the usage of a proxy in the curl call
	 *
	 * @param boolean $proxy
	 */
	public function enableProxy($proxy = false) {
		curl_setopt($this->_handler, CURLOPT_FOLLOWLOCATION, $proxy);
		curl_setopt($this->_handler, CURLOPT_PROXY, $proxy);
	}

	/**
	 * Enable verbose dislpay for debugging
	 *
	 * @param boolean $debug Enable Debugging features
	 */
	public function debug($debug) {
		curl_setopt($this->_handler, CURLOPT_VERBOSE, $debug);
	}

	/**
	 * Takes the compiled data and sends a curl request
	 *
	 * @return mixed $response
	 */
	protected function _sendCurl() {
		curl_setopt($this->_handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->_handler, CURLOPT_HEADER, 1);

		if ($this->_headers) {
			$final_headers = array();
			foreach ($this -> _headers as $key => $value) {
				$final_headers[] = $key . ' : ' . $value;
			}

			curl_setopt($this->_handler, CURLOPT_HTTPHEADER, $final_headers);
		};

		$response = curl_exec($this->_handler);

		if ($response === false) {
			$this->_error = curl_error($this->_handler);
			$this->hasError = true;
		}
		$this->_response = $response;

		$this->_response_info = curl_getinfo($this->_handler);

		curl_close($this->_handler);

		self::_notify(get_class() . '::' . __FUNCTION__, $this);

		return $response;
	}

	/**
	 * Sends the request via SOAP protocoal
	 *
	 * @param string $method The function to call via soap
	 * @param array $data The data being passed to that function
	 *
	 * @return $string
	 */
	protected function _sendSoap($method, $data = array()) {
		$response = $this->_handler->__soapCall($method, array($data));

		self::_notify(get_class() . '::' . __FUNCTION__, $this, $method, $data);

		return $response;
	}

	/**
	 * Sends the communication over a socket
	 *
	 * @param string $method The method to send
	 *
	 * @todo work on a test socket communication
	 */
	protected function _sendSocket($method) {
		if ($this->_handler !== false) {
			fwrite($this->_handler, $this->_data);
			$this->_response = stream_get_contents($client);
			fclose($this->_handler);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $this, $method);

		return $this->_response;
	}

	/**
	 * Retrieves the full response
	 *
	 * @param string
	 */
	public function getResponse() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		return $this->_response;
	}

	/**
	 * Gets information about the response
	 *
	 * @return string
	 */
	public function getResponseInfo() {
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		return $this->_response_info;
	}

	/**
	 * Gets the headers that came back in a response
	 *
	 * @param string
	 */
	public function getResponseHeader() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		return trim(substr($this->_response, 0, $this->_response_info['header_size']));

	}

	/**
	 * Retrieves a response body
	 *
	 * @return $string
	 */
	public function getResponseBody() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		return substr($this->_response, $this->_response_info['header_size']);
	}

	/**
	 * Gets the error response
	 *
	 * @return string
	 */
	public function getError() {
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		return $this->_error;
	}

}

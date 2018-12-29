<?php
namespace prodigyview\network;

use prodigyview\util\Conversion;
use prodigyview\design\StaticInstance;

/**
 * Request is responsible for receiving and parsing income HTTP requests.
 *
 * Built for receiving communication from other sources, Request has the ability to take a request,
 * parse the headers, get the data, determine what kind of requests, and other features.
 *
 * Example:
 * ```php
 * $request = new Request();
 *
 * if($request -> isAjaxRequest()) {
 * 	echo "AJAX REQUEST\n";
 * }
 *
 * if(strtolower($request -> getRequestMethod()) =='post') {
 *     echo "A Post Request was send\n";
 * }
 *
 * $data = getRequestData();
 * print_r($data);
 * ```
 *
 * @package network
 */
class Request {
	
	use StaticInstance;

	/**
	 * The data recieved from the request
	 */
	protected $_request_data;

	/**
	 * The method used to how the request was sent. Normally, GET, POST, PUT, DELETE but others request
	 * types are available.
	 */
	protected $_request_method;

	/**
	 * The information recieved from the HTTP REQUEST
	 */
	protected $_http_request;

	/**
	 * The headers found in the request
	 */
	protected $_headers = array();

	/**
	 * The kinds of mobile devices
	 */
	protected $_mobile_devices;

	/**
	 * Set up the default variables for the Request class.
	 *
	 * @param array $options An array of options that can be used to customize the class.
	 *
	 * @return Request instance
	 */
	public function __construct(array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);

		$defaults = array(
			'process_request' => true,
			'parse_headers' => true,
			'http_accept' => (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json')) ? 'json' : 'xml',
			'request_method' => 'REQUEST_METHOD',
			'mobile_devices' => "/(nokia|iphone|android|motorola|^mot-|softbank|foma|docomo|kddi|up.browser|up.link|htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte-|longcos|pantech|gionee|^sie-|portalmmm|jigs browser|hiptop|^ucweb|^benq|haier|^lct|operas*mobi|opera*mini|320x320|240x320|176x220)/i"
		);

		$options += $defaults;

		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));

		$this->_request_data = array();

		$this->_request_method = $options['request_method'];

		$this->_http_request = $options['http_accept'];

		$this->_request_method = $options['request_method'];

		$this->_mobile_devices = $options['mobile_devices'];

		if ($options['process_request']) {
			$this->processRequest();
		}
		
		if ($options['parse_headers']) {
			$this->parseHeaders();
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $this);

		return $this;
	}

	/**
	 * Process the request by breaking down the variables and adding it to the protected variable
	 * $_request_data. This method is ran automatically by the constructor but can be dispable in the
	 * constructor. If it is disabled, it should be ran before using other methods of this class.
	 *
	 * @return void
	 * @access public
	 * @todo make ability to handle 'head', 'delete' and 'continue'
	 */
	public function processRequest() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$this->_request_method = strtolower($_SERVER[$this->_request_method]);

		switch ($this -> _request_method) {

			case 'get' :
				$this->_request_data = $_GET;
				break;
			case 'post' :
				$this->_request_data = ($_POST) ? : file_get_contents('php://input');
				break;
			case 'put' :
				$this->_request_data = file_get_contents('php://input');
				break;
			case 'delete' :
				$this->_request_data = file_get_contents('php://input');
				break;
			case 'head' :
				echo 'Run around like a chicken with it\'s head cut off';
				break;
			case 'continue' :
				echo 'Run around like a chicken with it\'s head cut off';
				break;
		}

	}

	/**
	 * Parses the HTTP headers found in the request and assigns it to the class
	 * $_headers variable.
	 * 
	 * @return void
	 * @author https://stackoverflow.com/questions/541430/how-do-i-read-any-request-header-in-php
	 */
	public function parseHeaders() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			
			$this ->_headers[$header] = $value;
		}
	}

	/**
	 * Sets the data that will act as the data for a request.
	 *
	 * @param array $data Data to set as the request
	 *
	 * @return void
	 * @access public
	 */
	public function setRequestData($data) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data);

		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'args'));

		$this->_request_data = $data;

		self::_notify(get_class() . '::' . __FUNCTION__, $data);
	}

	/**
	 * Returns the request data. The data can be return in certain formats if neccesary.
	 *
	 * @param string $format The default format will return the data as set in the class. The formats
	 * 						can be set to the options of json, array and object, and will return the
	 * 						data in each of those types.
	 *
	 * @return mixed $data The data return in a certain format
	 * @access public
	 * @todo add ability to format data in xml and to serialize
	 */
	public function getRequestData($format = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $format);

		$format = self::_applyFilter(get_class(), __FUNCTION__, $format, array('event' => 'args'));

		switch ($format) {
			case 'json' :
				if (is_array($this->_request_data)) {
					$data = json_encode($this->_request_data);
				} else {
					$data = $this->_request_data;
				}
				break;
			case 'array' :
				if (is_string($this->_request_data) && json_decode($this->_request_data) && json_last_error() == JSON_ERROR_NONE) {
					$data = json_decode($this->_request_data, true);
				} else {
					$data = $this->_request_data;
				}
				break;
			case 'object' :
				if (is_array($this->_request_data)) {
					$data = Conversion::arrayToObject($this->_request_data);
				} elseif (is_string($this->_request_data) && json_decode($this->_request_data) && json_last_error() == JSON_ERROR_NONE) {
					$data = json_decode($this->_request_data);
				} else {
					$data = $this->_request_data;
				}
			default :
				$data = $this->_request_data;
				break;
		}

		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $data, $format);

		return $data;
	}

	/**
	 * Returns the request method, where it is get, put, post or another form.
	 *
	 * @return string $method The method that was sent in a header
	 * @access public
	 */
	public function getRequestMethod() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$method = $this->_request_method;

		$method = self::_applyFilter(get_class(), __FUNCTION__, $method, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $method);

		return $method;
	}

	/**
	 * Sets the request method to a certain type.
	 *
	 * @param string $method The method to set as the request method
	 *
	 * @return void
	 * @access public
	 */
	public function setRequestMethod($method) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $method);

		$method = self::_applyFilter(get_class(), __FUNCTION__, $method, array('event' => 'args'));

		$this->_request_method = $method;
		self::_notify(get_class() . '::' . __FUNCTION__, $method);
	}

	/**
	 * Returns a boolean that determines if the request was made by a mobile device.
	 *
	 * @return boolean $ismobile Returns true if the device is mobile, otherwise false
	 * @access public
	 */
	public function isMobile() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		return (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) || preg_match($this->_mobile_devices, strtolower($_SERVER['HTTP_USER_AGENT'])));
	}

	/**
	 * Returns the mobile device that is currently being used
	 *
	 * @return mixed $device Returns the mobile device and if none, returns false
	 * @access public
	 */
	public function getMobileDevice() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		preg_match($this->_mobile_devices, strtolower($_SERVER['HTTP_USER_AGENT']), $matches);

		$device = (isset($matches[0]) && !empty($matches[0])) ? $matches[0] : false;

		$device = self::_applyFilter(get_class(), __FUNCTION__, $device, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $device);

		return $device;
	}

	/**
	 * Determines if the request is an ajax request.
	 *
	 * @return boolean $isAjax Returns true if the request is ajax, otherwise false
	 * @access public
	 */
	public function isAjaxRequest() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
			return true;

		return false;
	}

	/**
	 * Attempts to retrieve a header that was found in the request
	 * 
	 * @param string $header The header being searched for
	 * 
	 * @return mixed, returns the header if found, otherwise returns false
	 */
	public function getHeader($header) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $header);
		
		$found_value = false;
		
		$header = self::_applyFilter(get_class(), __FUNCTION__, $header, array('event' => 'args'));
		
		$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower($header))));
		
		if(isset($this -> _headers[$header])) {
			$found_value = $this -> _headers[$header];
		}
		
		$found_value = self::_applyFilter(get_class(), __FUNCTION__, $found_value, array('event' => 'return'));
		self::_notify(get_class() . '::' . __FUNCTION__, $header, $found_value);
		
		return $found_value;
	}

}

<?php
/*
 *Copyright 2011 ProdigyView LLC. All rights reserved.
 *
 *Redistribution and use in source and binary forms, with or without modification, are
 *permitted provided that the following conditions are met:
 *
 *   1. Redistributions of source code must retain the above copyright notice, this list of
 *      conditions and the following disclaimer.
 *
 *   2. Redistributions in binary form must reproduce the above copyright notice, this list
 *      of conditions and the following disclaimer in the documentation and/or other materials
 *      provided with the distribution.
 *
 *THIS SOFTWARE IS PROVIDED BY My-Lan AS IS'' AND ANY EXPRESS OR IMPLIED
 *WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 *FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL My-Lan OR
 *CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 *ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 *ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *The views and conclusions contained in the software and documentation are those of the
 *authors and should not be interpreted as representing official policies, either expressed
 *or implied, of ProdigyView LLC.
 */
 
class PVRequest extends PVStaticInstance {
	
	protected $_request_data;
	
	protected $_request_method;

	protected $_http_request;
	
	protected $_mobile_devices;
	
	/**
	 * Set up the default variables for the Request class.
	 * 
	 * @param $mixed $data Currently not used but can be data of any sort
	 * @param array $options An array of options that can be used to customize the class.
	 */
	public function __construct(array $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		$defaults = array(
			'process_request' => true,
			'http_accept' =>  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json')) ? 'json' : 'xml',
			'request_method' => 'REQUEST_METHOD',
			'mobile_devices' => "/(nokia|iphone|android|motorola|^mot-|softbank|foma|docomo|kddi|up.browser|up.link|htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte-|longcos|pantech|gionee|^sie-|portalmmm|jigs browser|hiptop|^ucweb|^benq|haier|^lct|operas*mobi|opera*mini|320x320|240x320|176x220)/i"		
			);
		
		$options += $defaults;
		
		$options = self::_applyFilter(get_class(), __FUNCTION__, $options, array('event' => 'args'));
		
		$this -> _request_data = array();
		
		$this -> _request_method = $options['request_method'];
		
		$this -> _http_request = $options['http_accept'];
		
		$this -> _request_method = $options['request_method'];
		
		$this -> _mobile_devices = $options['mobile_devices'];
		
		if($options['process_request']) {
			$this -> processRequest();
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

		$this -> _request_method = strtolower($_SERVER[$this -> _request_method]);
		
		switch ($this -> _request_method) {

			case 'get' :
				$this -> _request_data = $_GET;
				break;
			case 'post' :
				$this -> _request_data = $_POST;
				break;
			case 'put' :
				$this -> _request_data = file_get_contents('php://input');
				break;
			case 'delete' :
				$this -> _request_data = file_get_contents('php://input');
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
		
		$this ->_request_data = $data;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $data);
	}
	
	/**
	 * Returns the request data. The data can be return in certain formats if neccesary.
	 * 
	 * @param string $format The default format will return the data as set in the class. If set to json, the data will be return in a json format
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
			case 'json':
				$data = json_encode($this ->_request_data);
				break;
			default:
				$data = $this ->_request_data;
				break;
		}
		
		$data = self::_applyFilter(get_class(), __FUNCTION__, $data , array('event' => 'return'));
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
		
		$method =$this ->_request_method;
		
		$method = self::_applyFilter(get_class(), __FUNCTION__, $method , array('event' => 'return'));
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
		
		$this -> _request_method = $method;
		self::_notify(get_class() . '::' . __FUNCTION__, $method);
	}
	
	/**
	 * Returns a boolean that determines if the request was made by a mobile device.
	 * 
	 * @return boolean $ismobile Returns true if the device is mobile, otherwise false
	 * @access public
	 */
	public function isMobile(){
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		return (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) || preg_match($this -> _mobile_devices, strtolower($_SERVER['HTTP_USER_AGENT'])));
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
			
		preg_match($this -> _mobile_devices, strtolower($_SERVER['HTTP_USER_AGENT']), $matches);
		
		$device = (isset($matches[0]) && !empty($matches[0])) ? $matches[0] : false;
		
		$device = self::_applyFilter(get_class(), __FUNCTION__, $device , array('event' => 'return'));
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
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
			return true;
		
		return false;
		
	}
}

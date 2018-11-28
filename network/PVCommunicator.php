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
 
 class PVCommunicator extends PVStaticInstance {
 	
	protected $_handler = null;
	
	protected $_headers = array();
	
	protected $_files = array();
	
	protected $_response_info = '';
	
	protected $_response = '';
	
	protected $_protocol = 'curl';
	
	protected $_data = null;
	
	protected $_error = null;
	
	public $hasError = true;
	
	/**
	 * Sets the protocol, right now either being defaulting
	 * to curl, but SOAP and SOCKET can be set
	 */
	public function __constrcut($protocol = null) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		if($protocol) {
			$this -> _protocol = strtolower(trim($protocol));
		}
		
	}
	
	/**
	 * Sends data over to an api endpoint
	 * 
	 * @param string $method For curl, should either be POST, PUT, GET or DELETE. For soap, this should be the name of the method being called.
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
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		$defaults = array(
			'timeout' => 500,
			'secure' => false,
			'enable_proxy' => false,
			'verbose' => false 
		);
		
		$options += $defaults;
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		$url = trim($url);
		
		$this -> _openConnection($url, $options);
		
		if($this -> _protocol === 'soap') {
			return $this -> _sendSoap($method, $data);
		} else if($this -> _protocol === 'socket') {
			$this -> _prepareData($data);
			$this -> _sendSocket();
		} else {
			$this -> setTimeout($options['timeout']);
			$this -> secureSendingOnly($options['secure']);
			$this -> enableProxy($options['enable_proxy']);
			$this -> debug($options['verbose']);
			
			$method = strtolower(trim($method));
			
			if($method === 'post') {
				return $this -> _post($data);
			} else if($method === 'get') {
				return $this -> _get($url, $data);
			} else if($method === 'put') {
				return$this -> _put($data);
			} else if($method === 'delete') {
				return $this -> _delete($data);
			} else {
				curl_setopt($this -> _handler, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				$this -> _prepareData($data);
				return $this -> _sendCurl($url);
			}
		}
		
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
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		$this -> _headers[$key]= $value;
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
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		$this -> _files[] = $file_location;
	}
	
	/**
	 * Opens the connection for sending
	 * 
	 * @param string $url The endpoint or url of the $wsdl
	 * @param array $options Optional parameters for the soap client
	 * 
	 * @return void
	 */
	protected function _openConnection($url, $options = array()) {
		
		if($this -> _protocol === 'soap') {
			$this -> _handler = new SoapClient($url, $options);
		} elseif($this -> _protocol === 'socket') {
			$protocol = 'tcp';
			
			if(isset($options['protocol'])) {
				$protocol = $options['protocol'];
			}

			$this -> _handler = stream_socket_client($protocol.'://'. $url, $errno, $errorMessage);
			
		} else {
			$this -> _handler = curl_init($url);
			curl_setopt($this -> _handler, CURLOPT_URL, $url);
		}
	}
	
	/**
	 * Sends a POST to an endpoint using curl
	 * 
	 * @param array $data Data to be sent
	 */
	protected function _post($data = array()) {
		$this -> _prepareData($data);
		curl_setopt($this -> _handler, CURLOPT_POST, 1);
		
		return $this -> _sendCurl();
	}
	
	/**
	 * Sends a PUT to an endpoint using curl
	 * 
	 * @param array $data Data to be sent
	 */
	protected function _put($data = array()) {
		$this -> _prepareData($data);
		
		curl_setopt($this -> _handler, CURLOPT_PUT, 1);
		curl_setopt($this -> _handler, CURLOPT_CUSTOMREQUEST, 'PUT');
		
		return $this -> _sendCurl();
	}
	
	/**
	 * Sends a GET to an endpoint using curl
	 * 
	 * @param array $data Data to be sent
	 */
	protected function _get($url, $data = array()) {
		$url .= '?' . http_build_query($data);
		curl_setopt($this -> _handler, CURLOPT_URL, $url);
		return $this -> _sendCurl();
	}
	
	/**
	 * Sends a DELETE to an endpoint using curl
	 * 
	 * @param array $data Data to be sent
	 */
	protected function _delete($data = array()) {
		$this -> _prepareData($data);
		
		curl_setopt($this -> _handler, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
		return $this -> _sendCurl();
	}
	
	/**
	 * Prepares the data to be sent to the client, including file ata
	 * 
	 * @param array $data The data to send
	 * 
	 * @return void
	 */
	protected function _prepareData($data = array()) {
		if($this -> _protocol === 'socket') {
			if(is_array($data)) {
				$data = implode(' ', $data);
			}
			
			$this -> _data = $data;
		} else {
			if($this -> _files) {
				$files = array();
				
				foreach($this -> _files as $key => $file) {
					$file_key = 'blob['. $key. ']';
					$file[$file_key] = '@' . realpath($file);
				}
				
				curl_setopt($this -> _handler, CURLOPT_POSTFIELDS, $files);
				
				$this -> _data = $files;
			} else if($data){
				$this -> _data = http_build_query($data);
				
				curl_setopt($this -> _handler, CURLOPT_POSTFIELDS, $this -> _data);
			}
		}
	}
	
	/**
	 * Adds any special authentication required.
	 * 
	 * @param string $username
	 * @param $sring $password
	 * 
	 * @return void
	 */
	public function setAuthentication($username, $password) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		curl_setopt($this -> _handler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    		curl_setopt($this -> _handler, CURLOPT_USERPWD, $username.':'.$password);
	}
	
	/**
	 * Will require all communication to be secure over ssl
	 * 
	 * @param boolean $secure Setting to true forces security protocals to be enabled
	 * 
	 * @return void
	 */
	public function secureSendingOnly($secure = false) {
		curl_setopt($this -> _handler, CURLOPT_SSL_VERIFYPEER, $secure);
		curl_setopt($this -> _handler, CURLOPT_SSL_VERIFYHOST, $secure);
	}
	
	/**
	 * Set any timeouts for long request or responses
	 * 
	 * @return void
	 */
	public function setTimeout($timeout) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		curl_setopt($this -> _handler, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($this -> _handler, CURLOPT_TIMEOUT, $timeout);
	}
	
	/**
	 * Enable/Disable the usage of a proxy in the curl call
	 */
	public function enableProxy($proxy = false) {
		curl_setopt($this -> _handler, CURLOPT_FOLLOWLOCATION, $proxy);
		curl_setopt($this -> _handler, CURLOPT_PROXY, $proxy);
	}
	
	/**
	 * Enable verbose dislpay for debugging
	 */
	public function debug($debug) {
		curl_setopt($this -> _handler, CURLOPT_VERBOSE, $debug);
	}
	
	/**
	 * Takes the compiled data and sends a curl request
	 * 
	 * @return mixed $response
	 */
	protected function _sendCurl() {
		curl_setopt($this -> _handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this -> _handler, CURLOPT_HEADER, 1);
		
	
		if($this -> _headers) {
			$final_headers = array();
			foreach($this -> _headers as $key => $value) {
				$final_headers[] = $key . ' : '. $value;
			}
			
			curl_setopt($this -> _handler, CURLOPT_HTTPHEADER, $final_headers);	
		};
		
		$response = curl_exec($this -> _handler);
		
		if($response === false) {
			$this -> _error = curl_error($this -> _handler);
			$this -> hasError = true;
		}
		$this -> _response = $response;
		
		$this -> _response_info = curl_getinfo($this -> _handler);
		
    		curl_close($this -> _handler);
			
		self::_notify(get_class() . '::' . __FUNCTION__, $this);

    		return $response;
	}
	
	protected function _sendSoap($method, $data = array()) {
		$response = $this -> _handler -> __soapCall($method, array($data));
		
		self::_notify(get_class() . '::' . __FUNCTION__, $this);
		
		return $response;
	}
	
	protected function _sendSocket($method) {
		if ($this -> _handler !== false) {
    			fwrite($this -> _handler, $this -> _data);
			$this -> _response = stream_get_contents($client);
			fclose($this -> _handler);
		}
		
		self::_notify(get_class() . '::' . __FUNCTION__, $this);
		
		return $this -> _response;
	}
	
	public function getResponse() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		return $this -> _response;
	}
	
	public function getResponseInfo() {
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		return $this -> _response_info;
	}
	
	public function getResponseHeader() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		return trim(substr($this -> _response, 0, $this -> _response_info['header_size']));
		
	}
	
	public function getResponseBody() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		return substr($this -> _response, $this -> _response_info['header_size']);
	}
	
	public function getError() {
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		return $this -> _error;
	}
	
 }

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
	 * 
	 * @return $response
	 */
	public function send($method, $url, $data = array(), $options = array()) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		$this -> _openConnection($url, $options);
		
		if($this -> _protocol === 'soap') {
			return $this -> _sendSoap($method, $data);
		} else if($this -> _protocol === 'socket') {
			$this -> _prepareData($data);
			$this -> _sendSocket();
		} else {
			$method = strtolower(trim($method));
			
			if($method === 'post') {
				return $this -> _post($data);
			} else if($method === 'get') {
				return $this -> _get($data);
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
		$url = sprintf("%s?%s", $url, http_build_query($data));
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
	 * Set any timeouts for long request or responses
	 * 
	 * @return void
	 */
	public function setTimeout($timeout) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options);
		
		curl_setopt($this -> _handler, CURLOPT_CONNECTTIMEOUT, $timeout);
	}
	
	/**
	 * Takes the compiled data and sends a curl request
	 * 
	 * @return mixed $response
	 */
	protected function _sendCurl() {
		curl_setopt($this -> _handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this -> _handler, CURLOPT_HEADER, TRUE);
		curl_setopt($this -> _handler, CURLOPT_SSL_VERIFYPEER, false);
	
		if($this -> _headers) {
			$final_headers = array();
			foreach($this -> _headers as $key => $value) {
				$final_headers[] = $key . ' : '. $value;
			}
			
			curl_setopt($this -> _handler, CURLOPT_HTTPHEADER, $final_headers);	
		}
		
		$response = curl_exec($this -> _handler);
		
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
	
 }

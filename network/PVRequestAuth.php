<?php

class PVRequestAuth {
	
	protected $_username;
	
	protected $_password;
	
	protected $_realm;
	
	protected $_digest;
	
	protected $_server_auth_digest;
	
	protected $_server_auth_user;
	
	protected $_server_auth_password;
	
	/**
	 * The constructor called when initializing the object
	 * 
	 * @param array $options Options used to help
	 */
	public function __construct(array $options = array()) {
		
		$defaults = array(
			'username' => '',
			'password' => '',
			'http_auth_digest' => 'PHP_AUTH_DIGEST',
			'server_auth_user' => 'PHP_AUTH_USER',
			'server_auth_password' => 'PHP_AUTH_PW',
			'realm' => uniqid(),
			'process_auth' => true
		);
		
		$options += $defaults;
		
		$this -> _username = $options['username'];
		$this -> _password = $options['password'];
		
		$this -> _server_auth_digest = $options['http_auth_digest'];
		$this -> _server_auth_user = $options['http_auth_user'];
		$this -> _server_auth_password = $options['http_auth_password'];
		$this -> _realm = $options['realm'];
		
		if($options['process_auth'])
			$this -> processAuth();
		
	}
	
	/**
	 * This methods sets up the digest, username, and password for the class. The digest, username, and password
	 * are server variables and are associated with http_auth_digest, server_auth_user, server_auther password
	 * set in the construct. This method is called automatically by the constructor.
	 * 
	 * @return void
	 * @access public
	 */
	public function processAuth() {
		
		$this -> _digest = $this -> _parseDigest($_SERVER[$this -> _server_auth_digest]);
		
		$this -> _username = $_SERVER[$this -> _server_auth_user];
		
		$this -> _password = $_SERVER[$this -> _server_auth_password];
		
	}
	
	/**
	 * Authenticate takes place by validating credentials passed in against the username and password
	 * retrieved during the processAuth method.
	 * 
	 * @param array $credentials An array that should contain the username and password to check against
	 * 
	 * @return boolean $authenticated Returns true if the variables match, otherwise false
	 * @access public
	 */
	public function authenticate($credentials = array()) {
			
		if($this -> _username == $credentials['username'] && $this -> _password == $credentials['password'] && $this -> _digest)
			return true;
		
		return false;
	}
	
	/**
	 * Returns the username and password set in the processAuth Method.
	 * 
	 * @return array $credtentials
	 * @access public
	 */
	public function getCredentials() {
		return array('username' => $this -> _username, 'password' => $this -> _password);
	}
	
	/**
	 * Set the credentials to be stored in the class as protected variables.
	 * 
	 * @param arry $credentiials Takes in the 'username' and 'password' in an array
	 * 
	 * @return void
	 * @access public
	 */
	public function setCredentials($credentials = array()) {
		
		$this -> _username = $credentials['username'];
		
		$this -> _password = $credentials['password'];
	}
	
	/**
	 * Set the realm
	 * 
	 * @param string $realm The realm to be used
	 * 
	 * @return void
	 * @access public
	 */
	public function setRealm($realm) {
		$this -> _realm = $realm;
	}
	
	/**
	 * Returns the realm that is currently set.
	 * 
	 * @return string $realm
	 * @access public
	 */
	public function getRealm() {
		return $this -> _realm;
	}
	
	/**
	 * Parses the digest retrieved from the server variable.
	 * 
	 * @param string $digest The digest data retrieved from the server application
	 * 
	 * @return mixed $value Returns the digest in array if all the parts are available, otherwirse false
	 * @access protected
	 */
	protected function _parseDigest($digest) {
			
		preg_match_all('@(username|nonce|uri|nc|cnonce|qop|response)'. '=[\'"]?([^\'",]+)@', $digest, $matches);
	    $data = array_combine($matches[1], $matches[2]);
		
	    return (count($data)==7) ? $data : false; 
	}
}

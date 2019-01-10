<?php
namespace prodigyview\network;

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

class RequestAuth {

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

		$this->_username = $options['username'];
		$this->_password = $options['password'];

		$this->_server_auth_digest = $options['http_auth_digest'];
		$this->_server_auth_user = $options['http_auth_user'];
		$this->_server_auth_password = $options['http_auth_password'];
		$this->_realm = $options['realm'];

		if ($options['process_auth'])
			$this->processAuth();

	}

	/**
	 * This methods sets up the digest, username, and password for the class. The digest, username, and
	 * password
	 * are server variables and are associated with http_auth_digest, server_auth_user, server_auther
	 * password
	 * set in the construct. This method is called automatically by the constructor.
	 *
	 * @return void
	 * @access public
	 */
	public function processAuth() {

		$this->_digest = $this->_parseDigest($_SERVER[$this->_server_auth_digest]);

		$this->_username = $_SERVER[$this->_server_auth_user];

		$this->_password = $_SERVER[$this->_server_auth_password];

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

		if ($this->_username === $credentials['username'] && $this->_password === $credentials['password'] && $this->_digest)
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
		return array(
			'username' => $this->_username,
			'password' => $this->_password
		);
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

		$this->_username = $credentials['username'];

		$this->_password = $credentials['password'];
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
		$this->_realm = $realm;
	}

	/**
	 * Returns the realm that is currently set.
	 *
	 * @return string $realm
	 * @access public
	 */
	public function getRealm() {
		return $this->_realm;
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

		preg_match_all('@(username|nonce|uri|nc|cnonce|qop|response)' . '=[\'"]?([^\'",]+)@', $digest, $matches);
		$data = array_combine($matches[1], $matches[2]);

		return (count($data) === 7) ? $data : false;
	}

}

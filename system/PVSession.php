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

class PVSession extends PVStaticObject {
	
	private static $cookie_lifetime=5000;
	private static $cookie_path='/';
	private static $cookie_domain='';
	private static $cookie_secure=false;
	private static $cookie_httponly=false;
	private static $hash_cookie=false;
	private static $hash_session=false;
	
	/**
	 * Initializes the static class PVSessions. Values passed can be
	 * used to set the default cookie options and the default session
	 * options.
	 * 
	 * @param array $session_vars An array of values that set how the class functions.
	 * 			-'cookie_path' _string_: The path where the cookie is to be stored
	 * 			-'cookie_domain' _string_: The domain the that the cookie resides on
	 * 			-'cookie_secure' _boolean_:
	 * 			-'cookie_httponly' _boolean_:
	 * 			-'cookie_lifetime' _int_: The amount of time the cookie is active for
	 * 			-'hash_cookie' _boolean_ :Hash the cookie to its value is not easily readable
	 * 			-'hash_session' _boolean: Has a season so its value is not easily readable
	 * 			-'session_name' _string_ : Name of the current session
	 * 			-'session_lifetime' _int_: The life time of the session, in seconds
	 * 			-'session_path' _string_: The path of the session.
	 * 			-'session_domain' _string_: The domain of the session. Default is current.
	 * 			-'session_secure'_boolean_: Sets if session is secure
	 * 			-'session_httponly' _boolean: Allows a session only over http
	 * @return void
	 */
	public static function init($session_vars=array()) {
		
		$defaults=array(
			'cookie_path'=>'/',
			'cookie_domain'=>$_SERVER['HTTP_HOST'],
			'cookie_secure'=>false,
			'cookie_httponly'=>false,
			'cookie_lifetime'=>5000,
			'hash_cookie'=>false,
			'hash_session'=>false,
			'session_name'=>'pv_session',
			'session_lifetime'=>2000,
			'session_path'=>'/',
			'session_domain'=>$_SERVER['HTTP_HOST'],
			'session_secure'=>false,
			'session_httponly'=>false,
			'session_start'=>true
		);
		
		if(empty($session_vars))
			$session_vars=PVConfiguration::getSiteSessionConfiguration();
		
		$session_vars += $defaults;
		
		self::$cookie_path=$session_vars['cookie_path'];
		self::$cookie_domain=$session_vars['cookie_domain'];
		self::$cookie_secure=$session_vars['cookie_secure'];
		self::$cookie_httponly=$session_vars['cookie_httponly'];
		self::$cookie_lifetime=$session_vars['cookie_lifetime'];
		
		self::$hash_cookie=$session_vars['hash_cookie'];
		self::$hash_session=$session_vars['hash_session'];
		
		session_name($session_vars['session_name']);
		session_set_cookie_params($session_vars['session_lifetime'], $session_vars['session_path'], $session_vars['session_domain']);
		
		if($session_vars['session_start'])
			session_start();
	}
	
	/**
	 * Write a cookie. Will use default options set in class. otherwise
	 * cookie parameters can be defined. Objects and arrays passed as values
	 * will be serialized.
	 * 
	 * @param string $name Key for the value to be written has a cookie
	 * @param string $value The value to be stored in a cookie.
	 * @param array $options Options that can change how the cookie is stored.
	 * 		  The options passed will override the default options pased in the init
	 * 			-'cookie_path' _string_: The path where the cookie is to be stored
	 * 			-'cookie_domain' _string_: The domain the that the cookie resides on
	 * 			-'cookie_secure' _boolean_:
	 * 			-'cookie_httponly' _boolean_:
	 * 			-'cookie_lifetime' _int_: The amount of time the cookie is active for
	 * 			-'hash_cookie' _boolean_ :Hash the cookie to its value is not easily readable
	 * 
	 * @return void
	 */
	public static function writeCookie($name, $value, $options=array()) {
		$options += self::getCookieDefaults();
		extract($options);
		
		if(is_array($value) || is_object($value)) {
			$value=serialize($value);
		}
		
		setcookie($name, $user_id, time()+$cookie_lifetime, $cookie_path, $cookie_path, $cookie_secure, $cookie_httponly );
	}
	
	/**
	 * Read a value set in a cookie. Objects and arrays thats were
	 * serilizaed will be unserialzed and returned.
	 * 
	 * @param string name
	 * 
	 * @return mixed stored_value
	 */
	public static function readCookie($name, $options=array()) {
		$options += self::getCookieDefaults();
		
		if(!isset($_COOKIE[$name]))
			return false;
		
		$cookie_value=$_COOKIE[$name];
		
		$data = @unserialize($cookie_value);
		if($data !== false || $cookie_value === 'b:0;')
		    return $data;
		else
		   return $cookie_value;
	}
	
	public static function deleteCookie($name, $options=array()){
		$options += self::getCookieDefaults();
		extract($options);
		
		setcookie($name, $NULL, time()-4800,$cookie_path, $cookie_path, $cookie_secure, $cookie_httponly);
		unset($_COOKIE[$name]);
	}
	/**
	 * Write a cookie. Will use default options set in class. otherwise
	 * cookie parameters can be defined. Objects and arrays passed as values
	 * will be serialized.
	 * 
	 * @param string $name The key for the session
	 * @param string value The value that will be stored in the session
	 * @param array options Options that can be changed that will ovveride the
	 * 		  values passed in the init
	 * 			-'hash_session' _boolean: Has a season so its value is not easily readable
	 * 
	 * @return void
	 * @access public
	 */
	public static function writeSession($name, $value, $options=array()) {
		$options += self::getSessionDefaults();
		extract($options);
		
		if(is_array($value) || is_object($value)) {
			$value=serialize($value);
		}
		
		$_SESSION[$name]=$value;
	}
	
	/**
	 * Read a value set in a cookie. Objects and arrays thats were
	 * serilizaed will be unserialzed and returned.
	 * 
	 * @param string $name
	 * 
	 * @return mixed $stored_value
	 * @access public
	 */
	public static function readSession($name,$options=array()) {
		$options += self::getSessionDefaults();
		if(!isset($_SESSION[$name]))
			return false;
		
		$session_value=$_SESSION[$name];
		
		$data = @unserialize($session_value);
		if($data !== false || $session_value === 'b:0;')
		    return $session_value;
		else
		   return$session_value;
	}
	
	/**Remove a session
	 * 
	 * @param strirng $name Key for the session
	 * @return void
	 * @access public
	 */
	public static function deleteSession($name, $options=array()) {
		$options += self::getSessionDefaults();
		extract($options);
		
		if(isset($_SESSION[$name] )){
			unset($_SESSION[$name]);
		}
	}
	
	/**
	 * Get the cookie default options
	 * 
	 * @return array default_cookie_options
	 * @access private
	 */
	private static function getCookieDefaults() {
		$defaults=array(
			'cookie_path'=>self::$cookie_path,
			'cookie_domain'=>self::$cookie_domain,
			'cookie_secure'=>self::$cookie_secure,
			'cookie_httponly'=>self::$cookie_httponly,
			'cooke_lifetime'=>self::$cookie_lifetime,
			'hash_cookie'=>self::$hash_cookie
		);
		
		return $defaults;
	}  
	
	/**
	 * Get the session default options
	 * 
	 * @return array default_session_options
	 * @access private
	 */
	private static function getSessionDefaults() {
		$defaults=array(
			'session_path'=>self::$cookie_path,
			'session_path'=>self::$cookie_domain,
			'session_secure'=>self::$cookie_secure,
			'session_httponly'=>self::$cookie_httponly,
			'session_lifetime'=>self::$cookie_lifetime,
			'hash_session'=>self::$hash_cookie
		);
		
		return $defaults;
	} 
	
}//end class
	
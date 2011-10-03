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
	
	/**
	 * Initializes the static class PVSessions. Values passed can be
	 * used to set the default cookie options and the default session
	 * options.
	 * 
	 * @param array session_vars
	 * 
	 * @return void
	 */
	public static function init($session_vars=array()) {
		
		$defaults=array(
			'cookie_path'=>'/',
			'cookie_domain'=>$_SERVER['HTTP_HOST'],
			'cookie_secure'=>false,
			'cookie_httponly'=>false,
			'cooke_lifetime'=>5000
		);
		
		if(empty($session_vars))
			$session_vars=PVConfiguration::getSiteSessionConfiguration();
		
		$session_vars += $defaults;
		
		self::$cookie_path=$session_vars['cookie_path'];
		self::$cookie_domain=$session_vars['cookie_domain'];
		self::$cookie_secure=$session_vars['cookie_secure'];
		self::$cookie_httponly=$session_vars['cookie_httponly'];
		self::$cookie_lifetime=$session_vars['cookie_lifetime'];
		
		self::setSessionConfig($session_vars);
		
	}
	
	/**
	 * Set the site wide session paramters at boot. $session_info is an array of variables
	 * set in the configuration file. THIS MODIFIES THE SESSION VARIABLE ONLY< NOT THE 
	 * COOKIE!
	 * 
	 * @param string session_name: Set the session name.
	 * @param int session_lifetime: The life time of the session, in seconds
	 * @param string session_path: the path of the session.
	 * @param string session_domain: The domain of the session. Default is current.
	 * @param boolean session_secure: Sets if session is secure
	 * @param boolean session_http_only: Allows a session only over http
	 */
	public static function setSessionConfig($session_info){
		$default=array(
			'session_name'=>'pv_session',
			'session_lifetime'=>2000,
			'session_path'=>'/',
			'session_domain'=>$_SERVER['HTTP_HOST'],
			'session_secure'=>false,
			'session_httponly'=>false
		);
		
		$session_info += $default;
		
		session_name($session_info['session_name']);
		session_set_cookie_params($session_info['session_lifetime'], $session_info['session_path'], $session_info['session_domain']);
		session_start();
		
	}//end setSession
	
	/**
	 * Write a cookie. Will use default options set in class. otherwise
	 * cookie parameters can be defined. Objects and arrays passed as values
	 * will be serialized.
	 * 
	 * @param string name
	 * @param string value
	 * @param array options
	 * 
	 * @return void
	 */
	public static function writeCookie($name, $value, $options=array()) {
		$defaults=self::getCookieDefaults();
		$options += $defaults;
		
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
	public static function readCookie($name) {
		
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
		$defaults=self::getCookieDefaults();
		$options += $defaults;
		
		extract($options);
		
		setcookie($name, $NULL, time()-4800,$cookie_path, $cookie_path, $cookie_secure, $cookie_httponly);
		unset($_COOKIE[$name]);
	}
	/**
	 * Write a cookie. Will use default options set in class. otherwise
	 * cookie parameters can be defined. Objects and arrays passed as values
	 * will be serialized.
	 * 
	 * @param string name
	 * @param string value
	 * @param array options
	 * 
	 * @return void
	 */
	public static function writeSession($name, $value, $options=array()) {
		$defaults=self::getCookieDefaults();
		$options += $defaults;
		
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
	public static function readSession($name) {
		
		if(!isset($_COOKIE[$name]))
			return false;
		
		$cookie_value=$_COOKIE[$name];
		
		$data = @unserialize($cookie_value);
		if($data !== false || $cookie_value === 'b:0;')
		    return $data;
		else
		   return $cookie_value;
	}
	
	public static function deleteSession($name) {
		if(isset($_SESSION[$name] )){
			unset($_SESSION[$name]);
		}
	}
	
	/**
	 * Get the cookie default options
	 * 
	 * @return array default_cookie_options
	 */
	private static function getCookieDefaults() {
		$defaults=array(
			'cookie_path'=>self::$cookie_path,
			'cookie_domain'=>self::$cookie_domain,
			'cookie_secure'=>self::$cookie_secure,
			'cookie_httponly'=>self::$cookie_httponly,
			'cooke_lifetime'=>self::$cookie_lifetime
		);
		
		return $defaults;
	}  
	
}//end class
	
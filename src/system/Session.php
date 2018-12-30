<?php

namespace prodigyview\system;

use prodigyview\design\StaticObject;

/**
 * Session is the class for handling the cookie session information related to the system.
 *
 * The class offers a variety of tools for how to set up basic session control within your
 * application. These features can be used in conjunction with other session handling methodologies.
 *
 * Example:
 * ```php
 * //Initialize the class
 * Session::init();
 *
 * //Write data to a cookie
 * Session::writeCookie('foo', 'bar');
 * echo Session::readCookie('foo');
 *
 * //Encrypt the  value
 * Session::writeCookie('foo', 'bar', array('hash_cookie' => true));
 * echo Session::readCookie('foo', array('hash_cookie' => true);
 * ```
 *
 * @package system
 */
class Session {
	
	use StaticObject;

	/**
	 * The lifetime of the cookie
	 */
	private static $cookie_lifetime = 5000;

	/**
	 * The cookie path
	 */
	private static $cookie_path = '/';

	/**
	 * The cookie domain
	 */
	private static $cookie_domain = '';

	/**
	 * Access the cookie only over an secure connection
	 */
	private static $cookie_secure = false;

	/**
	 * Write to the cookie only over an http(s) connection
	 */
	private static $cookie_httponly = false;

	/**
	 * Encrypt the cookie
	 */
	private static $hash_cookie = false;

	/**
	 * The lifetime of the session
	 */
	private static $session_lifetime = 5000;

	/**
	 * The tmp path for the session
	 */
	private static $session_path = '/';

	/**
	 * The domain for the session
	 */
	private static $session_domain = '';

	/**
	 * Access the session only over a secure connection
	 */
	private static $session_secure = false;

	/**
	 * Writes to the session only over an http connection
	 */
	private static $session_httponly = false;

	/**
	 * Encrypt the session data
	 */
	private static $hash_session = false;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initializes the static class Session. Values passed can be
	 * used to set the default cookie options and the default session
	 * options.
	 *
	 * @param array $session_vars An array of values that set how the class functions.
	 * 			-'cookie_path' _string_: The path where the cookie is to be stored
	 * 			-'cookie_domain' _string_: The domain the that the cookie resides on
	 * 			-'cookie_secure' _boolean_: Access the cookie only over an secure connection
	 * 			-'cookie_httponly' _boolean_: Write to the cookie only over an http(s) connection
	 * 			-'cookie_lifetime' _int_: The amount of time the cookie is active for
	 * 			-'hash_cookie' _boolean_ :Hash the cookie to its value is not easily readable
	 * 			-'hash_session' _boolean: Has a season so its value is not easily readable
	 * 			-'session_name' _string_ : Name of the current session
	 * 			-'session_lifetime' _int_: The life time of the session, in seconds
	 * 			-'session_path' _string_: The path of the session.
	 * 			-'session_domain' _string_: The domain of the session. Default is current.
	 * 			-'session_secure'_boolean_: Access the session only over a secure connection
	 * 			-'session_httponly' _boolean: Writes to the session only over an http connection
	 * @return void
	 */
	public static function init($session_vars = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $session_vars);

		if(!self::$_initialized) {
			
			$defaults = array(
				'cookie_path' => '/',
				'cookie_domain' => $_SERVER['HTTP_HOST'],
				'cookie_secure' => false,
				'cookie_httponly' => false,
				'cookie_lifetime' => 5000,
				'hash_cookie' => false,
				'hash_session' => false,
				'session_name' => 'pv_session',
				'session_lifetime' => 2000,
				'session_path' => '/',
				'session_domain' => $_SERVER['HTTP_HOST'],
				'session_secure' => false,
				'session_httponly' => false,
				'session_start' => true
			);
	
			$session_vars += $defaults;
	
			$session_vars = self::_applyFilter(get_class(), __FUNCTION__, $session_vars, array('event' => 'args'));
	
			self::$cookie_path = $session_vars['cookie_path'];
			self::$cookie_domain = $session_vars['cookie_domain'];
			self::$cookie_secure = $session_vars['cookie_secure'];
			self::$cookie_httponly = $session_vars['cookie_httponly'];
			self::$cookie_lifetime = $session_vars['cookie_lifetime'];
	
			self::$session_path = $session_vars['session_path'];
			self::$session_domain = $session_vars['session_domain'];
			self::$session_secure = $session_vars['session_secure'];
			self::$session_httponly = $session_vars['session_httponly'];
			self::$session_lifetime = $session_vars['session_lifetime'];
	
			self::$hash_cookie = $session_vars['hash_cookie'];
			self::$hash_session = $session_vars['hash_session'];
	
			if (session_status() == PHP_SESSION_NONE) {
				session_name($session_vars['session_name']);
				session_set_cookie_params($session_vars['session_lifetime'], $session_vars['session_path'], $session_vars['session_domain'], $session_vars['session_secure'], $session_vars['session_httponly']);
			}
	
			if ($session_vars['session_start'] && session_status() == PHP_SESSION_NONE) {
				session_start();
			}
	
			self::_notify(get_class() . '::' . __FUNCTION__, $session_vars);
			
			self::$_initialized = true;
		}
	}

	/**
	 * Write a cookie. Will use default options set in the init. Otherwise
	 * cookie parameters can be defined. Objects and arrays passed as values
	 * will be serialized.
	 *
	 * @param string $name Key for the value to be written as a cookie
	 * @param string $value The value to be stored in a cookie.
	 * @param array $options Options that can change how the cookie is stored.
	 * 		  The options passed will override the default options passed in the init
	 * 			-'cookie_path' _string_: The path where the cookie is to be stored
	 * 			-'cookie_domain' _string_: The domain the that the cookie resides on
	 * 			-'cookie_secure' _boolean_: If the cookie is only writable over a secure connection
	 * 			-'cookie_httponly' _boolean_: If the cookie is only accesible over an http connection
	 * 			-'cookie_lifetime' _int_: The amount of time the cookie is active for in seconds
	 * 			-'hash_cookie' _boolean_ :Hash the cookie key and its value is not easily readable
	 *
	 * @return void
	 */
	public static function writeCookie(string $name, $value, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $value, $options);

		$options += self::getCookieDefaults();
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'value' => $value,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$value = $filtered['value'];
		$options = $filtered['options'];

		extract($options);

		if (is_array($value) || is_object($value)) {
			$value = serialize($value);
		}

		if ($options['hash_cookie']) {
			$name = Security::encrypt($name);
			$value = Security::encrypt($value);
		}
		
		setcookie($name, $value, time() + $cookie_lifetime, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);
		
		self::_notify(get_class() . '::' . __FUNCTION__, $name, $value, $options);
	}

	/**
	 * Read a value set in a cookie. Objects and arrays thats were
	 * serilizaed will be unserialzed and returned.
	 *
	 * @param string $name The key the cookie was saved as
	 * @param array $options Options thats configure reading the cookie
	 * 			-hash_cookie _boolean_: If the cookie was hashed, set the value to true
	 *
	 * @return mixed $value The value retrieved from the cookie. Arrays and objects serialized will be
	 * unseralized and returned.
	 * @access public
	 */
	public static function readCookie(string $name, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

		$options += self::getCookieDefaults();
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		if ($options['hash_cookie'])
			$name = Security::encrypt($name);

		if (!isset($_COOKIE[$name]))
			return false;

		$cookie_value = $_COOKIE[$name];

		if ($options['hash_cookie'])
			$cookie_value = Security::decrypt($cookie_value);

		$data = @unserialize($cookie_value);
		if ($data !== false || $cookie_value === 'b:0;')
			$cookie_value = $data;

		self::_notify(get_class() . '::' . __FUNCTION__, $cookie_value, $name, $options);
		$cookie_value = self::_applyFilter(get_class(), __FUNCTION__, $cookie_value, array('event' => 'return'));

		return $cookie_value;
	}

	/**
	 * Removes a cookie from the system,
	 *
	 * @param string $name The name/key of the current cookie
	 * @param array $options Options than can be defined to customize the cookie deletion process
	 * 			-'hash_session' _boolean: If the cookie is hashed, use this to delete it.
	 * 			-'cookie_path' _string_: The path where the cookie is to be stored
	 * 			-'cookie_domain' _string_: The domain the that the cookie resides on
	 * 			-'cookie_secure' _boolean_:
	 * 			-'cookie_httponly' _boolean_:
	 */
	public static function deleteCookie(string $name, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

		$options += self::getCookieDefaults();
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		extract($options);

		if ($options['hash_cookie'])
			$name = Security::encrypt($name);

		setcookie($name, NULL, time() - 4800, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);
		unset($_COOKIE[$name]);
		self::_notify(get_class() . '::' . __FUNCTION__, $name, $options);
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
	 * 			-'hash_session' _boolean: Hash a session so its value is not easily readable
	 *
	 * @return void
	 * @access public
	 */
	public static function writeSession(string $name, $value, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $value, $options);

		$options += self::getSessionDefaults();
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'value' => $value,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$value = $filtered['value'];
		$options = $filtered['options'];

		extract($options);

		if (is_array($value) || is_object($value)) {
			$value = serialize($value);
		}

		if ($options['hash_session']) {
			$name = Security::encrypt($name);
			$value = Security::encrypt($value);
		}

		$_SESSION[$name] = $value;
		self::_notify(get_class() . '::' . __FUNCTION__, $name, $value, $options);
	}

	/**
	 * Read a value set in a cookie. Objects and arrays thats were
	 * serilizaed will be unserialzed and returned.
	 *
	 * @param string $name The key to access the session variable
	 * @param array $options Options to define how the information is acccessed
	 * 				-'hash_session' _boolean: Hash a session so its value is not easily readable
	 *
	 * @return mixed $stored_value
	 * @access public
	 */
	public static function readSession(string $name, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

		$options += self::getSessionDefaults();
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		if ($options['hash_session'])
			$name = Security::encrypt($name);

		if (!isset($_SESSION[$name]))
			return false;

		$session_value = $_SESSION[$name];

		if ($options['hash_session'])
			$session_value = Security::decrypt($session_value);

		$data = @unserialize($session_value);

		if ($data !== false || $session_value === 'b:0;')
			$session_value = $data;

		self::_notify(get_class() . '::' . __FUNCTION__, $session_value, $name, $options);
		$session_value = self::_applyFilter(get_class(), __FUNCTION__, $session_value, array('event' => 'return'));

		return $session_value;
	}

	/**
	 * Remove a session
	 *
	 * @param string $name Key for the session
	 * @param array $options Options used for deleting the key
	 * 				-'hash_session' _boolean: Hash a session so its value is not easily readable
	 * @return void
	 * @access public
	 */
	public static function deleteSession(string $name, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

		$options += self::getSessionDefaults();
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		extract($options);

		if ($options['hash_session'])
			$name = Security::encrypt($name);

		if (isset($_SESSION[$name])) {
			unset($_SESSION[$name]);
			self::_notify(get_class() . '::' . __FUNCTION__, $name, $options);
		}
	}

	/**
	 * Get the cookie default options
	 *
	 * @return array default_cookie_options
	 * @access private
	 */
	private static function getCookieDefaults() {

		$defaults = array(
			'cookie_path' => self::$cookie_path,
			'cookie_domain' => self::$cookie_domain,
			'cookie_secure' => self::$cookie_secure,
			'cookie_httponly' => self::$cookie_httponly,
			'cookie_lifetime' => self::$cookie_lifetime,
			'hash_cookie' => self::$hash_cookie
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

		$defaults = array(
			'session_path' => self::$session_path,
			'session_path' => self::$session_domain,
			'session_secure' => self::$session_secure,
			'session_httponly' => self::$session_httponly,
			'session_lifetime' => self::$session_lifetime,
			'hash_session' => self::$hash_session
		);

		return $defaults;
	}

}//end class

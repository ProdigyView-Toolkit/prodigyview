<?php

if (!defined('MCRYPT_DES')) {
	define('MCRYPT_DES', null);
}
/**
 * PVSecurity is a class designed to handle the security of your application ranging from encryption
 * to hashing.
 *
 * @TODO rework classs
 * @package system
 */
class PVSecurity extends PVStaticObject {

	private static $cipher;
	private static $mcrypt_algorithm;
	private static $mcrypt_algorithm_directory;
	private static $mcrypt_mode;
	private static $mcrypt_mode_directory;
	private static $mcrypt_key;
	private static $mcrypt_iv;

	protected static $_salt = null;
	protected static $_auth_table = 'users';
	protected static $_auth_hashed_fields = array();
	protected static $_auth_encrypted_fields = array();
	protected static $_save_cookie = true;
	protected static $_save_session = true;
	protected static $_cookie_fields = array();
	protected static $_session_fields = array();

	/**
	 * Initializes the security class for using encryption and for authentication. Requires that
	 * the package mcrypt be installed.
	 *
	 * @param array $args An array of arguments to be passed into the security class.
	 * 			-'mcrypt_algorithm' _string_ : The algorthim to be used for encruption. MCRYPT_DES is default
	 * 			-'mcrypt_algorithm_directory' _string_: The directory the algorithm
	 * 			-'mcrypt_mode' _string_ : The mode to set for mcrypt. Defaults of 'ofb'
	 * 			-'mcrypt_key' _string_: The default key that will be used for encryption
	 * 			-'mcrypt_iv' _string_: The iv the will be used for encryption
	 * 			-'salt' _string_: The default value that will be applied as a salt when hashing
	 * 			-'auth_table' _string_: The table name that will perform authorization of a user. Default name
	 * is users
	 * 			-'auth_hashed_fields' _array_: An array of fields that will be hashed on authentication
	 * 			-'auth_encrypted_fields' _array_: An array of fields that will be encryped on authentication
	 * 			-'save_cookie' _boolean_: Enable the saving of variables to a cookie on save
	 * 			-'save_session' _boolean_: Enable the saving the variables to a session on authentication
	 * 			-'cookie_fields' _array_: An array of fields pulled from the auth table that will be saved to
	 * the cookie on authentication
	 * 			-'session_fields' _array_: An array of fields pulled from the auth table that will be saved to
	 * the session on authentication
	 *
	 * @return void
	 * @access public
	 */
	public static function init($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$defaults = array(
			'mcrypt_algorithm' => MCRYPT_DES,
			'mcrypt_algorithm_directory' => '',
			'mcrypt_mode' => 'ofb',
			'mcrypt_mode_directory' => '',
			'mcrypt_key' => 'prodgiyviewkey',
			'mcrypt_iv' => 'prodgiyviewiv',
			'salt' => null,
			'auth_table' => 'users',
			'auth_hashed_fields' => array(),
			'auth_encrypted_fields' => array(),
			'save_cookie' => true,
			'save_session' => true,
			'cookie_fields' => array(),
			'session_fields' => array()
		);

		$args += $defaults;

		self::$mcrypt_algorithm = $args['mcrypt_algorithm'];
		self::$mcrypt_algorithm_directory = $args['mcrypt_algorithm_directory'];
		self::$mcrypt_mode = $args['mcrypt_mode'];
		self::$mcrypt_mode_directory = $args['mcrypt_mode_directory'];
		self::$mcrypt_key = $args['mcrypt_key'];
		self::$mcrypt_iv = $args['mcrypt_iv'];

		self::$_salt = $args['salt'];
		self::$_auth_table = $args['auth_table'];
		self::$_auth_hashed_fields = $args['auth_hashed_fields'];
		self::$_auth_encrypted_fields = $args['auth_encrypted_fields'];
		self::$_save_cookie = $args['save_cookie'];
		self::$_save_session = $args['save_session'];
		self::$_cookie_fields = $args['cookie_fields'];
		self::$_session_fields = $args['session_fields'];
	}

	/**
	 * Encrypts a string of data and returns the encrypted string.
	 *
	 * @param string $string The string to be encrypted
	 * @param array $options An array of options to configure the encryption
	 *
	 * @return string $encrypted_string Returns an encryped string of data
	 * @access public
	 */
	public static function encrypt($string, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $option);

		$options += self::_getEncryptDefaults();

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'string' => $string,
			'options' => $options
		), array('event' => 'args'));
		$string = $filtered['string'];
		$options = $filtered['options'];

		if (self::$cipher == null || $options['recreate_cipher'])
			self::$cipher = mcrypt_module_open($options['mcrypt_algorithm'], $options['mcrypt_algorithm_directory'], $options['mcrypt_mode'], $options['mcrypt_mode_directory']);

		$iv = self::_checkIv($options['mcrypt_iv']);
		$key = self::_checkKey($options['mcrypt_key']);

		mcrypt_generic_init(self::$cipher, $key, $iv);
		$encrypted_data = mcrypt_generic(self::$cipher, $string);
		mcrypt_generic_deinit(self::$cipher);

		self::_notify(get_class() . '::' . __FUNCTION__, $encrypted_data, $string, $options);
		$encrypted_data = self::_applyFilter(get_class(), __FUNCTION__, $encrypted_data, array('event' => 'return'));

		return $encrypted_data;
	}

	/**
	 * Decrypts a string of data.
	 *
	 * @param string $data The string to be decrypted
	 * @param array $options An array of options that defines how to perform the encryption
	 *
	 * @return string $decrypted_string The string decrypted
	 * @access public
	 */
	public function decrypt($string, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $options);

		$options += self::_getEncryptDefaults();

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'string' => $string,
			'options' => $options
		), array('event' => 'args'));
		$string = $filtered['string'];
		$options = $filtered['options'];

		if (self::$cipher == null || $options['recreate_cipher'])
			self::$cipher = mcrypt_module_open($options['mcrypt_algorithm'], $options['mcrypt_algorithm_directory'], $options['mcrypt_mode'], $options['mcrypt_mode_directory']);

		$iv = self::_checkIv($options['mcrypt_iv']);
		$key = self::_checkKey($options['mcrypt_key']);

		mcrypt_generic_init(self::$cipher, $key, $iv);
		$decrypted_data = mdecrypt_generic(self::$cipher, $string);
		mcrypt_generic_deinit(self::$cipher);

		self::_notify(get_class() . '::' . __FUNCTION__, $decrypted_data, $string, $options);
		$decrypted_data = self::_applyFilter(get_class(), __FUNCTION__, $decrypted_data, array('event' => 'return'));

		return $decrypted_data;
	}

	protected static function _checkIv($iv) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $iv);

		$iv = self::_applyFilter(get_class(), __FUNCTION__, $iv, array('event' => 'args'));

		$ivSize = mcrypt_enc_get_iv_size(self::$cipher);
		if (strlen($iv) > $ivSize)
			$iv = substr($iv, 0, $ivSize);

		self::_notify(get_class() . '::' . __FUNCTION__, $iv);
		$iv = self::_applyFilter(get_class(), __FUNCTION__, $iv, array('event' => 'return'));

		return ($iv);
	}

	protected static function _checkKey($key) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key);

		$key = self::_applyFilter(get_class(), __FUNCTION__, $key, array('event' => 'args'));

		$keySize = mcrypt_enc_get_key_size(self::$cipher);
		if (strlen($key) > $keySize)
			$key = substr($key, 0, $keySize);

		self::_notify(get_class() . '::' . __FUNCTION__, $key);
		$key = self::_applyFilter(get_class(), __FUNCTION__, $key, array('event' => 'return'));

		return ($key);
	}

	/**
	 * Returns the default arguements for encryptions. The arguements returned are initial
	 * set in the init.
	 *
	 * @return array $configuration Returns the configuration in an array
	 * @access protected
	 */
	protected static function _getEncryptDefaults() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$defaults = array(
			'mcrypt_algorithm' => self::$mcrypt_algorithm,
			'mcrypt_algorithm_directory' => self::$mcrypt_algorithm_directory,
			'mcrypt_mode' => self::$mcrypt_mode,
			'mcrypt_mode_directory' => self::$mcrypt_mode_directory,
			'recreate_cipher' => false,
			'mcrypt_key' => self::$mcrypt_key,
			'mcrypt_iv' => self::$mcrypt_iv
		);

		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));

		return $defaults;
	}

	/**
	 * Checks to the if the credentials passed match the credentials
	 * stored in the database.
	 *
	 * @param array $fields An array of fields that will be checked against the fields in the database
	 * table
	 * @param array $options An array of options
	 * 			-'auth_table' _string_: The table name to be checked against
	 * 			-'auth_hashed_fields' array: An array of fields that must be hashed before checking
	 * 			-'auth_encrypted_fields' array: An array of fields that must be encrypted before checking
	 * 			-'format_table' _boolean_: Will formated the table with any prefixes or schemas. Default is
	 * false.
	 * 			-'save_cookie' _boolean_: If authenticated save data into cookie. Default is true.
	 * 			-'save_session' _boolean_: If authenticated, save data into session. Default is true
	 * 			-'cookie_fields' _array_: The fields that will be saved into the cookie
	 * 			-'session_fields' _array_: The fields that will be saved into the session
	 *
	 * @return mixed If authenticated, the return will be the row in the database. Otherwise false.
	 * @access public
	 */
	public static function checkAuth($fields, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $fields, $options);

		$defaults = array(
			'auth_table' => self::$_auth_table,
			'auth_hashed_fields' => self::$_auth_hashed_fields,
			'auth_encrypted_fields' => self::$_auth_encrypted_fields,
			'format_table' => false,
			'save_cookie' => self::$_save_cookie,
			'save_session' => self::$_save_cookie,
			'cookie_fields' => self::$_cookie_fields,
			'session_fields' => self::$_session_fields,
			'salt' => self::$_salt
		);

		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'fields' => $fields,
			'options' => $options
		), array('event' => 'args'));
		$fields = $filtered['fields'];
		$options = $filtered['options'];

		foreach ($fields as $key => $value) {
			if (in_array($key, $options['auth_hashed_fields'])) {
				$fields[$key] = self::hash($value, $options['salt']);
			}

			if (in_array($key, $options['auth_encrypted_fields'])) {

				$fields[$key] = self::encrypt($value);
			}
		}//end foreach

		$args = array(
			'where' => $fields,
			'table' => ($options['format_table']) ? PVDatabase::formatTableName($options['auth_table']) : $options['auth_table'],
		);

		$result = PVDatabase::selectStatement($args, array('findOne' => true));

		$row = PVDatabase::fetchArray($result);

		if (!empty($row) && ($options['save_cookie'] || $options['save_session'])) {
			foreach ($row as $key => $value) {
				if (!is_numeric($key) && $options['save_cookie'] && in_array($key, $options['cookie_fields']))
					PVSession::writeCookie($key, $value);
				if (!is_numeric($key) && $options['save_session'] && in_array($key, $options['session_fields']))
					PVSession::writeSession($key, $value);
			}
		}

		$return = (!empty($row)) ? $row : false;

		self::_notify(get_class() . '::' . __FUNCTION__, $return, $fields, $options);
		$return = self::_applyFilter(get_class(), __FUNCTION__, $return, array('event' => 'return'));

		return $return;
	}

	/**
	 * Performas a one way hash on a string with an optional salt
	 * value.
	 *
	 * @param string $string The string to be hashed
	 * @param string $salt A salt to add to the hash
	 *
	 * @return string $hashed_string Returns the hashed string
	 * @access public
	 */
	public static function hash($string, $salt = null) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $salt);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'string' => $string,
			'salt' => $salt
		), array('event' => 'args'));
		$string = $filtered['string'];
		$salt = $filtered['salt'];

		//$hashed_string = crypt( $string, $salt ?: self::$_salt );
		$hashed_string = password_hash($string, PASSWORD_BCRYPT, array('cost' => 10));

		self::_notify(get_class() . '::' . __FUNCTION__, $hashed_string, $string, $salt);
		$hashed_string = self::_applyFilter(get_class(), __FUNCTION__, $hashed_string, array('event' => 'return'));

		return $hashed_string;
	}

}//end class

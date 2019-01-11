<?php

namespace prodigyview\system;

use prodigyview\design\StaticObject;
use prodigyview\util\Conversions;

/**
 * The Configuration acts a global registry for system-wide configuration options for the
 * application.
 *
 * The configuration class is most notably used for setting variables that can be retrieved anywhere
 * in your system with the setters and getters. There is also the option of setting different
 * environment.
 *
 * Example:
 * 
 * ```php
 * //Init the class
 * Configuration::init();
 *
 * //Add An Example Configuration
 * $data = array(
 * 	'host'=>'localhost',
 * 	'database' => 'test',
 * 	'user'=>'admin',
 * 	'password'=>'abc123'
 * );
 * Configuration::addConfiguration('mysql',  $data);
 *
 * //Retrieve and use that configuration
 * $mysql = Configuration::getConfiguration('mysql');
 * echo $mysql->host;
 *
 * //Set different configs for different environments
 * Configuration::addConfiguration('mysql',  $data, array('environment' => 'production'));
 * ```
 *
 * @package system
 */
class Configuration {
	
	use StaticObject;

	/**
	 * The environment, ie production, staging, development, etc.
	 */
	protected static $_environment = '';

	/**
	 * Configuration options that have been set.
	 */
	protected static $_configurations = '';
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initializes the configuration class by adding values to the collection
	 * available in the static parent object. Because the variable is added statically,
	 * the information will be available anywhere on the site.
	 *
	 * @param array $args Arguements to be added to the configuration
	 *
	 * @return void
	 * @access public
	 */
	public static function init(array $args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		if(!self::$_initialized) {
			$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
	
			if (isset($args['environment'])) {
				self::$_environment = $args['environment'];
				unset($args['environment']);
			}
	
			if (!empty($args)) {
				foreach ($args as $key => $value) {
					self::addToCollectionWithName($key . '_' . self::$_environment, $value);
				}
			}
	
			self::_notify(get_class() . '::' . __FUNCTION__, $args);
			
			self::$_initialized = true;
		}
		
	}

	/**
	 * Adds a configuration to the Configuration class based
	 * upon a key and value.
	 *
	 * @param string $key The Key to be used for accessing the configuration
	 * @param string $value The string value to be stored in the configuration
	 * @param array $options Options when setting the configuratuin
	 * 			- string "environment": The environment to set the configuration for
	 *
	 * @return void
	 * @access public
	 */
	public static function addConfiguration(string $key, $value, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $value);

		$defaults = array('environment' => self::$_environment);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'value' => $value,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$value = $filtered['value'];
		$options = $filtered['options'];

		$environment = $options['environment'];

		self::addToCollectionWithName($key . '_' . $environment, $value);
		self::_notify(get_class() . '::' . __FUNCTION__, $key, $value);
	}

	/**
	 * Retrieves a stored configuration based upon the key that was
	 * assigned to it.
	 *
	 * @param string $key The key to the string stored
	 * @param array $options Options can be be passed for retrieving the content.
	 * 				- string "environment": The environment to set the configuration for
	 *
	 * @return string $configuration
	 * @access pulbic
	 */
	public static function getConfiguration(string $key, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key);

		$defaults = array('environment' => self::$_environment);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'key' => $key,
			'options' => $options
		), array('event' => 'args'));
		
		$key = $filtered['key'];
		$options = $filtered['options'];

		$environment = $options['environment'];

		$value = self::get($key . '_' . $environment);

		self::_notify(get_class() . '::' . __FUNCTION__, $key, $value);
		$value = self::_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));

		return $value;
	}
	
	/**
	 * Load a configuration option from the ENV of the server. Otherwise a default ENV will be used.
	 * 
	 * @param string $value The ENV value to look for
	 * @param string $default a default value to return
	 * 
	 * @return string $env
	 */
	public static function env(string $value, string $default = '') {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $value, $default);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'value' => $value,
			'default' => $default
		), array('event' => 'args'));
		
		$value = $filtered['value'];
		$default = $filtered['default'];
		
		$env_value = getenv($value);
		
		if($env_value === false) {
			$env_value = $default;
		}
		
		$env_value = self::_applyFilter(get_class(), __FUNCTION__, $env_value, array('event' => 'return'));
		
		return $env_value;
	}

	/**
	 * Outside of the standardrd xml file reading, a custom xml configuration
	 * can be set in the xml file and read when needed.
	 *
	 * @param string $node_name The parent node in the xml file in which all children with be read rom.
	 *
	 * @return void mixed $config Any infomration retrieved from that node
	 * @access public
	 */
	public static function loadXMLConfiguration(string $file_location, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file_location);

		$file_location = self::_applyFilter(get_class(), __FUNCTION__, $file_location, array('event' => 'args'));

		$xml = simplexml_load_file($file_location);
		
		$data = Conversions::convertXmlToArray($xml);
		
		foreach($data as $key => $value) {
			self::addConfiguration($key, $value);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $node_name, $parameter_array);
		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'return'));

		return $data;
	}

	
	

}//end class

<?php

namespace prodigyview\system;

use prodigyview\design\StaticObject;

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
	public static function init($args = array()) {

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
	public static function addConfiguration($key, $value, $options = array()) {

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
	public static function getConfiguration($key, $options = array()) {

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

		$value = parent::get($key . '_' . $environment);

		self::_notify(get_class() . '::' . __FUNCTION__, $key, $value);
		$value = self::_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));

		return $value;
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
	public static function loadXMLConfigurationFromFile($node_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $node_name);

		$node_name = self::_applyFilter(get_class(), __FUNCTION__, $node_name, array('event' => 'args'));

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName($node_name);

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $node_name, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * the elements betweeen the <email></email> tags.
	 *
	 * @return array email_options: Returns the email options in an array
	 * @access public
	 */
	public static function getSiteEmailConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName('email');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * the elements betweeen the <sessions></sessions> tags.
	 *
	 * @return array $session_options Returns the session options in an array
	 * @access public
	 */
	public static function getSiteSessionConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName('sessions');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * all the xml in the file.
	 *
	 * @return array options Returns all the options in an array
	 * @access public
	 */
	public static function getSiteCompleteConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$parameter_array = array();
		if (defined('PV_CONFIG') && file_exists(PV_CONFIG)) {
			$filename = PV_CONFIG;

			$doc = new DOMDocument();
			$doc->formatOutput = true;
			$doc->preserveWhiteSpace = false;
			$doc->load($filename);
			$node_array = $doc->getElementsByTagName('general');

			foreach ($node_array as $node) {
				if ($node->childNodes->length) {
					foreach ($node->childNodes as $i) {
						$parameter_array[$i->nodeName] = $i->nodeValue;
					}//end foreach
				}//end if
			}//end foreach

			$node_array = $doc->getElementsByTagName('email');

			foreach ($node_array as $node) {
				if ($node->childNodes->length) {
					foreach ($node->childNodes as $i) {
						$parameter_array[$i->nodeName] = $i->nodeValue;
						self::addToCollectionWithName($i->nodeName, $i->nodeValue);
					}//end foreach
				}//end if

			}//end foreach

			$node_array = $doc->getElementsByTagName('system');

			foreach ($node_array as $node) {
				if ($node->childNodes->length) {
					foreach ($node->childNodes as $i) {
						$parameter_array[$i->nodeName] = $i->nodeValue;
						self::addToCollectionWithName($i->nodeName, $i->nodeValue);
					}//end foreach
				}//end if

			}//end foreach

			$node_array = $doc->getElementsByTagName('libraries');

			foreach ($node_array as $node) {
				if ($node->childNodes->length) {
					foreach ($node->childNodes as $i) {
						$parameter_array[$i->nodeName] = $i->nodeValue;
						self::addToCollectionWithName($i->nodeName, $i->nodeValue);
					}//end foreach
				}//end if
			}//end foreach

			$node_array = $doc->getElementsByTagName('sessions');

			foreach ($node_array as $node) {
				if ($node->childNodes->length) {
					foreach ($node->childNodes as $i) {
						$parameter_array[$i->nodeName] = $i->nodeValue;
						self::addToCollectionWithName($i->nodeName, $i->nodeValue);
					}//end foreach
				}//end if
			}
		}
		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * the one elements betweeen the <general></general> and <email></email> tags.
	 *
	 * @return array $general_options Returns the general options in an array
	 * @access public
	 */
	public static function getSiteGeneralConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName('general');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		$node_array = $doc->getElementsByTagName('email');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * the one elements betweeen the <system></system> tags.
	 *
	 * @return array $system_options Returns the system options in an array
	 * @access public
	 */
	public static function getSystemConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName('system');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end systemConfiguration

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * the one elements betweeen the <general></general> tags.
	 *
	 * @return array site_options: Returns the site options in an array
	 * @access public
	 */
	public static function getSiteConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName('general');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

	/**
	 * Retrieve the preferences in the sites xml
	 * configuration. The configuration options retrieved will be
	 * the one elements betweeen the <server></server> tags.
	 *
	 * @return array $sever_options Returns the site server in an array
	 * @access public
	 */
	public static function getServerConfiguration() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;
		$doc->load($filename);
		$node_array = $doc->getElementsByTagName('server');

		foreach ($node_array as $node) {
			if ($node->childNodes->length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i->nodeName] = $i->nodeValue;
					self::addToCollectionWithName($i->nodeName, $i->nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

}//end class

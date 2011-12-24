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

class PVConfiguration extends PVStaticObject {
	
	protected static $_environment = '';
	protected static $_configurations = '';
	

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

		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		
		if(isset($args['environment'])) {
			self::$_environment = $args['environment'];
			unset($args['environment']);
		}
		
		if (!empty($args)) {
			foreach ($args as $key => $value) {
				self::addToCollectionWithName($key.'_'.self::$_environment , $value);
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $args);
	}

	/**
	 * Adds a configuration to the Configuration class based
	 * upon a key and value.
	 *
	 * @param string $key The Key to be used for accessing the configuration
	 * @param string $value The string value to be stored in the configuration
	 *
	 * @return void
	 * @access public
	 */
	public static function addConfiguration($key, $value, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $value);
		
		$defaults = array('environment' => self::$_environment);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('key' => $key, 'value' => $value, 'options' => $options), array('event' => 'args'));
		$key = $filtered['key'];
		$value = $filtered['value'];
		$options = $filtered['options'];
		
		$environment = $options['environment'];

		self::addToCollectionWithName($key.'_'.$environment , $value);
		self::_notify(get_class() . '::' . __FUNCTION__, $key, $value);
	}

	/**
	 * Retrieves a stored configuration based upon the key that was
	 * assigned to it.
	 *
	 * @param string $key The key to the string stored
	 *
	 * @return string $configuration
	 * @access pulbic
	 */
	public static function getConfiguration($key, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key);
		
		$defaults = array('environment' => self::$_environment);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('key' => $key, 'options' => $options), array('event' => 'args'));
		$key = $filtered['key'];
		$options = $filtered['options'];
		
		$environment = $options['environment'];

		$value = parent::get($key.'_'.$environment);

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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName($node_name);

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('email');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('sessions');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
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

		$filename = PV_CONFIG;
		$parameter_array = array();

		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('general');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
				}//end foreach
			}//end if
		}//end foreach

		$node_array = $doc -> getElementsByTagName('email');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		$node_array = $doc -> getElementsByTagName('system');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		$node_array = $doc -> getElementsByTagName('libraries');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
				}//end foreach
			}//end if
		}//end foreach

		$node_array = $doc -> getElementsByTagName('sessions');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
				}//end foreach
			}//end if
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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('general');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		$node_array = $doc -> getElementsByTagName('email');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('system');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('general');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
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
		$doc -> formatOutput = true;
		$doc -> preserveWhiteSpace = false;
		$doc -> load($filename);
		$node_array = $doc -> getElementsByTagName('server');

		foreach ($node_array as $node) {
			if ($node -> childNodes -> length) {
				foreach ($node->childNodes as $i) {
					$parameter_array[$i -> nodeName] = $i -> nodeValue;
					self::addToCollectionWithName($i -> nodeName, $i -> nodeValue);
				}//end foreach
			}//end if

		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $parameter_array);
		$parameter_array = self::_applyFilter(get_class(), __FUNCTION__, $parameter_array, array('event' => 'return'));

		return $parameter_array;
	}//end getSiteEmailConfiguration

}//end class

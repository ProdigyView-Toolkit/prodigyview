<?php
namespace prodigyview\system;

use prodigyview\design\StaticObject;
/**
 * PVBootstrap is responsible for initializing the system the system by initializing all the classes.
 *
 * This class will have to be refactored, but it was designed to give base options for creating a
 * secure environment based off of a configuration file that would be passed to it.
 *
 * @TODO rework this class and decide if still needed
 * @package system
 */
class Bootstrap  {
	
	use StaticObject;

	/**
	 * Boot the ProdigyView system. Initilize variables, set logging,
	 * sessions, etc. Many of the configuration settings are located in the xml
	 * config file but can also be set here.
	 *
	 * @param array $args Arguments to pass that affect how ProdigyView will boot
	 * 			-'initialize_database' _boolean_: Initialize the database and set the database to the default config
	 * 			-'initialize_libraries' _boolean_: Initializes PBLibraries
	 * 			-'initialize_router' _boolean_: Initializes PVRouter
	 * 			-'initialize_template' _boolean_:Initializes PVTemplate
	 * 			-'initalize_validator' _boolean_: Initializes PVValidator
	 * 			-'initalize_session' _boolean_: Initializes PVSession
	 * 			-'initalize_security' _boolean_: Initializes PVSecurity
	 * 			-'load_plugins' _boolean_: Loads the plug-ins at boot.
	 *			-'load_libraries' _boolean_: Loads the libraries that have been added
	 * 			-'load_configuration' _boolean_: Loads the xml configuration file
	 * 			-'load_database' _boolean_: Opens up a connection to the database.
	 * 			-'load_database_profile' _mixed_: Connects to the specified database that the option
	 * 'load_database' connects too.
	 *
	 * @return void
	 * @access public
	 */
	public static function bootSystem($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$defaults = array(
			'initialize_database' => true,
			'initialize_libraries' => true,
			'initialize_router' => true,
			'initialize_template' => true,
			'initialize_validator' => true,
			'initialize_security' => true,
			'initialize_session' => true,
			'initialize_cache' => true,
			'initialize_mail' => true,
			'initialize_video' => true,
			'initialize_audio' => true,
			'initialize_image' => true,
			'load_plugins' => true,
			'load_libraries' => true,
			'load_configuration' => true,
			'load_database' => true,
			'load_database_profile' => 0,
			'config' => array(
				'report_errors' => false,
				'log_errors' => true,
				'error_report_level' => E_ALL,
				'enable_cache' => true,
				'unset_cookie' => false,
				'unset_session' => false,
				'unset_post' => false,
				'unset_get' => false,
				'unset_request' => false,
				'unset_env' => false,
				'unset_files' => false,
				'unset_server' => false,
				'cache_time' => 15
			)
		);

		$args += $defaults;
		
		if ($args['load_configuration']) {
			$config = PVConfiguration::getSiteCompleteConfiguration() + $defaults['config'];
		} else {
			$config = $args['config'] + $defaults['config'];
		}

		$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));

		self::setErrorReporting($config['report_errors'], $config['log_errors'], $config['error_report_level']);

		if ($args['initialize_database'])
			PVDatabase::init($config);

		if ($args['load_database'])
			PVDatabase::setDatabase($args['load_database_profile']);

		if ($args['initialize_libraries'])
			PVLibraries::init();

		if ($args['load_libraries'])
			PVLibraries::loadLibraries();

		if ($args['initialize_template'])
			PVTemplate::init($config);

		if ($args['initialize_router'])
			PVRouter::init($config);

		if ($args['initialize_validator'])
			PVValidator::init();

		if ($args['initialize_security'])
			PVSecurity::init($config);

		if ($args['initialize_session'])
			PVSession::init($config);

		if ($args['initialize_cache'])
			PVCache::init($config);

		if ($args['initialize_mail'])
			PVMail::init($config);

		if ($args['initialize_video'])
			PVVideo::init($config);

		if ($args['initialize_audio'])
			PVAudio::init($config);

		if ($args['initialize_image'])
			PVImage::init($config);

		self::removeMagicQuotes();

		if (empty($config['default_time_zone'])) {
			date_default_timezone_set('America/New_York');
		} else {
			date_default_timezone_set($config['default_time_zone']);
		}

		if ($config['enable_cache'] == 1) {
			self::setHeaderExpires($config['cache_time']);
		} else {
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', FALSE);
			header('Pragma: no-cache');
		}

		if ($config['unset_cookie'] == 1) {
			self::unsetGlobalVariable('_COOKIE');
		}

		if ($config['unset_session'] == 1) {
			self::unsetGlobalVariable('_SESSION');
		}

		if ($config['unset_post'] == 1) {
			self::unsetGlobalVariable('_POST');
		}

		if ($config['unset_get'] == 1) {
			self::unsetGlobalVariable('_GET');
		}

		if ($config['unset_request'] == 1) {
			self::unsetGlobalVariable('_REQUEST');
		}

		if ($config['unset_env'] == 1) {
			self::unsetGlobalVariable('_ENV');
		}

		if ($config['unset_files'] == 1) {
			self::unsetGlobalVariable('_FILES');
		}

		if ($config['unset_server'] == 1) {
			self::unsetGlobalVariable('_SERVER');
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $config);
	}//end bootSystem

	/**
	 * Sets the rror errporting in ProdigyView. The levels are numberic.
	 * 0. No errors reported
	 * 1. Report major and minor errors
	 * 2. Report fatal, notices and warnings
	 * 3. Report everything except notices
	 * 4. Report everything
	 *
	 * Or the values such as E_ALL, E_ALL ^ NOTICE, etc can be passed in.
	 *
	 *In your xml configuration, look for these tags.
	 * <report_errors>1</report_errors> 1 for displaying errors, 0 for not displaying errors
	 * <log_errors>1</log_errors> 1 for loggin errors to file, 0 for not logging errors to file
	 * <error_report_level>4</error_report_level> Setting the error repporting level
	 *
	 * @param boolean $report_errors Set to true, errors will be displayed
	 * @param boolean $log_errors Set to true, errors will be log in the defined log
	 * @param mixed $$error_report_level Set the level errors will b shown
	 *
	 * @return void
	 * @access private
	 */
	private static function setErrorReporting($report_errors = FALSE, $log_errors = TRUE, $error_report_level = E_ALL) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $report_errors, $log_errors, $error_report_level);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'report_errors' => $report_errors,
			'log_errors' => $log_errors,
			'error_report_level' => $error_report_level
		), array('event' => 'args'));
		
		$report_errors = $filtered['report_errors'];
		$log_errors = $filtered['log_errors'];
		$error_report_level = $filtered['error_report_level'];

		if ($error_report_level == 0) {
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
		} else if ($error_report_level == 1) {
			error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
		} else if ($error_report_level == 2) {
			error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
		} else if ($error_report_level == 3) {
			error_reporting(E_ALL ^ E_NOTICE);
		} else if ($error_report_level == 4) {
			error_reporting(E_ALL);
		} else if (!empty($error_report_level)) {
			error_reporting($error_report_level);
		}

		if ($report_errors) {
			ini_set('display_errors', 'On');
		} else {
			ini_set('display_errors', 'Off');
		}

		if ($log_errors) {
			ini_set('log_errors', 'On');
			ini_set('error_log', PV_ERROR_LOG);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $report_errors, $log_errors, $error_report_level);
	}//end setReporting

	/**
	 * Unsets a global at launch. Use for removing data from $_GET, $_SESSION
	 * $_POST, $_COOKIE, $_REQUEST, $_ENV.
	 *
	 * @param string $global The global variable to unset
	 *
	 * @return void
	 * @access private
	 */
	private static function unsetGlobalVariable($global) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $global);

		$global = self::_applyFilter(get_class(), __FUNCTION__, $global, array('event' => 'args'));

		foreach ($GLOBALS[$global] as $key => $var) {
			if ($var === $GLOBALS[$key]) {
				unset($GLOBALS[$key]);
			}
		}//end for

		self::_notify(get_class() . '::' . __FUNCTION__, $global);
	}//end unsetGlobalVariable

	/**
	 * Strips the slashes from an array
	 *
	 * @param array $array The array to modify
	 *
	 * @return array
	 */
	private static function stripSlashesRecursive($array) {

		$array = is_array($array) ? array_map(NULL, $array) : self::stripSlashesRecursive($array);
		return $array;
	}

	/**
	 * Magic Quoutes should be disabled on your system. But if it is on, this function
	 * will remove from any variables in the $_GET, POST, COOKIE, and $_REQUEST.
	 *
	 * @return void
	 * @access void
	 */
	private static function removeMagicQuotes() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		if (get_magic_quotes_gpc()) {
			array_walk_recursive($_GET, 'stripslashes_gpc');
			array_walk_recursive($_POST, 'stripslashes_gpc');
			array_walk_recursive($_COOKIE, 'stripslashes_gpc');
			array_walk_recursive($_REQUEST, 'stripslashes_gpc');
		}

		self::_notify(get_class() . '::' . __FUNCTION__);
	}//end

	/**
	 * At boot, set in what amount of time the header will expire in. Should be
	 * set in munutes. The configuration for this file can be changed in the xml
	 * configuration file in the <cache_time>x</cache_time> tags.
	 *
	 * @param int $expirationMinutes The amount of minutes in with the header will expire
	 *
	 * @return void
	 * @access private
	 */
	private static function setHeaderExpires($expirationMinutes) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $expirationMinutes);

		$expirationMinutes = self::_applyFilter(get_class(), __FUNCTION__, $expirationMinutes, array('event' => 'args'));

		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expirationMinutes) . 'GMT');

		self::_notify(get_class() . '::' . __FUNCTION__, $expirationMinutes);
	}//end setHeaderExpires

}//end class

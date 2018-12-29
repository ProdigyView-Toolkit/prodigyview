<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

//Define the directory seperator
if (!defined('DS')) {
	define('DS', '/');
}

/**
 * Log is used to write information to a log for record keeping.
 *
 * The logs that can be recorded is up to the developer. The class can be overridden with Adapters to
 * do things like write to syslog or external logging services.
 *
 * Example:
 * 
 * ```php
 * //Initialize The class
 * Log::init();
 *
 * //Write various logs with different priority levels
 * Log::writeLog('Warning', 'Illegal Access By User');
 * Log::writeLog('High Alert', 'System Almost Out Of Memory');
 * Log::writeLog('Low', 'Page Not Found');
 *
 * //Get the logs with a high priority level
 * $logs = Log::readLog('High Alert');
 * ```
 *
 * @package util
 */
class Log {
	
	use StaticObject;

	/**
	 * The directory the logs will be written too
	 */
	protected static $_logDirectory;

	/**
	 * The format of the timestamp for the logs
	 */
	protected static $_timestampFormat;

	/**
	 * The name of the file the logs are being saved too
	 */
	protected static $_logFile;

	/**
	 * The format for saving logs, in terms of priorty, message and date
	 */
	protected static $_logFormat;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initialize the log class and set the options for how the logger will write
	 * and read logs.
	 *
	 * @param array $config An array of configuration options
	 * 			-'directory' _string_: The directory to place the logged file. Default location is '/tmp/'
	 * folder
	 * 			-'file' _string_: The file name to save the log in. Default is a blank string. If left empty,
	 * the default
	 * 			will be the priorioty name. If the priority is 'alert', the file will be named alert.log.
	 * 			-'timestamp_format' _string_: The date/time format to save the log file as. Default is 'Y-m-d
	 * H:i:s'
	 * 			-'log_format' _string_: The format to save the log in. Values will be replaced with passed in
	 * values.
	 * 			Default values are '{priority} {timestamp} {message}\n'
	 *
	 * @return void
	 * @access public
	 */
	public static function init(array $config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);
		
		if(!self::$_initialized) {
			$defaults = array(
				'directory' => DS . 'tmp' . DS,
				'file' => '',
				'timestamp_format' => 'Y-m-d H:i:s',
				'log_format' => '{priority} {timestamp} {message}' . "\n"
			);
	
			$config += $defaults;
	
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$_logDirectory = $config['directory'];
			self::$_timestampFormat = $config['timestamp_format'];
			self::$_logFormat = $config['log_format'];
			self::$_logFile = $config['file'];
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}

	}

	/**
	 * Write a message to the log.
	 *
	 * @param string $priority The priority should be a string. Keep in mind that if no 'file' was set in
	 * the config, the priority will be the log file name
	 * @param string $message The message to write to the log
	 * @param array $options Options to configure writing to the log
	 * 			-'directory' _string_: The directory to place the logged file. Default set in the init.
	 * 			-'file' _string_: The file name to save the log in. Default is set in the init
	 * 			-'timestamp_format' _string_: The date/time format to save the log file as. Default is set in
	 * the init
	 * 			-'log_format' _string_: The format to save the log in. Default is set in the init
	 *
	 * @return boolean Returns true if the file is successfully written
	 * @access public
	 */
	public static function writeLog(string $priority, string $message, array $options = array()) : bool {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $priority, $message, $options);

		$defaults = self::_getLogDefaults($priority);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'priority' => $priority,
			'message' => $message,
			'options' => $options
		), array('event' => 'args'));
		
		$priority = $filtered['priority'];
		$message = $filtered['message'];
		$options = $filtered['options'];

		$log_time = date($options['timestamp_format']);

		$log_message = str_replace('{priority}', $priority, $options['log_format']);
		$log_message = str_replace('{timestamp}', $log_time, $log_message);
		$log_message = str_replace('{message}', $message, $log_message);

		$written = FileManager::writeFile($options['directory'] . $options['file'], $log_message, 'a');

		self::_notify(get_class() . '::' . __FUNCTION__, $written, $priority, $message, $options);
		$written = self::_applyFilter(get_class(), __FUNCTION__, $written, array('event' => 'return'));

		return $written;
	}

	/**
	 * Read a log file.
	 *
	 * @param string $priority The priority to be read
	 * @param array $options Options that can configure what file to read
	 * 			-'directory' _string_: The directory to place the logged file. Default set in the init.
	 * 			-'file' _string_: The file name to save the log in. Default is set in the init
	 *
	 * @return string $log Returns the log if readable and exist. Otherwise returns false
	 * @access public
	 */
	public static function readLog(string $priority, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $priority, $options);

		$defaults = self::_getLogDefaults($priority);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'priority' => $priority,
			'options' => $options
		), array('event' => 'args'));
		
		$priority = $filtered['priority'];
		$options = $filtered['options'];

		$log = false;

		if (is_readable($options['directory'] . $options['file']))
			$log = FileManager::readFile($options['directory'] . $options['file']);

		self::_notify(get_class() . '::' . __FUNCTION__, $log, $priority, $options);
		$log = self::_applyFilter(get_class(), __FUNCTION__, $log, array('event' => 'return'));

		return $log;
	}

	/**
	 * Get the defaults set in the initalization that configure the class.
	 *
	 * @param string $priority The priority to use. Default is null
	 *
	 * @return array $defaults
	 * @access protected
	 */
	protected static function _getLogDefaults($priority = null) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $priority);

		$priority = self::_applyFilter(get_class(), __FUNCTION__, $priority, array('event' => 'args'));

		$defaults = array(
			'directory' => self::$_logDirectory,
			'file' => (self::$_logFile) ? : $priority . '.log',
			'timestamp_format' => self::$_timestampFormat,
			'log_format' => self::$_logFormat
		);

		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));

		return $defaults;
	}

}

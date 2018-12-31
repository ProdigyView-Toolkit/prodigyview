<?php
namespace prodigyview\system;

use prodigyview\util\FileManager;
use prodigyview\design\StaticObject;

//Define the directory seperator
if (!defined('DS')) {
	define('DS', '/');
}

if (!defined('PV_LIBRARIES')) {
	define('PV_LIBRARIES', '');
}


/**
 * Libraries is designed to load external libraries into the system, especially those that are not
 * in a management service like Composer.
 *
 * While tools like composer make including and accessing libraries easy, not every library is on the
 * service nor does every project want to manage their 3rd parties libraries in the same way.
 * Libraries primary focus is the loading of external libraries to be used in your application.
 *
 * Example:
 * 
 * ```php
 * //Initialize the class
 * Libraries::init();
 *
 * //Add an external library
 * Libraries::addLibrary('MailLoader', array('path' => '/absolute/path/to/library/1',
 * 'explicit_load' => true));
 *
 * //Add a library with name spaces
 * Libraries::addLibrary('Facebook', array('path' => '/absolute/path/to/library/2', 'namespaced' =>
 * true));
 *
 * //To your application to load these libraries for use
 * Libraries::loadLibraries();
 * ```
 *
 * @package system
 */
class Libraries {

	use StaticObject;
	
	/**
	 * Javascript libraries
	 */
	private static $javascript_libraries_array;

	/**
	 * CSS files to load
	 */
	private static $css_files_array;

	/**
	 * Javascript that is not a file
	 */
	private static $open_javascript;

	/**
	 * PHP libraries
	 */
	private static $libraries;

	/**
	 * An array of classes to autoload
	 */
	private static $_autoloadClasses;

	/**
	 * Signals if namespace is on by default for all classes
	 */
	private static $_namespaced;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initialize the library class in preparotion for loading libraries. Needs to be configured if
	 * namespaces
	 * are going to be used.
	 *
	 * @param array $config A configuration that can be used for setting how the class works
	 * 			-'namespaced' _boolean_: Default is false. If set to true, classes will be treated and react as
	 * if they are namespaced
	 *
	 * @return void
	 * @access public
	 */
	public static function init($config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$defaults = array('namespaced' => false);
	
			$config += $defaults;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$javascript_libraries_array = array();
			self::$css_files_array = array();
			self::$libraries = array();
			self::$_autoloadClasses = array();
			self::$_namespaced = $config['namespaced'];
	
			spl_autoload_register(array(
				'prodigyview\system\Libraries',
				'_autoload'
			));
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			self::$_initialized = true;
		}
	}

	/**
	 * Adds javascript files to a queue of javascript files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing
	 * the script and the location of the script.
	 *
	 * @return void
	 * @access public
	 */
	public static function enqueueJavascript($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		
		self::$javascript_libraries_array[$script] = $script;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds css files to a queue of css files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 *
	 * @param string $script The name of script to be added. The name of script acts as key for accessing
	 * the script and the location of the script.
	 *
	 * @return void
	 * @access public
	 */
	public static function enqueueCss($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		
		self::$css_files_array[$script] = $script;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Adds a script directly into a queue to be outputted later.The script should be inputted with
	 * opening
	 * and closing tags as it would appear when the output occurs
	 *
	 *
	 * @param string $script The string to be added to a queue. The string does not have a key and cannot
	 * be removed once added.
	 *
	 * @return void
	 * @access public
	 */
	public static function enqueueOpenscript($script) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $script);

		$script = self::_applyFilter(get_class(), __FUNCTION__, $script, array('event' => 'args'));
		
		self::$open_javascript .= $script;
		self::_notify(get_class() . '::' . __FUNCTION__, $script);
	}

	/**
	 * Returns javascript file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should
	 * present the location of the script
	 * @access public
	 */
	public static function getJavascriptQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$javascript_libraries_array);
		
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$javascript_libraries_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns css file locations that have been inserted
	 * into the queue.
	 *
	 * @return array $script Returns an array of scripts. The key => value are the same and should
	 * present the location of the script
	 * @access public
	 */
	public static function getCssQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$css_files_array);
		
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$css_files_array, array('event' => 'return'));

		return $script;
	}

	/**
	 * Returns the open scripts that were previously added to the open script queue.
	 *
	 * @return string $script The scripts added will be returned in one unified string
	 * @access public
	 */
	public static function getOpenscriptQueue() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_notify(get_class() . '::' . __FUNCTION__, self::$open_javascript);
		
		$script = self::_applyFilter(get_class(), __FUNCTION__, self::$open_javascript, array('event' => 'return'));

		return $script;
	}

	/**
	 * Add a library that will be auto loaded when loadLibraries is called. The libraries
	 * added will be available through the class.
	 *
	 * @param folder_name The name of folder that contains the library. By default the folder should be
	 * in the PV_Libraries
	 * 		  DEFINE location. Also acts as the library name when being referenced
	 * @param array $options Options than can be used to configure the library that will be loaded
	 * 			-'path' _string_: The path to the library. The default path is PV_LIBRARIES.$folder_name.DS
	 * 			-'auto_load' _boolean_: When true, library will become part of the spl_autoload. Default is
	 * true. Other the library will not be auto_loaded
	 * 			-'explicit_load' _boolean_: Default is false. If set to false
	 * 			-'extensions' _array_: An array of allowed file extensions that will be included when the
	 * library loads. Default is .php
	 *
	 * @return void
	 * @access public
	 */
	public static function addLibrary($folder_name, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $folder_name, $options);

		$defaults = array(
			'path' => PV_LIBRARIES . $folder_name . DS,
			'auto_load' => true,
			'explicit_load' => false,
			'extensions' => array('.php')
		);

		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'folder_name' => $folder_name,
			'options' => $options
		), array('event' => 'args'));
		
		$folder_name = $filtered['folder_name'];
		$options = $filtered['options'];

		self::$libraries[$folder_name] = $options;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $folder_name, $options);
	}

	/**
	 * Looks through any libraries that have been added through addLibrary function. If there ae
	 * libraries
	 * and their autoload is set to true, the library's file and folders will be included and made
	 * accessible.
	 *
	 * @return void
	 * @access public
	 */
	public static function loadLibraries() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		self::_buildAutoLoads();

		if (!empty(self::$libraries)) {

			foreach (self::$libraries as $key => $value) {
				if ($value['explicit_load']) {
					$library = FileManager::getFilesInDirectory($value['path'], array('verbose' => true));
					self::_loadLibrary($library, $value['extensions']);
				}
			}//end foreach
		}

		self::_notify(get_class() . '::' . __FUNCTION__);
	}//end loadLibraries

	/**
	 * Explicity loads a specfic library, even if autoload is set to false. If the library is already
	 * loaded, the files that have already
	 * been included WILL NOT be re-included.
	 *
	 * @param string $library_name The name of the library to be load. Will be the same name passed when
	 * addLibrary was used.
	 *
	 * @return void
	 * @access public
	 */
	public static function loadLibrary($library_name) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $library_name);

		$library_name = self::_applyFilter(get_class(), __FUNCTION__, $library_name, array('event' => 'args'));

		if (isset(self::$libraries[$library_name])) {
			$library = FileManager::getFilesInDirectory(self::$libraries[$library_name]['path'], array('verbose' => true));

			if (self::$libraries[$library_name]['auto_load'])
				self::_buildAutoLoads();

			if (self::$libraries[$library_name]['explicit_load'])
				self::_loadLibrary($library, self::$libraries[$library_name]['extensions']);

			self::_notify(get_class() . '::' . __FUNCTION__, $library_name);
		}//end loadLibrary

	}//end

	/**
	 * Loads the library that is passed through. Uses include_once when including a file.
	 *
	 * @param array $library An array of the library that contains directores, files, and file
	 * information
	 * @param array $allow_extensions The allowed extensions
	 *
	 * @return void
	 * @access protected
	 */
	protected static function _loadLibrary($library, $allow_extensions) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $library, $allow_extensions);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'library' => $library,
			'allow_extensions' => $allow_extensions
		), array('event' => 'args'));
		
		$library = $filtered['library'];
		$allow_extensions = $filtered['allow_extensions'];

		foreach ($library as $key => $value) {
			if ($value['type'] === 'folder') {
				self::_loadLibrary($value['files'], $allow_extensions);
			} else {
				if (empty($allow_extensions)) {
					include_once ($key);
				} else {
					$extensions_allowed = (is_array($allow_extensions)) ? implode($allow_extensions, '|') : $allow_extensions;

					if (preg_match('/' . $extensions_allowed . '/', $value['basename'], $matches)) {

						$key = str_replace('\\', '/', $key);
						include_once ($key);
					}
				}
			}//end else
		}//end foreach

		self::_notify(get_class() . '::' . __FUNCTION__, $library, $allow_extensions);
	}//end _loadLibrary

	/**
	 * Build an array of the classes to autoload through spl_autoload if thec classes are not
	 * automatically included.
	 *
	 * @return void
	 * @access public
	 * @todo Find a faster method for autloading
	 */
	protected static function _buildAutoLoads() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		foreach (self::$libraries as $library) {

			if ($library['auto_load']) {
				
				$allow_extensions = $library['extensions'];

				$directory_iterator = new \RecursiveDirectoryIterator($library['path']);
				$iterator_iterator = new \RecursiveIteratorIterator($directory_iterator, \RecursiveIteratorIterator::SELF_FIRST);
				
				foreach ($iterator_iterator as $file) {
					
					$extensions_allowed = (is_array($allow_extensions)) ? implode($allow_extensions, '|') : $allow_extensions;

					if (false === strpos($file->getFilename(), '~') && 0 < strpos($file->getFilename(), '.php') && preg_match('/' . $extensions_allowed . '/', $file->getBasename(), $matches)) {

						if (self::$_namespaced || (isset($library['namespaced']) && $library['namespaced'])) {
							if (isset($library['path'])) {
								$namespace = basename($library['path']) . DS . str_replace($library['path'], '', $file->getPathname());
							} else {
								$namespace = str_replace(PV_LIBRARIES, '', $file->getPathname());
							}
							
							$namespace = str_replace($matches[0], '', $namespace);
							self::$_autoloadClasses[$namespace] = $file->getPathname();
							
						} else {
							self::$_autoloadClasses[$file->getBasename($matches[0])] = $file->getPathname();
						}
					}
				}//end inter foreach
			}//end if autoload
		}//endforeach

	}

	/**
	 * Will attempt to autoload the classes if a class cannot be found. Works with namespaced classes
	 * also.
	 *
	 * @param $classname The name of the class to autoload
	 *
	 * @return void
	 * @access protected
	 * @todo Fix for dealing with namespaces
	 */
	protected static function _autoload($classname) {

		$classname = str_replace('\\', '/', $classname);

		if (isset(self::$_autoloadClasses[$classname]) && is_readable(self::$_autoloadClasses[$classname])) {
			include_once self::$_autoloadClasses[$classname];
		}
	}

}//end class

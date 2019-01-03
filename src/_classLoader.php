<?php

//Define the directory seperator
if (!defined('DS')) {
	define('DS', '/');
}

//Define the sites root directory
if (!defined('PV_ROOT')) {
	define('PV_ROOT', './');
}

//Define the core constant
if (!defined('PV_CORE')) {
	define('PV_CORE', dirname(__FILE__) . DS);
}

//Define the location to store audio files
if (!defined('PV_AUDIO')) {
	define('PV_AUDIO', sys_get_temp_dir());
}

//Define the location to store video files
if (!defined('PV_VIDEO')) {
	define('PV_VIDEO', sys_get_temp_dir());
}

//Define the location to store images
if (!defined('PV_IMAGE')) {
	define('PV_IMAGE', sys_get_temp_dir());
}

//Remove at somepoint when the template is done being refactored
if (!defined('PV_IS_ADMIN')) {
	define('PV_IS_ADMIN', false);
}

//Define a default root location to hold libraries
if (!defined('PV_LIBRARIES')) {
	define('PV_LIBRARIES', '');
}

//Define a location to write error logs
if (!defined('PV_ERROR_LOG')) {
	define('PV_ERROR_LOG', sys_get_temp_dir());
}

/*** nullify any existing autoloads ***/
spl_autoload_register(null, false);
/*** specify extensions that may be loaded ***/
spl_autoload_extensions('.php');

/**
 * Load the system classes
 *
 * @param string $class The name of the class to load
 */
function systemLoader($class) {

	$class = str_replace('\\', '/', $class);

	$filename = $class . '.php';
	$file = PV_CORE . DS . 'system' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/**
 * Load the template classes
 *
 * @param string $class The name of the class to load
 */
function templateLoader($class) {

	$class = str_replace('\\', '/', $class);

	$filename = $class . '.php';
	$file = PV_CORE . DS . 'template' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/**
 * Load the utility classes
 *
 * @param string $class The name of the class to load
 */
function utilLoader($class) {

	$class = str_replace('\\', '/', $class);

	$filename = $class . '.php';
	$file = PV_CORE . DS . 'util' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/**
 * Load the data classes
 *
 * @param string $class The name of the class to load
 */
function dataLoader($class) {

	$class = str_replace('\\', '/', $class);

	$filename = $class . '.php';
	$file = PV_CORE . DS . 'design' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/**
 * Load the media classes
 *
 * @param string $class The name of the class to load
 */
function mediaLoader($class) {

	$class = str_replace('\\', '/', $class);

	$filename = $class . '.php';
	$file = PV_CORE . DS . 'media' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/**
 * Load the network classes
 *
 * @param string $class The name of the class to load
 */
function networkLoader($class) {

	$class = str_replace('\\', '/', $class);

	$filename = $class . '.php';
	$file = PV_CORE . DS . 'network' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/*** register the loader functions ***/
spl_autoload_register('systemLoader');
spl_autoload_register('templateLoader');
spl_autoload_register('utilLoader');
spl_autoload_register('dataLoader');
spl_autoload_register('mediaLoader');
spl_autoload_register('networkLoader');

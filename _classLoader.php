<?php
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
	
	$class =  str_replace('\\', '/', $class);
	
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
	
	$class =  str_replace('\\', '/', $class);
	
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
	
	$class =  str_replace('\\', '/', $class);
	
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
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'data' . DS . $filename;
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
	
	$class =  str_replace('\\', '/', $class);
	
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
	
	$class =  str_replace('\\', '/', $class);
	
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

?>
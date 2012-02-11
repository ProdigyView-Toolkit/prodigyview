<?php
/*** nullify any existing autoloads ***/
spl_autoload_register(null, false);
/*** specify extensions that may be loaded ***/
spl_autoload_extensions('.php');

function systemLoader($class) {
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'system' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

function cmsLoader($class) {
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'cms' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

function componentsLoader($class) {
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'components' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

function templateLoader($class) {
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'template' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

function utilLoader($class) {
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'util' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

function dataLoader($class) {
	
	$class =  str_replace('\\', '/', $class);
	
	$filename = $class . '.php';
	$file = PV_CORE . DS . 'data' . DS . $filename;
	if (!file_exists($file)) {
		return false;
	}
	require_once $file;
}

/*** register the loader functions ***/
spl_autoload_register('systemLoader');
spl_autoload_register('cmsLoader');
spl_autoload_register('componentsLoader');
spl_autoload_register('templateLoader');
spl_autoload_register('utilLoader');
spl_autoload_register('dataLoader');

//Include the Core
require_once (PV_CORE . 'PVCore.php');
?>
<?php
namespace prodigyview\design;

/**
 * Patterns is the parent class for implementing Adapters, Observers, Intercepting Filters and
 * Singletons on static methods.
 *
 * Prodgiyview comes with  4 design patterns that can be extended to any object: Adapters, Observers,
 * Intercepting Filters and Singletons. By extending this class to any object that uses static
 * methods, they will have the capability of using these design patterns.
 *
 * @package data
 */

class StaticSingleton {

	/**
	 * Instances for singleton that have added
	 */
	protected static $_instances = array();


	/**
	 * Returns the instance of a class. Used for implementing the singleton design pattern. Class
	 * will only be instantiated once.
	 *
	 * @return object $instance Returns the instance of a class.
	 * @access public
	 */
	public static function getInstance() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		$class = get_called_class();

		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = new $class;
		}

		$object = self::$_instances[$class];
		$object = self::_applyFilter(get_class(), __FUNCTION__, $object, array('event' => 'return'));

		return $object;
	}

}

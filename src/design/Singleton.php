<?php
namespace prodigyview\design;

/**
 * Singleton is the parent class for implementing Adapters, Observers, Intercepting Filters and
 * Singletons on instances.
 *
 * Prodgiyview comes with  4 design patterns that can be extended to any object: Adapters, Observers,
 * Intercepting Filters and Singletons. By extending this class to any object that can be
 * instantiated, they will have the capability of using these design patterns.
 *
 * @package data
 */

trait Singleton {

	/**
	 * Get stores instance f singletons
	 */
	protected static $_instances = array();

	/**
	 * Returns the instance of a class. Used for implementing the singleton design pattern. Class
	 * will only be instantiated once.
	 *
	 * @return object $instance Returns the instance of a class.
	 * @access public
	 */
	public static function getInstance($data = null) {

		$class = get_called_class();

		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = new $class($data);
		}

		$object = self::$_instances[$class];

		return $object;
	}

}

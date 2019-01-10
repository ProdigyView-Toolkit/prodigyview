<?php
namespace prodigyview\design;

use prodigyview\util\Collection;
use prodigyview\util\Log;

/**
 * StaticObject is an extendable class used to enhance an object with static methods.
 *
 * ProdigyView comes with the ability to implement adapters, intercepting filters and observers.
 * Extending this class to a child class will give the child class the ability to use those design
 * patterns along with a collection that can assign and retrieve values using magic functions.
 * 
 * ```php
 * Example:
 *
 * //Create the class
 * class Example extends Object {
 *   	public static function testMe($string) {
 * 			echo $string;
 *
 * 			//An observer
 * 			$this->_notify(get_class() . '::' . __FUNCTION__, $string);
 * 		}
 * 	}
 *
 * //Add to its collection
 * Example::set('foo','bar');
 * echo Example::get('foo');
 *
 * //Add a dynamic method
 * Example::addMethod('fiz', function($text) {
 * 		return 'fiz ' . $text;
 * });
 *
 * echo Example::fizz('Bop');
 *
 *
 * //Add Observer
 * Example::addObserver('Example::testMe', 'test_closure', function($string) {
 *   	echo "\nLine 2 \n"
 * 		echo $string;
 *
 * }, array('type' => 'closure'));
 *
 * //Will call test me and the observer attached
 * Example::testMe('Testing String ');
 * ```
 *
 * @package data
 */

trait StaticObject {
	
	/**
	 * Set traits to use the same base class to avoid conflicts
	 */
	use StaticAdapter, StaticFilter, StaticObserver {
        	StaticAdapter::_invokeMethod insteadof StaticFilter, StaticObserver;
		StaticAdapter::_invokeStaticMethod insteadof StaticFilter, StaticObserver;
		StaticAdapter::_prepareLogData insteadof StaticFilter, StaticObserver;
    }

	/**
	 * A collection of items that belong to this class
	 */
	protected static $_collection = null;

	/**
	 * A collection of dynamically added methods that below to this class
	 */
	protected static $_methods = array();

	/**
	 * Adds a value to the classes Collection. By default the collection is stored
	 * in the public collection. The stored instance can be retrieved later by called
	 * in it's key value.
	 *
	 * @param string $index The key or index to store the value at
	 * @param mixed $value A mixed value that can be anytype
	 *
	 * @return void
	 * @access public
	 */
	public static function set($index, $value) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $index, $value);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'index' => $index,
			'value' => $value
		), array('event' => 'args'));
		
		$index = $filtered['index'];
		$value = $filtered['value'];

		if (self::$_collection[get_called_class()] === null) {
			self::$_collection[get_called_class()] = new Collection();
		}

		self::$_collection[get_called_class()]->addWithName($index, $value);
		self::_notify(get_class() . '::' . __FUNCTION__, $index, $value);
	}

	/**
	 * Retrieves a value that is in the public data collection or was pass through
	 * by the set method.
	 *
	 * @param string $index The index to retrieve a value from
	 *
	 * @return mixed $data The data that was stored at that index
	 * @access public
	 */
	public static function get($index) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $index);

		$index = self::_applyFilter(get_class(), __FUNCTION__, $index, array('event' => 'args'));

		if (self::$_collection[get_called_class()] === null) {
			self::$_collection[get_called_class()] = new Collection();
		}

		$value = self::$_collection[get_called_class()]->$index;

		self::_notify(get_class() . '::' . __FUNCTION__, $value, $index);
		$value = self::_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));

		return $value;
	}

	/**
	 * Uses the magic method __call and executes a closure/anonymous function that has been added
	 * to the classes $_methods using the addMethod()  method.
	 *
	 * @param string $method The key/name assigned to the method when added
	 * @param mixed $args Arguements to pass to the annoymous function. The function is called using
	 * 				call_user_func_array.
	 *
	 * @return mixed $value The value returned is the value the stored function returns
	 * @access public
	 */
	public static function __callStatic($method, $args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $method, $args);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'method' => $method,
			'args' => $args
		), array('event' => 'args'));
		
		$method = $filtered['method'];
		$args = $filtered['args'];

		if (isset(self::$_methods[get_called_class()][$method]))
			$value = call_user_func_array(self::$_methods[get_called_class()][$method], $args);
		else
			throw new BadMethodCallException('Method \'' . $method . '\' was not found in class ' . get_called_class());

		self::_notify(get_class() . '::' . __FUNCTION__, $value, $method, $args);
		$value = self::_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));

		return $value;
	}

	/**
	 * Adds a data to the public collection, index will be assigned automatically. Primarily used for
	 * adding
	 * launch quanties of data to the collection
	 *
	 * @param mixed $data Any data type( Object, Array, int, etc) to add to the public data collection
	 *
	 * @return void
	 * @access public
	 */
	public static function addToCollection($data) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data);

		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'args'));
		
		if (self::$_collection[get_called_class()] === null) {
			self::$_collection[get_called_class()] = new Collection();
		}
		
		self::$_collection[get_called_class()]->add($data);
		self::_notify(get_class() . '::' . __FUNCTION__, $data);
	}//end

	/**
	 * Adds data to the public collection. The data is assigned a key/index. If the key/index is already
	 * set, new information
	 * will override the old one.
	 *
	 * @param string $name The key/index to assign the value to
	 * @param mixed $data Data to be stored in the collection
	 *
	 * @return void
	 * @access public
	 * @todo check the relevance of get and set
	 */
	public static function addToCollectionWithName($name, $data) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $data);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'data' => $data
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$data = $filtered['data'];

		if (self::$_collection[get_called_class()] === null) {
			self::$_collection[get_called_class()] = new Collection();
		}
		
		self::$_collection[get_called_class()]->addWithName($name, $data);
		self::_notify(get_class() . '::' . __FUNCTION__, $name, $data);
	}//end

	/**
	 * Returns the iterator for iterating through the values stored in the classes collection.
	 *
	 * @return Iterator $iterator The classes collection in an iteratable form
	 * #access public
	 */
	public static function getIterator() {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);

		if (self::$_collection[get_called_class()] === null) {
			self::$_collection[get_called_class()] = new Collection();
		}
		return self::$_collection[get_called_class()]->getIterator();
	}

	/**
	 * Adds a closure/anonymous function to the object that can be called.
	 *
	 * @param string $method The key/value that will be used to call the function
	 * @param function $closure The anonymous function/closure to be added
	 *
	 * @return void
	 * @access public
	 */
	public static function addMethod($method, $closure) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $method, $closure);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'method' => $method,
			'closure' => $closure
		), array('event' => 'args'));
		
		$method = $filtered['method'];
		$closure = $filtered['closure'];

		self::$_methods[get_called_class()][$method] = $closure;
		self::_notify(get_class() . '::' . __FUNCTION__, $method, $closure);
	}

}//end class

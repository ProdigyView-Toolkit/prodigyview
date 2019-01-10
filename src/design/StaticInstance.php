<?php
namespace prodigyview\design;

use prodigyview\util\Collection;

/**
 *StaticInstance is a hybrid class that combines that usage of static design patterns of Adatper, Obsver and Filter
 * in an the instance of object.
 */
trait StaticInstance {
	
	use StaticAdapter, StaticObserver, StaticFilter {
        	StaticAdapter::_invokeMethod insteadof StaticFilter, StaticObserver;
		StaticAdapter::_invokeStaticMethod insteadof StaticFilter, StaticObserver;
		StaticAdapter::_prepareLogData insteadof StaticFilter, StaticObserver;
    }

	/**
	 * A collection of items that belong to this class
	 */
	protected $_collection = null;
	
	/**
	 * A collection of dynamically added methods that below to this class
	 */
	protected $_methods = array();

	/**
	 * Adds a value to the classes Collection. By default the collection is stored
	 * in the public collection. The stored instance can be retrieved later by called
	 * in it's key value.
	 *
	 * @param string $index The key or index to store the value at
	 * @param mixed $value A mixed value that can be any type
	 *
	 * @return void
	 * @access public
	 */
	public function __set($index, $value) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $index, $value);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('index' => $index, 'value' => $value), array('event' => 'args'));
		$index = $filtered['index'];
		$value = $filtered['value'];
		
		if ($this -> _collection === null) {
			$this -> _collection = new Collection();
		}
		$this -> _collection -> addWithName($index, $value);
		$this->_notify(get_class() . '::' . __FUNCTION__, $index, $value);
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
	public function __get($index) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $index);
		
		$index = $this->_applyFilter(get_class(), __FUNCTION__, $index, array('event' => 'args'));
		
		if ($this -> _collection === null) {
			$this -> _collection = new Collection();
		}
		
		$value = $this -> _collection -> get($index);
		
		$this->_notify(get_class() . '::' . __FUNCTION__, $value, $index);
		$value = $this->_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));
		
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
	public function __call($method,$args = array()) {
  			
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $method, $args);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('method' => $method, 'args' => $args), array('event' => 'args'));
		$method = $filtered['method'];
		$args = $filtered['args'];
		
  		if(isset($this->_methods[$method]))
  			$value = call_user_func_array($this->_methods[$method] , $args);
		else 
			throw new BadMethodCallException('Method \''.$method. '\' was not found in class '.get_called_class());
		
		$this->_notify(get_class() . '::' . __FUNCTION__, $value, $method, $args);
		$value = $this->_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));
		
		return $value;
	}

	/**
	 * Adds a data to the public collection, index will be assigned automatically. Primarily used for adding
	 * launch quanties of data to the collection
	 *
	 * @param mixed $data Any data type( Object, Array, int, etc) to add to the public data collection
	 *
	 * @return void
	 * @access public
	 */
	public function addToCollection($data) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $data);
		
		$data = $this->_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'args'));
		
		if ($this -> _collection === null) {
			$this -> _collection = new Collection();
		}
		$this -> _collection -> add($data);
		$this->_notify(get_class() . '::' . __FUNCTION__, $data);
	}//end

	/**
	 * Adds data to the public collection. The data is assigned a key/index. If the key/index is already set, new information
	 * will override the old one.
	 *
	 * @param string $name The key/index to assign the value to
	 * @param mixed $data Data to be stored in the collection
	 * 
	 * @return void
	 * @access public
	 * @todo check the relevance of get and set
	 */
	public function addToCollectionWithName($name, $data) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $name, $data);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'data' => $data), array('event' => 'args'));
		$name = $filtered['name'];
		$data = $filtered['data'];
		
		if ($this -> _collection === null) {
			$this -> _collection = new Collection();
		}
		$this -> _collection -> addWithName($name, $data);
		$this->_notify(get_class() . '::' . __FUNCTION__, $name, $data);
	}//end

	/**
	 * Returns the iterator for iterating through the values stored in the classes collection.
	 * 
	 * @return Iterator $iterator The classes collection in an iteratable form
	 * #access public
	 */
	public function getIterator() {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__);
		
		if ($this -> _collection === null) {
			$this -> _collection = new Collection();
		}
		return $this -> _collection -> getIterator();
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
	public function addMethod($method, $closure) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $method, $closure);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('method' => $method, 'closure' => $closure), array('event' => 'args'));
		$method = $filtered['method'];
		$closure = $filtered['closure'];
		
		$this->_methods[$method]=$closure;
		$this->_notify(get_class() . '::' . __FUNCTION__, $method, $closure);
	}
	
}//end class

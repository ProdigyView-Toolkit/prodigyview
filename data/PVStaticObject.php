<?php
/*
 *Copyright 2011 ProdigyView LLC. All rights reserved.
 *
 *Redistribution and use in source and binary forms, with or without modification, are
 *permitted provided that the following conditions are met:
 *
 *   1. Redistributions of source code must retain the above copyright notice, this list of
 *      conditions and the following disclaimer.
 *
 *   2. Redistributions in binary form must reproduce the above copyright notice, this list
 *      of conditions and the following disclaimer in the documentation and/or other materials
 *      provided with the distribution.
 *
 *THIS SOFTWARE IS PROVIDED BY My-Lan AS IS'' AND ANY EXPRESS OR IMPLIED
 *WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 *FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL My-Lan OR
 *CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 *ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 *ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *The views and conclusions contained in the software and documentation are those of the
 *authors and should not be interpreted as representing official policies, either expressed
 *or implied, of ProdigyView LLC.
 */

class PVStaticObject extends PVStaticPatterns {

	protected static $_collection = null;
	
	protected static $_methods = array();

	/**
	 * Adds a value to the classes Collection. By default the collection is stored
	 * in the public instance data. The stored instance can be retrieved later by called
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
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('index' => $index, 'value' => $value), array('event' => 'args'));
		$index = $filtered['index'];
		$value = $filtered['value'];
		
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}

		self::$_collection -> addWithName($index, $value);
		self::_notify(get_class() . '::' . __FUNCTION__, $index, $value);
	}

	/**
	 * Retrieves a value that is in the public data collection or was pass through
	 * by the set method.
	 *
	 * @param string $index The index to retrieve a value from
	 *
	 * @return mixed $data The data that was stored in that index
	 */
	public static function get($index) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $index);
		
		$index = self::_applyFilter(get_class(), __FUNCTION__, $index, array('event' => 'args'));
		
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		
		$value = self::$_collection -> $index;
		
		self::_notify(get_class() . '::' . __FUNCTION__, $value, $index);
		$value = self::_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));
		
		return $value;
	}

	/**
	 * Uses the magic method __call and calls a closure/annoymous function that has been added
	 * to the classes $_methods using the addMethod()  method.
	 * 
	 * @param string method The key/name assigned to the method when added
	 * @param mixed $args Arguements to pass to the annoymous function. The function is called using
	 * 				call_user_func_array.
	 * 
	 * @return mixed $value The value returned is the value the function retuens
	 * @access public
	 */
	public static function __callStatic($method,$args = array()) {
  			
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $method, $args);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('method' => $method, 'args' => $args), array('event' => 'args'));
		$method = $filtered['method'];
		$args = $filtered['args'];
		
  		if(isset(self::$_methods[$method]))
  			$value = call_user_func_array(self::$_methods[$method] , $args);
		else 
			throw new BadMethodCallException('Method \''.$method. '\' was not found in class '.get_called_class());
		
		self::_notify(get_class() . '::' . __FUNCTION__, $value, $method, $args);
		$value = self::_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));
		
		return $value;
	}
	
	/**
	 * Adds a data to the public collection, index will be assigned. Primary used for adding
	 * launch quanties of data to the collection,
	 *
	 * @param mixed $data Any information type( Object, Array, etc) to add to the public data collection
	 *
	 * @return void
	 * @access public
	 */
	public static function addToCollection($data) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data);
		
		$data = self::_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'args'));
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		self::$_collection -> add($data);
		self::_notify(get_class() . '::' . __FUNCTION__, $data);
	}//end

	/**
	 * Adds data to the public collection.
	 * @see seet
	 *
	 * @todo check the relevance of get and set
	 */
	public static function addToCollectionWithName($name, $data) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $data);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'data' => $data), array('event' => 'args'));
		$name = $filtered['name'];
		$data = $filtered['data'];
		
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		self::$_collection -> addWithName($name, $data);
		self::_notify(get_class() . '::' . __FUNCTION__, $name, $data);
	}//end

	public function getIterator() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
		
		if (self::$_collection == null) {
			self::$_collection = new PVCollection();
		}
		return self::$_collection -> getIterator();
	}
	
	/**
	 * Adds a closure/annoymous function the object that can be called.
	 * 
	 * @param string $method The key/value the function will be called by
	 * @param function $closure The annymous function/closure to be added
	 * 
	 * @return void
	 * @access public
	 */
	public function addMethod($method, $closure) {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $method, $closure);
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('method' => $method, 'closure' => $closure), array('event' => 'args'));
		$method = $filtered['method'];
		$closure = $filtered['closure'];
		
		self::$_methods[$method]=$closure;
		self::_notify(get_class() . '::' . __FUNCTION__, $method, $closure);
	}

	protected static function _getSqlSearchDefaults() {
		
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__);
			
		$defaults = array(
			'custom_where' => '', 
			'limit' => '', 
			'order_by' => '', 
			'custom_join' => '', 
			'custom_select' => '', 
			'distinct' => '', 
			'group_by' => '', 
			'having' => '', 
			'join_users' => false, 
			'prequery' => '', 
			'current_page' => '', 
			'results_per_page' => '', 
			'paged' => '', 
			'prefix_args' => '', 
			'join_user_roles' => false, 
			'join_content' => false, 
			'join_content' => false, 
			'join_comments' => false, 
			'join_applications' => false, 
			'join_apps' => false, 
			'join_pages' => false, 
			'join_modules' => false, 
			'join_containers' => false
		);
		
		$defaults = self::_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));

		return $defaults;
	}

}//end class

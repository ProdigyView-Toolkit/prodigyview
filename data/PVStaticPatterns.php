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

class PVStaticPatterns {

	protected static $_adapters = array();

	protected static $_observers = array();
	
	protected static $_instances = array();

	protected static $_filters = array();

	/**
	 * Adapters allows completely override the method of another class by calling a different class
	 * with the same function name.
	 *
	 * @param string $trigger_class  The class that contains the function the adapter will respond too
	 * @param string $trigger_method The method called that will have the adapter to be called.
	 * @param string $call_call The new class to be called that has the same method name
	 * @param array $options An array of options that be called
	 * 			-'object' _string_ : Assumes that default method in the class to be called is static. If called
	 * 			needs to be instantiated, change to instance and one will be created before the adapter calld the function
	 * 			-'call_method' _string_: By default the method to be called to override the current one should be the
	 * 			same name. But this can be ovveridden to call a different method.
	 * 			-'type' _string_: The type of function being called. Default is class_method but if the function is a closure,
	 * 			set the type to be 'closure' and make the $trigger_method the closure
	 *
	 * @return void
	 * @access public
	 */
	public static function addAdapter($trigger_class, $trigger_method, $call_class, $options = array()) {
		$defaults = array(
			'object' => 'static', 
			'call_class' => $call_class, 
			'class' => $trigger_class, 
			'method' => $trigger_method, 
			'call_method' => $trigger_method,
			'type' => 'class_method'
		);
		$options += $defaults;

		self::$_adapters[$trigger_class][$trigger_method] = $options;
	}

	/**
	 * Calls an adapter for this class. The easiest way of implementing an adapter is by placing the
	 * adapter at the top of the function that it is being called in. An infinite amout of parameters
	 * can be passed to the adapter BUT the parameters should be the same as the parents.
	 *
	 * @param string $class The name of the class the adapter is in
	 * @param string $method THe name of the method the class is being called from.
	 * @param mixed $args An infiniate amout of parameters to passed to this class.
	 *
	 * @return mixed $value A value that the adapter returns
	 * @access protected
	 */
	protected static function _callAdapter($class, $method) {
		$args = func_get_args();
		array_shift($args);
		array_shift($args);

		$passable_args = array();
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		$options = self::$_adapters[$class][$method];
		
		if($options['type'] == 'closure')
			return call_user_func_array( $options['call_method'], $passable_args);
		else if ($options['object'] == 'instance')
			return self::_invokeMethod($options['call_class'], $options['call_method'], $passable_args);
		else
			return self::_invokeStaticMethod($options['call_class'], $options['call_method'], $passable_args);

	}//end _callAdapter

	/**
	 * Checks if an adapter is set for the function.
	 *
	 * @param string class The associated class the function is calling
	 * @param string $method The associated method
	 *
	 * @return boolea $hasAdapter Returns true if it has an adapter or false if it doesn not
	 * @access protected
	 */
	protected static function _hasAdapter($class, $method) {
		if (isset(self::$_adapters[$class][$method])) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Adds an observer to the class. Observer events can fired in any method
	 * to trigger a response.
	 *
	 * @param string $event The name of the event that will cause a certain class and method to fire
	 * @param string $class The name of the class that contains the function that will be fired for this event
	 * @param string $method The name of the method that will be fired when the event occurs
	 * @param array $options Options to further the define the firing of an event
	 * 			-'object' _string_ : If the method being called is static, should be set to static. Else set to instance
	 * 			-'class' _stinrg_ : The name of the class to be called. Default is the class that is passed in.
	 * 			-'method' _string_: The name of the method to be called. Default is the method that is passed in.
	 * 			-'type' _string_: The type of function being called. Default is class_method but if the function is a closure,
	 * 			set the type to be 'closure' and make the $method the closure
	 *
	 * @return void
	 * @access public
	 */
	public static function addObserver($event, $class, $method, $options = array()) {
		$default = array(
			'object' => 'static', 
			'class' => $class, 
			'method' => $method,
			'type' => 'class_method'
		);

		$options += $default;
		self::$_observers[$event][] = $options;
	}//end _addObersver

	/**
	 * Calls any functions that have been added to the observer if the event is present in the
	 * observers array.
	 *
	 * @param string $event The name of the even that occured that will trigger notifies
	 * @param mixed $args An array of infinite arguements that will passed to each function related to the event
	 *
	 * @return void
	 * @access protected
	 */
	protected static function _notify($event) {
		$args = func_get_args();
		array_shift($args);

		$passable_args = array();
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		if (isset(self::$_observers[$event])) {
			foreach (self::$_observers[$event] as $options) {
				
				if($options['type'] == 'closure') 
					call_user_func_array( $options['method'], $passable_args);
				else if ($options['object'] == 'instance')
					self::_invokeMethod($options['class'], $options['method'], $passable_args);
				else
					self::_invokeStaticMethod($options['class'], $options['method'], $passable_args);
			}//end for each
		}

	}//end _notify

	/**
	 * Adds a filter to the class. Filters are for modifying a value within a class and should not
	 * interpet the normal flow within the method.
	 *
	 * @param string $class The name of the class the filter is going in
	 * @param string $method The name of the method the filter is in
	 * @param string $filter_class The class that the filter resides in.
	 * @param string $filter_method The method in the class that the parameters will be passed too.
	 * @param array $options Options that can be set for further modifying the filter.
	 * 			-'object' _string_: If the method being called is static, static should be inserted. If its in an instance, 'instance' should be set.
	 * 			Default is set to static.
	 * 			-'event' _string_: Associate this filter with an event.
	 * 			-'type' _string_: The type of function being called. Default is class_method but if the function is a closure,
	 * 			set the type to be 'closure' and make the $filter_method the closure
	 *
	 * @return void
	 * @access public
	 */
	public static function addFilter($class, $method, $filter_class, $filter_method, $options = array()) {
		$defaults = array(
			'object' => 'static', 
			'class' => $filter_class, 
			'method' => $filter_method, 
			'event' => null,
			'type' => 'class_method'
		);

		$options += $defaults;

		if (!isset(self::$_filters[$class][$method])) {
			self::$_filters[$class][$method] = array();
		}

		array_push(self::$_filters[$class][$method], $options);

	}//end _addFilter

	/**
	 * Apply a fitler if filter is set.
	 *
	 * @param string $class The name of the class the filter is in
	 * @param string $method The method the filter is in
	 * @param mixed $data The data that is being passed to the filter
	 * @param array $options options to be passed to the filter. Passed options we be passed to the function.
	 * 			-'default_return' _mixed_: If no filter is return, the data passed in by default will be return. Can be overriden
	 * 			-'event' _string_: An event to associate with the filter. Default is null
	 *
	 * @return mixed $data The data the function returns
	 * @access protected
	 */
	protected static function _applyFilter($class, $method, $data, $options = array()) {
		$defaults = array('default_return' => $data, 'event' => null);
		$options += $defaults;

		if (!isset(self::$_filters[$class][$method])) {
			return $options['default_return'];
		}

		$passable_args = array($data, $options);

		foreach (self::$_filters[$class][$method] as $function) {
			
			if($function['type'] == 'closure' && $function['event'] == $options['event']) {
				$passable_args[0] = call_user_func_array( $function['method'], $passable_args);
			} else if ($function['event'] == $options['event']) {
				if ($function['object'] == 'instance')
					$passable_args[0] = self::_invokeMethod($function['class'], $function['method'], $passable_args);
				else
					$passable_args[0] = self::_invokeStaticMethod($function['class'], $function['method'], $passable_args);
			}
		}
		return $passable_args[0];
	}

	/**
	 * Checks if a filter has been set.
	 *
	 * @param string $class The class the filter is in
	 * @param string $method The method of the class that the filter is in
	 */
	protected static function _hasFilter($class, $method) {
		if (isset(self::$_filters[$class][$method]))
			return TRUE;
		return false;
	}
	
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

	/**
	 * Calls a methods that is an instance of an class. This method is generally
	 * faster than using user_call_func_array.
	 *
	 * @param string $class The name of the class to be called
	 * @param string $method The name of the method in the class to be called
	 * @param array $args An array of arguements. Arguements have to be embedded in an array to be called.
	 *
	 * @return mixed $data Data returned by the function called
	 * @access protected
	 */
	protected static function _invokeMethod($class, $method, $args) {
		if (!is_object($class))
			$class = new $class();

		switch(count($args)) :
			case 0 :
				return $class -> {$method}();
				break;
			case 1 :
				return $class -> {$method}($args[0]);
				break;
			case 2 :
				return $class -> {$method}($args[0], $args[1]);
				break;
			case 3 :
				return $class -> {$method}($args[0], $args[1], $args[2]);
				break;
			case 4 :
				return $class -> {$method}($args[0], $args[1], $args[2], $args[3]);
				break;
			case 5 :
				return $class -> {$method}($args[0], $args[1], $args[2], $args[3], $args[4]);
				break;
			default :
				return call_user_func_array(array($class, $method), $args);
				break;
		endswitch;

	}//end _invokeMethod

	/**
	 * Calls a methods that is a static method of a class. This method is generally
	 * faster than using user_call_func_array.
	 *
	 * @param string $class The name of the class to be called
	 * @param string $method The name of the method in the class to be called
	 * @param array $args An array of arguements. Arguements have to be embedded in an array to be called.
	 *
	 * @return mixed $data Data returned by the function called
	 * @access protected
	 */
	protected static function _invokeStaticMethod($class, $method, $args) {
		switch(count($args)) :
			case 0 :
				return $class::$method();
				break;
			case 1 :
				return $class::$method($args[0]);
				break;
			case 2 :
				return $class::$method($args[0], $args[1]);
				break;
			case 3 :
				return $class::$method($args[0], $args[1], $args[2]);
				break;
			case 4 :
				return $class::$method($args[0], $args[1], $args[2], $args[3]);
				break;
			case 5 :
				return $class::$method($args[0], $args[1], $args[2], $args[3], $args[4]);
				break;
			default :
				return call_user_func_array(array($class, $method), $args);
				break;
		endswitch;

	}//end _invokeMethod

}

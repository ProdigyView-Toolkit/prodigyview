<?php
/**
 * PVPatterns is the parent class for implementing Adapters, Observers, Intercepting Filters and Singletons on static methods.
 * 
 * Prodgiyview comes with  4 design patterns that can be extended to any object: Adapters, Observers, Intercepting Filters and Singletons. By extending this class to any object that uses static methods, they will have the capability of using these design patterns.
 * 
 * @package data
 */

class PVStaticPatterns {

	/**
	 * The adapters that have been added
	 */
	protected static $_adapters = array();

	/**
	 * Observers that have been added
	 */
	protected static $_observers = array();
	
	/**
	 * Instances for singleton that have added
	 */
	protected static $_instances = array();

	/**
	 * Intercepting filters that have been added
	 */
	protected static $_filters = array();
	
	/**
	 * Boolean for following and logging adapters that have been added
	 */
	private static $_traceAdapters = false;
	
	/**
	 * Boolean for following and logging filters that have been added
	 */
	private static $_traceFilters = false;
	
	/**
	 * Boolean for following and logging observers that have been added.
	 */
	private static $_traceObservers = false;
	

	/**
	 * Adapters allows a method to be completely overwritten by calling a different class
	 * with the same method name. Adapters can also be used with closures. The adapter uses
	 * a strategy/adapter design pattern.
	 *
	 * @param string $trigger_class  The class that contains the function the adapter will respond too
	 * @param string $trigger_method The method called that will have the adapter to be called.
	 * @param string $call_class The new class to be called that has the same method name
	 * @param array $options An array of options that be called
	 * 			-'object' _string_ : Assumes that default method in the class to be called is static. If called object
	 * 			needs to be instantiated, change to object to 'instance' and one will be created before the adapter calls the function
	 * 			-'call_method' _string_: By default the method to be called to override the current one should be the
	 * 			same name. But this can be ovveridden to call a different method.
	 * 			-'type' _string_: The type of method being called. Default is class_method but if the method is a closure,
	 * 			set the type to be 'closure' and make the $trigger_method the closure
	 *
	 * @return void
	 * @access public
	 * @todo add ability to adapt singleton class
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
	 * Will add an adapter for every method in the trigger_class to another class. The method will only be adapted to another class
	 * if the method in the trigger class has an adapter. This functionality can be very similiar to DI.
	 * 
	 * @param mixed $trigger_class This can either be the name of the class or an object whose methods will be adapted to another class.
	 * 		  The class should be included or be autoloaded by this point.
	 * @param string $call_class The call class is the classes methods that will be called in place of the methods in the trigger_class.
	 * 		  These class does not have to be included as this point.
	 * @param array $options Options that be used to further distinguish the behavior of the adapters added
	 * 			-'object' _string_: Determines if the object being adapted to is static or an instance.Default is static
	 * 			-'call_class' _string_: The name of the class that the methods will be adapted too.
	 * 			-'class' _string_: The name of the whose methods will be adapted to another class
	 * 
	 * @return void
	 * @access public
	 * @todo Add ability to use singleton classes
	 */
	public static function addClassAdapter($trigger_class, $call_class, $options = array()) {
		
		$defaults = array(
			'object' => 'static', 
			'call_class' => $call_class, 
			'class' => $trigger_class, 
		);
		$options += $defaults;
		
		if($options['object'] === 'instance' && !is_object($call_class))
			$call_class = new $call_class();
		
		if(class_exists($trigger_class) || is_object($trigger_class)) {
			
			$reflection = new ReflectionClass($trigger_class);
			
			foreach($reflection->getMethods() as $method) {
					
				if($method -> class == $trigger_class && $options['object'] === 'static')
					$call_class::addAdapter($trigger_class, $method -> name, $call_class, $options);
				else if($method -> class == $trigger_class && $options['object'] === 'instance')
					$call_class -> addAdapter($trigger_class, $method -> name, $call_class, $options);
				
			}//end foreach
		}
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
		
		if(self::$_traceAdapters){
			$trace = debug_backtrace();
			$options['trace'] = $trace[1]['class'].'::'.$trace[1]['function'];
			self::_logAdapter( $options );
		}
		
		if($options['type'] === 'closure')
			return call_user_func_array( $options['call_class'], $passable_args);
		else if ($options['object'] === 'instance')
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
	 * Removes an adapter.
	 *
	 * @param string class The associated class the function is calling
	 * @param string $method The associated method
	 *
	 * @return void
	 * @access public
	 */
	public static function removeAdapter($class, $method) {
		unset(self::$_adapters[$class][$method]);
	}
	
	/**
	 * Removes an adapter for an entire class.
	 *
	 * @param string class The associated class the function is calling
	 *
	 * @return void
	 * @access public
	 */
	public static function removeClassAdapter($class) {
		unset(self::$_adapters[$class]);
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
				
				if(self::$_traceObservers){
					$trace = debug_backtrace();
					$options['trace'] = $trace[1]['class'].'::'.$trace[1]['function'];
					self::_logObserver($options);
				}
								
				if($options['type'] === 'closure') 
					call_user_func_array( $options['method'], $passable_args);
				else if ($options['object'] === 'instance')
					self::_invokeMethod($options['class'], $options['method'], $passable_args);
				else
					self::_invokeStaticMethod($options['class'], $options['method'], $passable_args);
			}//end for each
		}

	}//end _notify
	
	/**
	 * Removes all the observers assoicated with an event.
	 * 
	 * @param string $event The event to remove all the observers from
	 * 
	 * @return void
	 * @access public
	 */
	public static function clearObservers($event) {
		unset(self::$_observers[$event]);
	}

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
			
			if ($function['event'] === $options['event']) {
				
				if(self::$_traceFilters) {
					$trace = debug_backtrace();
					$function['trace'] = $trace[1]['class'].'::'.$trace[1]['function'];
					self::_logFilter($function);
				}
				
				if($function['type'] === 'closure')
					$passable_args[0] = call_user_func_array( $function['method'], $passable_args);
				else if ($function['object'] === 'instance')
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
	 * Remove all the filters from a class.
	 * 
	 * @param string $class The class the filter is in
	 * @param string $method The method of the class that the filter is in
	 * 
	 * @return void
	 * @access public
	 */
	public static function clearFilters($class, $method) {
		unset(self::$_filters[$class][$method]);
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
	 * Turn on/off the ability to trace an adapter.Turning on will log
	 * an adapter using PVLog when adapter is executed.
	 * 
	 * @param boolean $trace Default is false. If set to true, will trace adatper.
	 * 
	 * @return void
	 * @access public
	 */
	public static function setAdapterTrace($trace = false) {
		self::$_traceAdapters = $trace;
	}
	
	/**
	 * Turn on/off the ability to trace an filter.Turning on will log
	 * a filter using PVLog when filter is executed.
	 * 
	 * @param boolean $trace Default is false. If set to true, will trace filter.
	 * 
	 * @return void
	 * @access public
	 */
	public static function setFilterTrace($trace = false) {
		self::$_traceFilters = $trace;
	}
	
	/**
	 * Turn on/off the ability to trace an observer.Turning on will log
	 * an observer using PVLog when the observer is executed.
	 * 
	 * @param boolean $trace Default is false. If set to true, will trace observer.
	 * 
	 * @return void
	 * @access public
	 */
	public static function setObserverTrace($trace = false) {
		self::$_traceObservers = $trace;	
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
	
	/**
	 * Write out the contents of adapters used to a log
	 * 
	 * @param array $data The data in the adapter
	 * 
	 * @return void
	 * @access private
	 */
	private static function _logAdapter( $data ) {
		$message = self::_prepareLogData($data);
		PVLog::writeLog('adapter', $message);
	}
	
	/**
	 * Write out the contents of a filter used to a log
	 * 
	 * @param array $data The data in the filter
	 * 
	 * @return void
	 * @access private
	 */
	private static function _logFilter( $data ) {
		$message = self::_prepareLogData($data);
		PVLog::writeLog('filter', $message);
	}
	
	/**
	 * Write out the contents of an observer to a log.
	 * 
	 * @param array $data The data in the observer
	 * 
	 * @return void
	 * @access private
	 */
	private static function _logObserver( $data ) {
		$message = self::_prepareLogData($data);
		PVLog::writeLog('observer', $message);
	}
	
	/**
	 * Breaks down the data to be logged from an adapter, filter or observer.
	 * 
	 * @param array $data
	 * 
	 * @return string $message JSON encode message of information about the data
	 * @access private
	 */
	private static function _prepareLogData($data) {
		
		foreach($data as $key => $value) {
			if($value instanceof Closure) {
				$closure = new ReflectionFunction($value);
				$data[$key] = $closure -> getFileName();
				$data['start_line'] = $closure -> getStartLine();
				$data['end_line'] = $closure -> getEndLine();
			} else if(is_object($value)) {
				$object = new ReflectionClass($value);
				$data[$key] = $object -> getFileName();
				$data['start_line'] = $object -> getStartLine();
				$data['end_line'] = $object -> getEndLine();
			} else if(!is_string($value)) {
				unset($data[$key]);
			}
			
		}
		
		return json_encode($data);
	}

}

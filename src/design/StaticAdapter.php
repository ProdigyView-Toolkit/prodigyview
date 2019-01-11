<?php
namespace prodigyview\design;

use prodigyview\util\Log;

trait StaticAdapter {
	
	use StaticInvoke;
	
	/**
	 * The adapters that have been added
	 */
	protected static $_adapters = array();
	
	/**
	 * Boolean for following and logging adapters that have been added
	 */
	protected static $_traceAdapters = false;
	
	/**
	 * Adapters allows a method to be completely overwritten by calling a different class
	 * with the same method name. Adapters can also be used with closures. The adapter uses
	 * a strategy/adapter design pattern.
	 *
	 * @param string $trigger_class  The class that contains the function the adapter will respond too
	 * @param string $trigger_method The method called that will have the adapter to be called.
	 * @param string $call_class The new class to be called that has the same method name
	 * @param array $options An array of options that be called
	 * 			-'object' _string_ : Assumes that default method in the class to be called is static. If called
	 * 			object
	 * 			needs to be instantiated, change to object to 'instance' and one will be created before the
	 * 			adapter calls the function
	 * 			-'call_method' _string_: By default the method to be called to override the current one should
	 * 			be the
	 * 			same name. But this can be ovveridden to call a different method.
	 * 			-'type' _string_: The type of method being called. Default is class_method but if the method is
	 * a closure,
	 * 			set the type to be 'closure' and make the $trigger_method the closure
	 *
	 * @return void
	 * @access public
	 * @todo add ability to adapt singleton class
	 */
	public static function addAdapter(string $trigger_class, string $trigger_method, $call_class, array $options = array()) {
		
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
	 * Will add an adapter for every method in the trigger_class to another class. The method will only
	 * be adapted to another class
	 * if the method in the trigger class has an adapter. This functionality can be very similiar to DI.
	 *
	 * @param mixed $trigger_class This can either be the name of the class or an object whose methods
	 * will be adapted to another class.The class should be included or be autoloaded by this point.
	 * 
	 * @param string $call_class The call class is the classes methods that will be called in place of
	 * 			the methods in the trigger_class.
	 * 		  These class does not have to be included as this point.
	 * @param array $options Options that be used to further distinguish the behavior of the adapters
	 * 				added
	 * 			-'object' _string_: Determines if the object being adapted to is static or an instance.Default
	 * 			is static
	 * 			-'call_class' _string_: The name of the class that the methods will be adapted too.
	 * 			-'class' _string_: The name of the whose methods will be adapted to another class
	 *
	 * @return void
	 * @access public
	 * @todo Add ability to use singleton classes
	 */
	public static function addClassAdapter(string $trigger_class, $call_class, array $options = array()) {

		$defaults = array(
			'object' => 'static',
			'call_class' => $call_class,
			'class' => $trigger_class,
		);
		
		$options += $defaults;

		if ($options['object'] === 'instance' && !is_object($call_class))
			$call_class = new $call_class();

		if (class_exists($trigger_class) || is_object($trigger_class)) {

			$reflection = new ReflectionClass($trigger_class);

			foreach ($reflection->getMethods() as $method) {

				if ($method->class == $trigger_class && $options['object'] === 'static')
					$call_class::addAdapter($trigger_class, $method->name, $call_class, $options);
				else if ($method->class == $trigger_class && $options['object'] === 'instance')
					$call_class->addAdapter($trigger_class, $method->name, $call_class, $options);

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

		if (self::$_traceAdapters) {
			$trace = debug_backtrace();
			$options['trace'] = $trace[1]['class'] . '::' . $trace[1]['function'];
			self::_logAdapter($options);
		}

		if ($options['type'] === 'closure')
			return call_user_func_array($options['call_class'], $passable_args);
		else if ($options['type'] === 'instance')
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
	protected static function _hasAdapter(string $class, string $method) {
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
	public static function removeAdapter(string $class, string $method) {
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
	public static function removeClassAdapter(string $class) {
		unset(self::$_adapters[$class]);
	}
	
	/**
	 * Turn on/off the ability to trace an adapter.Turning on will log
	 * an adapter using Log when adapter is executed.
	 *
	 * @param boolean $trace Default is false. If set to true, will trace adatper.
	 *
	 * @return void
	 * @access public
	 */
	public static function setAdapterTrace(bool $trace = false) {
		self::$_traceAdapters = $trace;
	}
	
	/**
	 * Write out the contents of adapters used to a log
	 *
	 * @param array $data The data in the adapter
	 *
	 * @return void
	 * @access private
	 */
	protected static function _logAdapter(array $data) {
		$message = self::_prepareLogData($data);
		Log::writeLog('adapter', $message);
	}
	
}

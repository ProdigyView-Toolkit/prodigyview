<?php
namespace prodigyview\design;

use prodigyview\util\Log;

trait Adapter {
	
	use Invoke;
	
	/**
	 * The adapters that have been stored
	 */
	protected $_adapters = array();
	
	/**
	 * The boolean for following and printing out adapters as they are called
	 */
	protected $_traceAdapters = false;
	
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
	 * 			a closure,
	 * 			set the type to be 'closure' and make the $trigger_method the closure
	 *
	 * @return void
	 * @access public
	 */
	public function addAdapter(string $trigger_class, string $trigger_method, $call_class, array $options = array()) {

		$defaults = array(
			'object' => 'static',
			'call_class' => $call_class,
			'class' => $trigger_class,
			'method' => $trigger_method,
			'call_method' => $trigger_method,
			'type' => 'class_method'
		);
		
		$options += $defaults;

		$this->_adapters[$trigger_class][$trigger_method] = $options;
	}

	/**
	 * Will add an adapter for every method in the trigger_class to another class. The method will only
	 * be adapted to another class
	 * if the method in the trigger class has an adapter. This functionality can be very similiar to DI.
	 *
	 * @param mixed $trigger_class This can either be the name of the class or an object whose methods
	 * 								will be adapted to another class.
	 * @param string $call_class The call class is the classes methods that will be called in place of
	 * 							the methods in the trigger_class.
	 * @param array $options Options that be used to further distinguish the behavior of the adapters added
	 * 			-'object' _string_: Determines if the object being adapted to is static or an instance.Default
	 * 			is static
	 * 			-'call_class' _string_: The name of the class that the methods will be adapted too.
	 * 			-'class' _string_: The name of the whose methods will be adapted to another class
	 *
	 * @return void
	 * @access public
	 * @todo Add ability to use singleton classes
	 */
	public function addClassAdapter(string $trigger_class, $call_class, array $options = array()) {

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
	 * @param string $method The name of the method that is being adapted.
	 * @param mixed $args An infinite amout of parameters to passed to this method.
	 *
	 * @return mixed $value A value that the adapter returns
	 * @access protected
	 */
	protected function _callAdapter(string $class, string $method) {
		$args = func_get_args();
		array_shift($args);
		array_shift($args);

		$passable_args = array();
		
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		$options = $this->_adapters[$class][$method];

		if ($this->_traceAdapters) {
			$trace = debug_backtrace();
			$options['trace'] = $trace[1]['class'] . '::' . $trace[1]['function'];
			$this->_logAdapter($options);
		}

		if ($options['type'] === 'closure')
			return call_user_func_array($options['call_class'], $passable_args);
		else if ($options['type'] === 'instance')
			return $this->_invokeMethod($options['call_class'], $options['call_method'], $passable_args);
		else
			return $this->_invokeStaticMethod($options['call_class'], $options['call_method'], $passable_args);

	}//end _callAdapter

	/**
	 * Checks if an adapter is set in a class, method combination.
	 *
	 * @param string $class The associated class to check if it has an adapter
	 * @param string $method The associated method to check if it has an adapter
	 *
	 * @return boolean $hasAdapter Returns true if it has an adapter or false if it does not
	 * @access protected
	 */
	protected function _hasAdapter(string $class, string $method) {
		
		if (!empty($this->_adapters[$class][$method])) {
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
	public function removeAdapter(string $class, string $method) {
		unset($this->_adapters[$class][$method]);
	}

	/**
	 * Removes an adapter for an entire class.
	 *
	 * @param string class The associated class the function is calling
	 *
	 * @return void
	 * @access public
	 */
	public function removeClassAdapter(string $class) {
		unset($this->_adapters[$class]);
	}
	
	/**
	 * Turn on/off the ability to trace an adapter.Turning on will log
	 * an adapter using Log class when adapter is executed.
	 *
	 * @param boolean $trace Default is false. If set to true, will trace adatper.
	 *
	 * @return void
	 * @access public
	 */
	public function setAdapterTrace(bool $trace = false) {
		$this->_traceAdapters = $trace;
	}
	
	/**
	 * Write out the contents of adapters used to a log
	 *
	 * @param array $data The data in the adapter
	 *
	 * @return void
	 * @access private
	 */
	protected function _logAdapter(array $data) {
		
		$message = $this->_prepareLogData($data);
		Log::writeLog('adapter', $message);
	}
}

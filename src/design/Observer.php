<?php
namespace prodigyview\design;

use prodigyview\util\Log;

trait Observer {
	
	use Invoke;
	
	/**
	 * Observers that have been stored
	 */
	protected $_observers = array();
	
	/**
	 * The Boolean for following and printing out observers as they are called
	 */
	protected $_traceObservers = false;
	
	/**
	 * Adds an observer to the class. Observer events can fired in any method
	 * to trigger a response.
	 *
	 * @param string $event The name of the event that will cause a certain class and method to fire
	 * @param string $class The name of the class that contains the function that will be fired for this event
	 * @param string $method The name of the method that will be fired when the event occurs
	 * @param array $options Options to further the define the firing of an event
	 * 			-'object' _string_ : If the method being called is static, should be set to static. Else set to
	 * 			instance
	 * 			-'class' _string_ : The name of the class to be called. Default is the class that is passed in.
	 * 			-'method' _string_: The name of the method to be called. Default is the method that is passed
	 * 			in.
	 * 			-'type' _string_: The type of function being called. Default is class_method but if the
	 * 			function is a closure, set the type to be 'closure' and make the $method the closure
	 *
	 * @return void
	 * @access public
	 */
	public function addObserver(string $event, $class, $method, array $options = array()) {
		
		$default = array(
			'object' => 'static',
			'class' => $class,
			'method' => $method,
			'type' => 'class_method'
		);

		$options += $default;
		$this->_observers[$event][] = $options;
	}//end _addObersver

	/**
	 * Calls any functions that have been added to the observer if the event is present in the
	 * observers array.
	 *
	 * @param string $event The name of the event that will trigger notifiers
	 * @param mixed $args An array of infinite arguements that will passed to each function related to
	 * the event
	 *
	 * @return void
	 * @access protected
	 */
	protected function _notify(string $event) {
		
		$args = func_get_args();
		array_shift($args);

		$passable_args = array();
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		if (isset($this->_observers[$event])) {
			foreach ($this->_observers[$event] as $options) {

				if ($this->_traceObservers) {
					$trace = debug_backtrace();
					$options['trace'] = $trace[1]['class'] . '::' . $trace[1]['function'];
					$this->_logObserver($options);
				}

				if ($options['type'] === 'closure')
					call_user_func_array($options['method'], $passable_args);
				else if ($options['type'] === 'instance')
					$this->_invokeMethod($options['class'], $options['method'], $passable_args);
				else
					$this->_invokeStaticMethod($options['class'], $options['method'], $passable_args);
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
	public function clearObservers(string $event) {
		unset($this->_observers[$event]);
	}
	
	/**
	 * Turn on/off the ability to trace an observer.Turning on will log
	 * an observer using Log when the observer is executed.
	 *
	 * @param boolean $trace Default is false. If set to true, will trace observer.
	 *
	 * @return void
	 * @access public
	 */
	public function setObserverTrace(bool $trace = false) {
		$this->_traceObservers = $trace;
	}
	
	/**
	 * Write out the contents of an observer to a log.
	 *
	 * @param array $data The data in the observer
	 *
	 * @return void
	 * @access private
	 */
	protected function _logObserver(array $data) {
		
		$message = $this->_prepareLogData($data);
		Log::writeLog('observer', $message);
	}
}

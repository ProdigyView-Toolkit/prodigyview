<?php
namespace prodigyview\design;

use prodigyview\util\Log;

trait Filter {
	
	use Invoke;
	
	/**
	 * Filters that have been stored
	 */
	protected $_filters = array();


	/**
	 * Boolean for following and printing out filters as they are called
	 */
	protected $_traceFilters = false;
	
	/**
	 * Adds a filter to the class. Filters are for modifying a value within a class and should not
	 * interupt the normal flow within the method(IE: They should not be used as adapters).
	 *
	 * @param string $class The name of the class the filter is going in
	 * @param string $method The name of the method the filter is in
	 * @param string $filter_class The class that the filter resides in.
	 * @param string $filter_method The method in the class that the parameters will be passed too.
	 * @param array $options Options that can be set for further modifying the filter.
	 * 			-'object' _string_: If the method being called is static, static should be inserted. If its in
	 * an instance, 'instance' should be set.
	 * 			Default is set to static.
	 * 			-'event' _string_: Associate this filter with an event.
	 * 			-'type' _string_: The type of function being called. Default is class_method but if the
	 * function is a closure,
	 * 			set the type to be 'closure' and make the $filter_method the closure
	 *
	 * @return void
	 * @access public
	 */
	public function addFilter(string $class, string $method, $filter_class, $filter_method, array $options = array()) {
		
		$defaults = array(
			'object' => 'static',
			'class' => $filter_class,
			'method' => $filter_method,
			'event' => null,
			'type' => 'class_method'
		);

		$options += $defaults;

		if (!isset($this->_filters[$class][$method])) {
			$this->_filters[$class][$method] = array();
		}

		array_push($this->_filters[$class][$method], $options);

	}//end _addFilter

	/**
	 * Apply a fitler if filter is set.
	 *
	 * @param string $class The name of the class the filter is in
	 * @param string $method The method the filter is in
	 * @param mixed $data The data that is being passed to the filter
	 * @param array $options options to be passed to the filter. Passed options we be passed to the
	 * 				function.
	 * 			-'default_return' _mixed_: If no filter is return, the data passed in by default will be
	 * 			return. Can be overriden
	 * 			-'event' _string_: An event to associate with the filter. Default is null
	 *
	 * @return mixed $data The data the function returns
	 * @access protected
	 */
	protected function _applyFilter(string $class, string $method, $data, array $options = array()) {
		
		$defaults = array(
			'default_return' => $data,
			'event' => null
		);
		$options += $defaults;

		if (!isset($this->_filters[$class][$method])) {
			return $options['default_return'];
		}

		$passable_args = array(
			$data,
			$options
		);
		
		foreach ($this->_filters[$class][$method] as $function) {

			if ($function['event'] == $options['event']) {

				if ($this->_traceFilters) {
					$trace = debug_backtrace();
					$function['trace'] = $trace[1]['class'] . '::' . $trace[1]['function'];
					$this->_logFilter($function);
				}

				if ($function['type'] === 'closure')
					$passable_args[0] = call_user_func_array($function['method'], $passable_args);
				else if ($function['type'] === 'instance')
					$passable_args[0] = $this->_invokeMethod($function['class'], $function['method'], $passable_args);
				else
					$passable_args[0] = $this->_invokeStaticMethod($function['class'], $function['method'], $passable_args);
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
	protected function _hasFilter(string $class, string $method) {
		
		if (isset($this->_filters[$class][$method]))
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
	public function clearFilters(string $class, string $method) {
		unset($this->_filters[$class][$method]);
	}
	
	/**
	 * Turn on/off the ability to trace an filter.Turning on will log
	 * a filter using Log class when filter is executed.
	 *
	 * @param boolean $trace Default is false. If set to true, will trace filter.
	 *
	 * @return void
	 * @access public
	 */
	public function setFilterTrace(bool $trace = false) {
		$this->_traceFilters = $trace;
	}
	
	/**
	 * Write out the contents of a filter used to a log
	 *
	 * @param array $data The data in the filter
	 *
	 * @return void
	 * @access private
	 */
	protected function _logFilter(array $data) {
		
		$message = $this->_prepareLogData($data);
		Log::writeLog('filter', $message);
	}
	
	
}

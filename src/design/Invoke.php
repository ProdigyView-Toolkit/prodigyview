<?php
namespace prodigyview\design;

trait Invoke {
	
	/**
	 * Calls a methods that is an instance of an class. This method is generally
	 * faster than using user_call_func_array.
	 *
	 * @param string $class The name of the class to be called
	 * @param string $method The name of the method in the class to be called
	 * @param array $args An array of arguements. Arguements have to be embedded in an array to be
	 * called.
	 *
	 * @return mixed $data Data returned by the function called
	 * @access protected
	 */
	protected function _invokeMethod($class, $method, $args) {
		
		if (!is_object($class))
			$class = new $class();

		switch(count($args)) :
			case 0 :
				return $class->{$method}();
				break;
			case 1 :
				return $class->{$method}($args[0]);
				break;
			case 2 :
				return $class->{$method}($args[0], $args[1]);
				break;
			case 3 :
				return $class->{$method}($args[0], $args[1], $args[2]);
				break;
			case 4 :
				return $class->{$method}($args[0], $args[1], $args[2], $args[3]);
				break;
			case 5 :
				return $class->{$method}($args[0], $args[1], $args[2], $args[3], $args[4]);
				break;
			default :
				return call_user_func_array(array(
					$class,
					$method
				), $args);
				break;
		endswitch;

	}//end _invokeMethod

	/**
	 * Calls a methods that is a method of a class. This method is generally
	 * faster than using user_call_func_array.
	 *
	 * @param string $class The name of the class to be called
	 * @param string $method The name of the method in the class to be called
	 * @param array $args An array of arguements. Arguements have to be embedded in an array to be
	 * called.
	 *
	 * @return mixed $data Data returned by the function called
	 * @access protected
	 */
	protected function _invokeStaticMethod($class, $method, $args) {
		
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
				return call_user_func_array(array(
					$class,
					$method
				), $args);
				break;
		endswitch;

	}//end _invokeMethod
	
	/**
	 * Breaks down the data to be logged from an adapter, filter or observer.
	 *
	 * @param array $data
	 *
	 * @return string $message JSON encode message of information about the data
	 * @access private
	 */
	protected function _prepareLogData($data) {

		foreach ($data as $key => $value) {
			if ($value instanceof Closure) {
				$closure = new \ReflectionFunction($value);
				$data[$key] = $closure->getFileName();
				$data['start_line'] = $closure->getStartLine();
				$data['end_line'] = $closure->getEndLine();
			} else if (is_object($value)) {
				$object = new \ReflectionClass($value);
				$data[$key] = $object->getFileName();
				$data['start_line'] = $object->getStartLine();
				$data['end_line'] = $object->getEndLine();
			} else if (!is_string($value)) {
				unset($data[$key]);
			}

		}

		return json_encode($data);
	}
}

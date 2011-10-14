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

class PVPatterns {
	
	protected static $_adapters=array();
	
	protected static $_observers=array();
	
	/**
	 * Adapters allows completely override the method of another class by calling a different class
	 * with the same function name.
	 * 
	 * @param string $trigger_class  The class that contains the function the adapter will respond too
	 * @param string $trigger_method The method called that will have the adapter to be called.
	 * @param string $call_call The new class to be called that has the same method name
	 * @param array $options An array of options that be called
	 * 			-'call' _string_ : Assumes that default method in the class to be called is static. If called
	 * 			needs to be instantiated, change to instance and one will be created before the adapter calld the function
	 * 			-'call_method' _string_: By default the method to be called to override the current one should be the
	 * 			same name. But this can be ovveridden to call a different method.
	 * 
	 * @return void
	 * @access public
	 */
	public static function _addAdapter($trigger_class, $trigger_method, $call_class, $options=array()) {
		$defaults=array(
			'call'=> 'static',
			'call_class'=>$call_class,
			'class'=>$trigger_class,
			'method'=>$trigger_method,
			'call_method=>'=>$trigger_method
		);
		$options += $defaults;
		
		self::$_adapters[$class][$method]=$options;
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
       
        $passasbe_args = array();
        foreach($args as $key => &$arg){
            $passasbe_args[$key] = &$arg;
        } 
		
		$options=self::$_adapters[$class][$method];
		if($options['call']=='instance')
			
			
		
		return call_user_func_array(array($class, $method), $passasbe_args);
	}//end _callAdapter
	
	/**
	 * Checks if an adapter is set for the function.
	 * 
	 * @param string 
	 */	
	protected static function _hasAdapter($class, $method) {
		if(isset(self::$_adapters[$class][$method])) {
			return TRUE;
		}
		return FALSE;
	}
	
	protected static function _addObserver() {
		
	}
	
	protected static function _fireEvent($event_type) {
		//foreach($_observers[$event_type])
		//self::_notify($obj, $event)
	}
	
	protected static function _notify($obj, $event) {
		
	}
}
	
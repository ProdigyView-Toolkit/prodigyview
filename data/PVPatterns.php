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
	
	protected static function _addAdapter($class, $method, $options) {
		$defaults=array(
			'call'=> 'static',
			'class'=>$class,
			'method'=>$method
		);
		$options += $defaults;
		
		self::$_adapters[$class][$method]=$options;
	}
	
	protected static function _callAdapter($class, $method) {
		$args = func_get_args();
        array_shift($args);
        array_shift($args);
       
        $passasbe_args = array();
        foreach($args as $key => &$arg){
            $passasbe_args[$key] = &$arg;
        } 
		
		return call_user_func_array(array($class, $method), $passasbe_args);
	}//end _callAdapter
	
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
	
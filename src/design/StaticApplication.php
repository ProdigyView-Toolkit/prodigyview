<?php
namespace prodigyview\design;

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

abstract class StaticApplication {
	
	use StaticObject;

	/**
	 * Takes in a command and arguements and if the command exist, will pass that command to
	 * a function with the same name.
	 *
	 * @param string $command The name of the function to be called
	 * @param mixed $args An infinate amount of parameters that can be passed. to a function.
	 *
	 * @return mixed $return Returns the value of the function that is called
	 * @access public
	 */
	public function commandInterpreter($command) {
		$args = func_get_args();
		array_shift($args);

		$passable_args = array();
		
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		if (self::_hasAdapter(get_called_class(), __FUNCTION__))
			return self::_callAdapter(get_called_class(), __FUNCTION__, $command, $passable_args);

		$filtered = self::_applyFilter(get_called_class(), __FUNCTION__, array(
			'command' => $command,
			'passable_args' => $passable_args
		), array('event' => 'args'));
		
		$command = $filtered['command'];
		$passable_args = $filtered['passable_args'];

		if (method_exists($this, $command)) {
			$value = self::_invokeMethod($this, $command, $passable_args);
		} else {
			$value = self::_invokeMethod($this, 'defaultFunction', $passable_args);
		}

		self::_notify(get_called_class() . '::' . __FUNCTION__, $value, $command, $passable_args);
		$value = self::_applyFilter(get_called_class(), __FUNCTION__, $value, array('event' => 'return'));

		return $value;
	}

	/**
	 * The default function that must be implemented. If the commandIntepreter cannot find a correspoding
	 * function, this function will be called.
	 */
	abstract function defaultFunction($params = array());

}//end class

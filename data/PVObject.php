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

class PVObject extends PVPatterns {

	protected $_collection = null;
	
	protected $_methods = array();

	public function __set($index, $value) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $index, $value);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('index' => $index, 'value' => $value), array('event' => 'args'));
		$index = $filtered['index'];
		$value = $filtered['value'];
		
		if ($this -> _collection == null) {
			$this -> _collection = new PVCollection();
		}
		$this -> _collection -> addWithName($index, $value);
		$this->_notify(get_class() . '::' . __FUNCTION__, $index, $value);
	}

	public function __get($index) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $index);
		
		$index = $this->_applyFilter(get_class(), __FUNCTION__, $index, array('event' => 'args'));
		
		if ($this -> _collection == null) {
			$this -> _collection = new PVCollection();
		}
		
		$value = $this -> _collection -> get($index);
		
		$this->_notify(get_class() . '::' . __FUNCTION__, $value, $index);
		$value = $this->_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));
		
		return $value;
	}
	
	function __call($method,$args) {
  			
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $method, $args);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('method' => $method, 'args' => $args), array('event' => 'args'));
		$method = $filtered['method'];
		$args = $filtered['args'];
		
  		if(isset($this->_methods[$method]))
  			$value = call_user_func_array($this->_methods[$method] , $args);
		else 
			$value = null;
		
		$this->_notify(get_class() . '::' . __FUNCTION__, $value, $method, $args);
		$value = $this->_applyFilter(get_class(), __FUNCTION__, $value, array('event' => 'return'));
		
		return $value;
	}

	protected function _addToCollection($data) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $data);
		
		$data = $this->_applyFilter(get_class(), __FUNCTION__, $data, array('event' => 'args'));
		
		if ($this -> _collection == null) {
			$this -> _collection = new PVCollection();
		}
		$this -> _collection -> add($data);
		$this->_notify(get_class() . '::' . __FUNCTION__, $data);
	}//end

	protected function _addToCollectionWithName($name, $data) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $name, $data);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('name' => $name, 'data' => $data), array('event' => 'args'));
		$name = $filtered['name'];
		$data = $filtered['data'];
		
		if ($this -> _collection == null) {
			$this -> _collection = new PVCollection();
		}
		$this -> _collection -> addWithName($name, $data);
		$this->_notify(get_class() . '::' . __FUNCTION__, $name, $data);
	}//end

	public function getIterator() {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__);
		
		if ($this -> _collection == null) {
			$this -> _collection = new PVCollection();
		}
		return $this -> _collection -> getIterator();
	}
	
	public function addMethod($method, $closure) {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__, $method, $closure);
		
		$filtered = $this->_applyFilter(get_class(), __FUNCTION__, array('method' => $method, 'closure' => $closure), array('event' => 'args'));
		$method = $filtered['method'];
		$closure = $filtered['closure'];
		
		$this->_methods[$method]=$closure;
		$this->_notify(get_class() . '::' . __FUNCTION__, $method, $closure);
	}

	protected function getSqlSearchDefaults() {
		
		if ($this->_hasAdapter(get_class(), __FUNCTION__))
			return $this->_callAdapter(get_class(), __FUNCTION__);
			
		$defaults = array(
			'custom_where' => '', 
			'limit' => '', 
			'order_by' => '', 
			'custom_join' => '', 
			'custom_select' => '', 
			'distinct' => '', 
			'group_by' => '', 
			'having' => '', 
			'join_users' => '', 
			'prequery' => '', 
			'current_page' => '', 
			'results_per_page' => '', 
			'paged' => '', 
			'prefix_args' => ''
		);

		$defaults = $this->_applyFilter(get_class(), __FUNCTION__, $defaults, array('event' => 'return'));
		
		return $defaults;
	}

}//end class

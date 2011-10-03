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
	
	public $data=null;
	
	protected $_filters;
	
	public function __set($index, $value) {
		if($this->data==null) {
			$this->data=new PVDataSet();
		}
		$this->data->addWithName($index, $value);
 	}
	
	public function __get($index) {
		return $this->data->get($index);
 	}
	
	protected function _addToDataSet($data) {
		if($this->data==null) {
			$this->data=new PVDataSet();
		}
		$this->data->add($data);
	}//end 
	
	protected function _addToDataSetWithName($name, $data) {
		if($this->data==null) {
			$this->data=new PVDataSet();
		}
		$this->data->addWithName($name, $data);
	}//end 
	
	public function _addFilter($class, $method, $callback){
		
		if(!isset($this->_filters[$class][$method])){
			$this->_filters[$class][$method]=array();
		}
		
		array_push($this->_filters[$class][$method], $callback);
		
	}//end _addFilter
	
	protected function _applyFilter( $class, $method, $data, $default_return){
		
		if(!isset($this->_filters[$class][$method])){
			return $default_return;
		}
		
		if(count($this->_filters[$class][$method])>1){
			$result=array();
			foreach($this->_filters[$class][$method] as $function){
				
				$result[]=call_user_func ( $function , $data );
			}
			return $result;
		}
		
		return call_user_func ( $this->_filters[$class][$method][0] , $data );
	}
	
	protected function getSqlSearchDefaults() {
		$defaults=array(
			'custom_where'=>'',
			'limit'=>'',
			'order_by'=> '',
			'custom_join'=>'',
			'custom_select'=>'',
			'distinct'=>'',
			'group_by'=>'',
			'having'=>'',
			'join_users'=>'',
			'prequery'=>'',
			'current_page'=>'',
			'results_per_page'=>'',
			'paged'=>'',
			'prefix_args'=>''
		);
		
		return $defaults;
	}
	
}//end class
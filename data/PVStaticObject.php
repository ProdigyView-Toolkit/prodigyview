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

class PVStaticObject extends PVPatterns {
	
	public static $data=null;
	
	private static $instance;
	
	protected static $_filters;
	
	public static function __set($index, $value) {
		if(self::$data==null) {
			self::$data=new PVDataSet();
		}
		
		self::$data->addWithName($index, $value);
 	}
	
	public static function __get($index) {
		return self::$data[$index];
 	}
	
	protected static function addToDataSet($data) {
		if(self::$data==null) {
			self::$data=new PVDataSet();
		}
		self::$data->add($data);
	}//end 
	
	protected static function addToDataSetWithName($name, $data) {
		if(self::$data==null) {
			self::$data=new PVDataSet();
		}
		self::$data->addWithName($name, $data);
	}//end 
	
	public static function _addFilter($class, $method, $callback){
		
		if(!isset(self::$_filters[$class][$method])){
			self::$_filters[$class][$method]=array();
		}
		
		array_push(self::$_filters[$class][$method], $callback);
		
	}//end _addFilter
	
	protected static function _applyFilter( $class, $method, $data, $default_return){
		
		if(!isset(self::$_filters[$class][$method])){
			return $default_return;
		}
		
		if(count(self::$_filters[$class][$method])>1){
			$result=array();
			foreach(self::$_filters[$class][$method] as $function){
				
				$result[]=call_user_func ( $function , $data );
			}
			return $result;
		}
		
		return call_user_func ( self::$_filters[$class][$method][0] , $data );
	}
	
	protected static function _getSqlSearchDefaults() {
		$defaults=array(
			'custom_where'=>'',
			'limit'=>'',
			'order_by'=> '',
			'custom_join'=>'',
			'custom_select'=>'',
			'distinct'=>'',
			'group_by'=>'',
			'having'=>'',
			'join_users'=>false,
			'prequery'=>'',
			'current_page'=>'',
			'results_per_page'=>'',
			'paged'=>'',
			'prefix_args'=>'',
			'join_user_roles'=>false,
			'join_content'=>false,
			'join_content'=>false,
			'join_comments'=>false,
			'join_applications'=>false,
			'join_apps'=>false
		);
		
		return $defaults;
	}
	
  	public static function getInstance() { 

    	if(!self::$instance) { 
      		self::$instance = new self(); 
    	} 

    	return self::$instance;
  	} 
	
}//end class
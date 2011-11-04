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
	
	protected $_collection=null;
	
	public function __set($index, $value) {
		if($this->_collection==null) {
			$this->_collection=new PVCollection();
		}
		$this->_collection->addWithName($index, $value);
 	}
	
	public function __get($index) {
		if($this->_collection==null) {
			$this->_collection=new PVCollection();
		}
		return $this->_collection->get($index);
 	}
	
	protected function addToCollection($data) {
		if($this->_collection==null) {
			$this->_collection=new PVCollection();
		}
		$this->_collection->add($data);
	}//end 
	
	protected function addToCollectionWithName($name, $data) {
		if($this->_collection==null) {
			$this->_collection=new PVCollection();
		}
		$this->_collection->addWithName($name, $data);
	}//end 
	
	public function getIterator() {
		if($this->_collection==null) {
			$this->_collection=new PVCollection();
		}
		return $this->_collection->getIterator();
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
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

class PVDataSet implements IteratorAggregate {
    private $dataset = array();
    private $count = 0;

    // Required definition of interface IteratorAggregate
    public function getIterator() {
        return new PVIterator($this->dataset);
    }

    public function add($data) {
    		
    	if(is_array($data)) {
    		$this->dataset[$this->count++] = PVConversions::arrayToObject($data);
    	} else {
        	$this->dataset[$this->count++] = $data;
		}
    }//end add
    
    public function get($index) {
    	return $this->dataset[$index];
    }
	
    
    public function addWithName($name, $data) {
    		
    	if(is_array($data)) {
    		$this->dataset[$name] = PVConversions::arrayToObject($data);
    	} else {
        	$this->dataset[$name] = $data;
		}
		
		$this->count++;
    }//end add

}//end class

class PVIterator implements Iterator {
    private $data = array();

    public function __construct($array) {
        if (is_array($array)) {
            $this->data = $array;
        }
    }

    public function rewind() {
        reset($this->data);
    }
  
    public function current() {
        $data = current($this->data);
        return $data;
    }
  
    public function key()  {
        $data = key($this->data);
        return $data;
    }
  
    public function next() {
        $data = next($this->data);
        return $data;
    }
  
    public function valid() {
        $key = key($this->data);
        $data = ($key !== NULL && $key !== FALSE);
        return $data;
    }

}


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

class PVCollection implements IteratorAggregate {
	private $dataset = array();
	private $count = 0;

	/**
	 * The constructor of this class takes in an array and passes
	 * it to the collection as the initial data.
	 *
	 * @param array $array An array of data
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct($data = array()) {
		$this -> dataset = $data;
	}

	/**
	 * Returns an iterable object that can iteratered over in a loop.
	 *
	 * @return PVIterator $iterable An iterable object
	 * @access public
	 */
	public function getIterator() {
		return new PVIterator($this -> dataset);
	}

	/**
	 * Adds the passed data to an index in the collection that can be
	 * retrieved later.
	 *
	 * @param mixed $data Passed data can be an array, object and any other value.
	 *
	 * @return void
	 * @access public
	 */
	public function add($data) {

		if (is_array($data)) {
			$this -> dataset[$this -> count++] = PVConversions::arrayToObject($data);
		} else {
			$this -> dataset[$this -> count++] = $data;
		}
	}//end add

	/**
	 * PHP magic function that returns an index if it is
	 * in the collection.
	 *
	 * @param string $index An index/key that will be used to find the value, if present
	 *
	 * @return $value  The returned value if found in the index
	 * @access public
	 */
	public function __get($index) {
		if (isset($this -> dataset[$index]))
			return $this -> dataset[$index];
	}

	/**
	 * Same as the magic function __get.
	 * @see get
	 */
	public function get($index) {
		if (isset($this -> dataset[$index]))
			return $this -> dataset[$index];
	}

	/**
	 * Adds a value to the collection but also defines the index/key in which the value
	 * will be placed at
	 *
	 * @param string $name The name/index/key that will be associated with the passed value
	 * @param mixed $data The data the will be story associated with thhat key.
	 *
	 * @return void
	 * @access public
	 */
	public function addWithName($name, $data) {

		if (is_array($data)) {
			$this -> dataset[$name] = PVConversions::arrayToObject($data);
		} else {
			$this -> dataset[$name] = $data;
		}

		$this -> count++;
	}//end add

	/**
	 * Remove a value from the collection based on the key/index.
	 *
	 * @param mixed $index The index can either be a int or string
	 *
	 * @return void
	 * @access public
	 */
	public function remove($index) {
		if (isset($this -> dataset[$index]))
			unset($this -> dataset[$index]);
	}
	
	/**
	 * Returns all the data
	 */
	public function getData() {
		return $this -> dataset;
	}

}//end class

class PVIterator implements Iterator {
	private $data = array();

	/**
	 * Initializes the iterater of information to
	 * iterate through.
	 *
	 * @param array $array An array of information to make iterable
	 *
	 * @return void
	 * @access public
	 */
	public function __construct($array = array()) {
		if (is_array($array)) {
			$this -> data = $array;
		}
	}

	/**
	 * Sets the pointer to the first index in the array
	 *
	 * @return void
	 * @access public
	 */
	public function rewind() {
		reset($this -> data);
	}

	/**
	 * Moves the pointer to the last index in the array and returns
	 * the value of the last index
	 *
	 * @return mixed $value The value of the last index
	 * @access public
	 */
	public function last() {
		return end($this -> data);
	}

	/**
	 * Returns the value of the current index in the array.
	 *
	 * @return mixed $value The data stored in the current index
	 * @access public
	 */
	public function current() {
		$data = current($this -> data);
		return $data;
	}

	/**
	 * Returns the key value of the current index
	 *
	 * @return mixed $key
	 * @access public
	 */
	public function key() {
		$data = key($this -> data);
		return $data;
	}

	/**
	 * Returns the value of the next pointer in the array
	 *
	 * @return mixed $value The data at the location of the next pointer
	 * @access public
	 */
	public function next() {
		$data = next($this -> data);
		return $data;
	}

	/**
	 * Returns the value of the previous pointer in the array
	 *
	 * @return mixed $value The data at the location of the previous pointer
	 * @access public
	 */
	public function previous() {
		$data = prev($this -> data);
		return $data;
	}

	/**
	 * Determines if the key exist, and then returns the associated data
	 *
	 * @return mixed $value The validated data
	 * @access public
	 */
	public function valid() {
		$key = key($this -> data);
		$data = ($key !== NULL && $key !== FALSE);
		return $data;
	}
	
	/**
	 * Returns the data as stored in the object
	 * 
	 */
	public function getData() {
		return $this -> data;
	}

}

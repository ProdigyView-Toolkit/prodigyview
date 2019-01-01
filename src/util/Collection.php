<?php
namespace prodigyview\util;

use prodigyview\util\Conversions;

/**
 * The Collection class acts as a repository for data to be stored, retrieved and iterated over.
 *
 * The collection class is a simple way of storing and getting information with key, value pairs, Any
 * information can be stored and retrieved including strings, array, and objects. Some example use
 * cases:
 * 
 * ```php
 * //Add data and increment over fit
 * $collection = new Collection();
 * $collection -> add('Apples');
 * $collection -> add('Oranges');
 *
 * foreach($collection as $key => $value) {
 * 	echo $value;
 * }
 * ```
 * @package util
 */
class Collection implements \IteratorAggregate {
	/**
	 * The items stored in the collection, key=>value pair
	 */
	private $dataset = array();

	/**
	 * The number of items in the collection
	 */
	private $count = 0;

	/**
	 * The constructor of this class takes in an array and passes
	 * it to the collection as the initial data.
	 *
	 * @param array $data An array of data
	 *
	 * @return void
	 * @access public
	 */
	public function __construct(array $data = array()) {
		$this->dataset = $data;
		$this->count = count($data);
	}

	/**
	 * Returns an iterable object that can iteratered over in a loop.
	 *
	 * @return Iterator $iterable An iterable object
	 * @access public
	 */
	public function getIterator() {
		return new Iterator($this->dataset);
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
			$this->dataset[$this->count++] = Conversions::arrayToObject($data);
		} else {
			$this->dataset[$this->count++] = $data;
		}
		
	}//end add

	/**
	 * PHP magic function that returns an index if it is in the collection.
	 *
	 * @param string $index An index/key that will be used to find the value, if present
	 *
	 * @return $value The returned value if found in the index
	 * @access public
	 */
	public function __get($index) {
		if (isset($this->dataset[$index]))
			return $this->dataset[$index];
	}

	/**
	 * Same as the magic function __get.
	 *
	 * @param string $index An index/key that will be used to find the value, if present
	 *
	 * @see get
	 */
	public function get($index) {
		if (isset($this->dataset[$index]))
			return $this->dataset[$index];
	}

	/**
	 * Adds a value to the collection but also defines the index/key in which the value
	 * will be placed at.
	 *
	 * @param string $name The name/index/key that will be associated with the passed value
	 * @param mixed $data The data the will be story associated with thhat key.
	 *
	 * @return void
	 * @access public
	 */
	public function addWithName(string $name, $data) {

		if (is_array($data)) {
			$this->dataset[$name] = Conversions::arrayToObject($data);
		} else {
			$this->dataset[$name] = $data;
		}

		$this->count++;
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
		if (isset($this->dataset[$index]))
			unset($this->dataset[$index]);
	}

	/**
	 * Returns all the data
	 */
	public function getData() {
		return $this->dataset;
	}

}//end class

/**
 * A class used for iterating over items in loops.
 */
class Iterator implements \Iterator {

	/**
	 * The items to iterate over
	 */
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
	public function __construct(array $array = array()) {
		if (is_array($array)) {
			$this->data = $array;
		}
	}

	/**
	 * Sets the pointer to the first index in the array
	 *
	 * @return void
	 * @access public
	 */
	public function rewind() {
		reset($this->data);
	}

	/**
	 * Moves the pointer to the last index in the array and returns
	 * the value of the last index
	 *
	 * @return mixed $value The value of the last index
	 * @access public
	 */
	public function last() {
		return end($this->data);
	}

	/**
	 * Returns the value of the current index in the array.
	 *
	 * @return mixed $value The data stored in the current index
	 * @access public
	 */
	public function current() {
		$data = current($this->data);
		return $data;
	}

	/**
	 * Returns the key value of the current index
	 *
	 * @return mixed $key
	 * @access public
	 */
	public function key() {
		$data = key($this->data);
		return $data;
	}

	/**
	 * Returns the value of the next pointer in the array
	 *
	 * @return mixed $value The data at the location of the next pointer
	 * @access public
	 */
	public function next() {
		$data = next($this->data);
		return $data;
	}

	/**
	 * Returns the value of the previous pointer in the array
	 *
	 * @return mixed $value The data at the location of the previous pointer
	 * @access public
	 */
	public function previous() {
		$data = prev($this->data);
		return $data;
	}

	/**
	 * Determines if the key exist, and then returns the associated data
	 *
	 * @return mixed $value The validated data
	 * @access public
	 */
	public function valid() {
		$key = key($this->data);
		$data = ($key !== NULL && $key !== FALSE);
		return $data;
	}

	/**
	 * Counts the number of elements currently in the iterator.
	 *
	 * @return int count
	 * @access public
	 */
	public function count() {
		return count($this->data);
	}

	/**
	 * Returns the data as stored in the object
	 *
	 */
	public function getData() {
		return $this->data;
	}

}

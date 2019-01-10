<?php
use prodigyview\util\Collection;

use PHPUnit\Framework\TestCase;

class CollectionTests extends TestCase {
	
	private $_data = array('small' => 'Cat', 'big' => 'Dog', 1 => 'Ferret', 2 => 'Parrots');
	
	public function testPrefilledData() {
		
		$collection = new Collection($this ->_data);
		
		$this-> assertEquals($this ->_data['big'], $collection -> big);
	}
	
	public function testPrefilledDataCompare() {
		
		$collection = new Collection($this ->_data);
		
		$this-> assertEquals($this -> _data, $collection -> getData());
	}
	
	public function testAddingData() {
		$collection = new Collection();
		
		foreach($this -> _data as $key => $value) {
			$collection->add($value);
		}
		
		$data = $collection -> getIterator() -> getData();
		
		$cleaned_data = array_values($this -> _data);
		
		$this-> assertEquals($data, $cleaned_data);
	}
	
	public function testAddingDataWithName() {
		$collection = new Collection();
		
		foreach($this -> _data as $key => $value) {
			$collection->addWithName($key, $value);
		}
		
		$this-> assertEquals($this -> _data, $collection -> getData());
	}
	
}


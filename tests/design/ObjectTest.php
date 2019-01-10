<?php
use prodigyview\util\Collection;
use prodigyview\design\InstanceObject;
use prodigyview\design\StaticObject;

use PHPUnit\Framework\TestCase;

class InstanceTest {
	
	use InstanceObject;
}

class StaticTest {
	
	use StaticObject;
	
	/**
	 * Resets the collection, otherwise data persist
	 */
	public static function resetCollection() {
		self::$_collection['StaticTest'] = new Collection();
	}
}

class ObjectTests extends TestCase {
	
	private $_data = array(
		'bark' => 'Dog',
		'meow' => 'Cat',
		'gallup' => 'Horse'
	);
	
	public function testInstanceAddDataWithSetters() {
		
		$instance = new InstanceTest();
		
		foreach($this ->_data as $key => $value) {
			
			$instance->$key=$value;
			
		}//endforeach
		
		$this -> assertEquals($this->_data['meow'], $instance-> meow);
	}
	
	public function testInstanceAddDataWithAddWithName() {
		
		$instance = new InstanceTest();
		
		foreach($this ->_data as $key => $value) {
			
			$instance->addToCollectionWithName($key, $value);
			
		}//endforeach
		
		$this -> assertEquals($this->_data['gallup'], $instance-> gallup);
	}
	
	public function testInstanceAddDataWithAddWithoutName() {
		
		$instance = new InstanceTest();
		
		foreach($this ->_data as $key => $value) {
			
			$instance->addToCollection($value);
			
		}//endforeach
		
		$this -> assertEquals(array_values($this->_data), $instance-> getIterator()->getData());
	}
	
	public function testInstanceWithMethod() {
		
		$instance = new InstanceTest();
		
		$instance->addMethod('foo', function($string) {
			return 'bar-'.$string;
		});
		
		
		$this -> assertEquals('bar-test', $instance->foo('test'));
		
		
	}
	
	public function testStaticAddDataWithSetters() {
		
		foreach($this ->_data as $key => $value) {
			
			StaticTest::set($key,$value);
			
		}//endforeach
		
		$this -> assertEquals($this->_data['meow'], StaticTest::get('meow'));
	}
	
	public function testStaticAddDataWithAddWithName() {
		
		foreach($this ->_data as $key => $value) {
			
			StaticTest::addToCollectionWithName($key, $value);
			
		}//endforeach
		
		$this -> assertEquals($this->_data['gallup'], StaticTest::get('gallup'));
	}
	
	public function testStaticAddDataWithAddWithoutName() {
		
		StaticTest::resetCollection();
		
		foreach($this ->_data as $key => $value) {
			
			StaticTest::addToCollection($value);
			
		}//endforeach
		
		$this -> assertEquals(array_values($this->_data), StaticTest::getIterator()->getData());
	}
	
	public function testStaticWithMethod() {
		
		StaticTest::addMethod('foo', function($string) {
			return 'bar-'.$string;
		});
		
		
		$this -> assertEquals('bar-test', StaticTest::foo('test'));
		
		
	}
}

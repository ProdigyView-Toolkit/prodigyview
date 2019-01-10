<?php
use prodigyview\design\StaticInstance;

use PHPUnit\Framework\TestCase;

class StaticInstanceObject {	
	use StaticInstance;
}

class StaticStaticInstanceObjects extends TestCase {
	
	private $_data = array(
		'bark' => 'Dog',
		'meow' => 'Cat',
		'gallup' => 'Horse'
	);
	
	public function testInstanceAddDataWithSetters() {
		
		$instance = new StaticInstanceObject();
		
		foreach($this ->_data as $key => $value) {
			
			$instance->$key=$value;
			
		}//endforeach
		
		$this -> assertEquals($this->_data['meow'], $instance-> meow);
	}
	
	public function testInstanceAddDataWithAddWithName() {
		
		$instance = new StaticInstanceObject();
		
		foreach($this ->_data as $key => $value) {
			
			$instance->addToCollectionWithName($key, $value);
			
		}//endforeach
		
		$this -> assertEquals($this->_data['gallup'], $instance-> gallup);
	}
	
	public function testInstanceAddDataWithAddWithoutName() {
		
		$instance = new StaticInstanceObject();
		
		foreach($this ->_data as $key => $value) {
			
			$instance->addToCollection($value);
			
		}//endforeach
		
		$this -> assertEquals(array_values($this->_data), $instance-> getIterator()->getData());
	}
	
	public function testInstanceWithMethod() {
		
		$instance = new StaticInstanceObject();
		
		$instance->addMethod('foo', function($string) {
			return 'bar-'.$string;
		});
		
		
		$this -> assertEquals('bar-test', $instance->foo('test'));	
	}
}

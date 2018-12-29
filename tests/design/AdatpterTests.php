<?php
use prodigyview\design\Adapter;
use prodigyview\design\StaticAdapter;

use PHPUnit\Framework\TestCase;

/**
 * The base class that extends the adapter and be used
 * to call other classes
 */
class TestInstance {
	
	use Adapter;
	
	public function execute($string) {
		
		if($this-> _hasAdapter(get_class(), __FUNCTION__))
			return $this -> _callAdapter(get_class(), __FUNCTION__, $string);
		
		return $string;
	}
	
}

/**
 * The base class that extends the adapter and be used
 * to call other classes
 */
class TestStatic {
	
	use StaticAdapter;
	
	public static function execute($string) {
		
		if(self::_hasAdapter(get_called_class(), __FUNCTION__))
			return self::_callAdapter(get_called_class(), __FUNCTION__, $string);
		
		return $string;
	}
	
}

/**
 * The class to be used when an instance is set as an adapter
 */
class TestAdapterInstance {
	
	public function execute($string) {
		
		return $string . '_test_instance';
		
	}
	
}

/**
 * The class to be used when adapted to a static method/function
 */
class TestAdapterStatic {
	
	public static function execute($string) {
		
		return $string . '_test_static';
		
	}
}

/**
 * The PHPUnit class to be executed during the test cases.
 */
class AdapterTests extends TestCase {

	private $_testString = 'Abc !@#@';
	
	private $_object = null;
	
	protected function setUp() {
		
		$this -> _object = new TestInstance();
		
	}

	protected function tearDown() {
		
	}

	/**
	 * Test the class with no adapter, to ensure its working
	 */
	public function testNoAdapter() {
		
		$result = $this -> _object -> execute($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
	}
	
	/**
	 * Test the adapter as a closusure
	 */
	public function testClosureAdapter() {
		
		//Add an Adapter as a closure
		$this -> _object -> addAdapter('TestInstance', 'execute',  function($string) {
			return $string . '_test_closure';
			
		}, array('type' => 'closure'));
		
		$result_string = $this -> _testString . '_test_closure';
		
		$result = $this -> _object -> execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);
		
	}
	
	/**
	 * Test that the adapter can be removed
	 */
	public function testRemoveAdapter() {
		
		$this -> _object -> removeAdapter('TestInstance', 'execute');
		
		$result = $this -> _object -> execute($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
		
	}
	
	/**
	 * Test that adapter works being being passed to an instance
	 */
	public function testInstanceAdapter() {
		
		$instance = new TestAdapterInstance();
		
		//Add an Adapter as a closure
		$this -> _object -> addAdapter('TestInstance', 'execute', $instance, array('type' => 'instance'));
		
		$result_string = $this -> _testString . '_test_instance';
		
		$result = $this -> _object -> execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);	
	}
	
	/**
	 * Test that adapter works being being passed to an instance
	 */
	public function testStaticAdapter() {
		
		//Add an Adapter as a closure
		$this -> _object -> addAdapter('TestInstance', 'execute', 'TestAdapterStatic');
		
		$result_string = $this -> _testString . '_test_static';
		
		$result = $this -> _object -> execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);	
	}
	
	/**
	 * Test that adapter works being being passed to an instance
	 */
	public function testInstanceLogAdapter() {
		
		$instance = new TestAdapterInstance();
		
		//Add an Adapter as a closure
		$this -> _object -> addAdapter('TestInstance', 'execute', $instance, array('type' => 'instance'));
		
		$this -> _object -> setAdapterTrace(true);
		
		$result_string = $this -> _testString . '_test_instance';
		
		$result = $this -> _object -> execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);	
	}
	
	/**
	 * Test the static class with no adapter, to ensure its working
	 */
	public function testStaticNoAdapter() {
		
		$result = TestStatic::execute($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
	}
	
	/**
	 * Test the static adapter as a closusure
	 */
	public function testStaticClosureAdapter() {
		
		//Add an Adapter as a closure
		TestStatic::addAdapter('TestStatic', 'execute',  function($string) {
			return $string . '_test_closure';
			
		}, array('type' => 'closure'));
		
		$result_string = $this -> _testString . '_test_closure';
		
		$result = TestStatic::execute($this -> _testString);
		
		$this->assertEquals($result, $result_string);
	}
	
	/**
	 * Test that the adapter can be removed
	 */
	public function testStaticRemoveAdapter() {
		
		TestStatic::removeAdapter('TestStatic', 'execute');
		
		$result = TestStatic::execute($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
		
	}
	
	/**
	 * Test that adapter works being being passed to an instance
	 */
	public function testStaticInstanceAdapter() {
		
		$instance = new TestAdapterInstance();
		
		//Add an Adapter as a closure
		TestStatic::addAdapter('TestStatic', 'execute', $instance, array('type' => 'instance'));
		
		$result_string = $this -> _testString . '_test_instance';
		
		$result = TestStatic::execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);	
	}
	
	/**
	 * Test that adapter works being being passed to an instance
	 */
	public function testStaticStaticAdapter() {
		
		//Add an Adapter as a closure
		TestStatic::addAdapter('TestStatic', 'execute', 'TestAdapterStatic');
		
		$result_string = $this -> _testString . '_test_static';
		
		$result = TestStatic::execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);	
	}

	/**
	 * Test with logging
	 */
	public function testStaticLogAdapter() {
		
		//Add an Adapter as a closure
		TestStatic::setAdapterTrace(true);
		
		$result_string = $this -> _testString . '_test_static';
		
		$result = TestStatic::execute($this -> _testString);
		
		
		$this->assertEquals($result, $result_string);	
	}

	

}

<?php
use prodigyview\design\Filter;
use prodigyview\design\StaticFilter;

use PHPUnit\Framework\TestCase;

/**
 * This class will be used to testing and applying the filters for instances
 * in which the filter is applied.
 */
class FilterInstance {
	
	use Filter;
	
	public function filter($string) {
		
		if ($this -> _hasFilter(get_class(), __FUNCTION__))
			$string = $this -> _applyFilter(get_class(), __FUNCTION__, $string);
		
		return $string;
	}
	
}

/**
 * This class will be used for testing and applying the filters to static
 * implementations of the filter.
 */
class FilterStatic {
	
	use StaticFilter;
	
	public static function filter($string) {
		
		if (self::_hasFilter(get_called_class(), __FUNCTION__))
			$string = self::_applyFilter(get_called_class(), __FUNCTION__, $string);
		
		return $string;	
	}
	
}

/**
 * This class will be used for passed the data to another
 * class to be filtered.
 */
class FilterInstanceTest {
	
	public function filter($string) {
		return $string . '_instance_class';
	}
}

/**
 * This class will be used to test passing the data to a static
 * class to test the filter.
 */
class FilterStaticTest {
	
	public function filter($string) {
		return $string . '_instance_static';
	}
}

/**
 * This is the actual test cases run by PHP Unit.
 */
class FilterTests extends TestCase {
	
	private $_testString = 'Farlow Jimmy';
	
	private $_object = null;
	
	protected function setUp() {
		
		$this -> _object = new FilterInstance();
	}
	
	protected function tearDown() {
		
	}
	
	
	/**
	 * Test the results when no filter is applied.
	 */
	public function testNoFilter() {
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
		
	}
	
	/**
	 * Test the filter in which a filter is applied as a closure.
	 */
	public function testInstanceClosureFilter() {
		
		$this -> _object -> addFilter('FilterInstance', 'filter', 'closure', function($string) {
			
			return $string . '_instance_closure';
			
		}, array('type' => 'closure'));
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_closure';
		
		$this->assertEquals($result, $test_result);
	}
	
	/**
	 * Test the filter by seeing if all filters can be removed.
	 */
	public function testClearFilter() {
		
		$this -> _object -> addFilter('FilterInstance', 'filter', 'closure', function($string) {
			
			return $string . '_instance_closure';
			
		}, array('type' => 'closure'));
		
		$this -> _object -> clearFilters('FilterInstance', 'filter');
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
		
	}
	
	/**
	 * Test the filter when and instance is passed to another instance
	 * to do the filtering.
	 */
	public function testInstanceClassFilter() {
		
		$instance = new FilterInstanceTest();
		
		$this -> _object -> addFilter('FilterInstance', 'filter', $instance, 'filter', array('type' => 'instance'));
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_class';
		
		$this->assertEquals($result, $test_result);
		
	}
	
	/**
	 * Test the filter when the logging is enabled to trace the output.
	 */
	public function testInstanceClassLogFilter() {
		
		$this -> _object -> setFilterTrace(true);
		
		$instance = new FilterInstanceTest();
		
		$this -> _object -> addFilter('FilterInstance', 'filter', $instance, 'filter', array('type' => 'instance'));
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_class';
		
		$this->assertEquals($result, $test_result);
		
	}
	
	/**
	 * Test an instance filter when a static method of another
	 * class will be doing the filtering.
	 */
	public function testInstanceStatic() {
		
		$this -> _object -> addFilter('FilterInstance', 'filter', 'FilterStaticTest', 'filter');
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_static';
		
		$this->assertEquals($result, $test_result);
		
	}

	/**
	 * Test an instance filter when multiple filters are added and has
	 * to be chained through.
	 */
	public function testInstanceMultiple() {
		
		$this -> _object -> addFilter('FilterInstance', 'filter', 'closure', function($string) {
			
			return $string . '_instance_closure';
			
		}, array('type' => 'closure'));
		
		$instance = new FilterInstanceTest();
		
		$this -> _object -> addFilter('FilterInstance', 'filter', $instance, 'filter', array('type' => 'instance'));
		
		$this -> _object -> addFilter('FilterInstance', 'filter', 'FilterStaticTest', 'filter');
		
		$result = $this -> _object -> filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_closure'. '_instance_class' . '_instance_static';
		
		$this->assertEquals($result, $test_result);
	}

	/**
	 * Test the results when no filter is applied.
	 */
	public function testStaticNoFilter() {
		
		$result = FilterStatic::filter($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
		
	}
	
	/**
	 * Test the filter to a static method in which a filter is applied as a closure.
	 */
	public function testStaticClosureFilter() {
		
		FilterStatic::addFilter('FilterStatic', 'filter', 'closure', function($string) {
			
			return $string . '_instance_closure';
			
		}, array('type' => 'closure'));
		
		$result = FilterStatic::filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_closure';
		
		$this->assertEquals($result, $test_result);
	}
	
	/**
	 * Test the filter by seeing if all filters can be removed.
	 */
	public function testStaticClearFilter() {
		
		FilterStatic::addFilter('FilterStatic', 'filter', 'closure', function($string) {
			
			return $string . '_instance_closure';
			
		}, array('type' => 'closure'));
		
		FilterStatic::clearFilters('FilterStatic', 'filter');
		
		$result = FilterStatic::filter($this -> _testString);
		
		$this->assertEquals($result, $this -> _testString);
		
	}
	
	/**
	 * Test the filter when a static method is passed to another instance
	 * to do the filtering.
	 */
	public function testStaticClassFilter() {
		
		$instance = new FilterInstanceTest();
		
		FilterStatic::addFilter('FilterStatic', 'filter', $instance, 'filter', array('type' => 'instance'));
		
		$result = FilterStatic::filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_class';
		
		$this->assertEquals($result, $test_result);
		
	}
	
	/**
	 * Test the filter when the logging is enabled to trace the output.
	 */
	public function testStaticClassLogFilter() {
		
		FilterStatic::clearFilters('FilterStatic', 'filter');
		
		FilterStatic::setFilterTrace(true);
		
		$instance = new FilterInstanceTest();
		
		FilterStatic::addFilter('FilterStatic', 'filter', $instance, 'filter', array('type' => 'instance'));
		
		$result = FilterStatic::filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_class';
		
		$this->assertEquals($result, $test_result);
		
	}
	
	/**
	 * Test an static filter when a static method of another
	 * class will be doing the filtering.
	 */
	public function testStaticStatic() {
		
		FilterStatic::clearFilters('FilterStatic', 'filter');
		
		
		FilterStatic::addFilter('FilterStatic', 'filter', 'FilterStaticTest', 'filter');
		
		$result = FilterStatic::filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_static';
		
		$this->assertEquals($result, $test_result);
		
	}

	/**
	 * Test an static filter when multiple filters are added and has
	 * to be chained through.
	 */
	public function testStaticMultiple() {
		
		FilterStatic::clearFilters('FilterStatic', 'filter');
		
		FilterStatic::addFilter('FilterStatic', 'filter', 'closure', function($string) {
			
			return $string . '_instance_closure';
			
		}, array('type' => 'closure'));
		
		$instance = new FilterInstanceTest();
		
		FilterStatic::addFilter('FilterStatic', 'filter', $instance, 'filter', array('type' => 'instance'));
		
		FilterStatic::addFilter('FilterStatic', 'filter', 'FilterStaticTest', 'filter');
		
		$result = FilterStatic::filter($this -> _testString);
		
		$test_result = $this -> _testString . '_instance_closure'. '_instance_class' . '_instance_static';
		
		$this->assertEquals($result, $test_result);
	}
	
}

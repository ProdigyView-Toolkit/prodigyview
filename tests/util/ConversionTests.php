<?php
use prodigyview\util\Conversions;
use prodigyview\util\Tools;

use PHPUnit\Framework\TestCase;

/**
 * Test the Conversions class to ensure that data is being properly converted between
 * several different formats.
 * 
 * @todo Add testing for encoding
 */
class ConversionTests extends TestCase {
	
	private $_testArray = null;
	
	private $_testObject = null;
	
	private $_testXML = '<?xml version="1.0"?><root><slot_1>SLOT 1</slot_1><item_123>SLOT 2</item_123><sub><item_0>child1</item_0><one>Chid 2</one></sub></root>';
	
	protected function setUp() {
		$this -> _testArray = array('slot_1' => 'SLOT 1', 123 => 'SLOT 2', 'sub' => array('child1', 'one' => 'Chid 2'));
		
		$this -> _testObject = (object) array('slot_1' => 'SLOT 1', 123 => 'SLOT 2', 'sub' => (object) array('child1', 'one' => 'Chid 2'));
	}
	
	public function testObjectToArray() {
		
		$output = Conversions::objectToArray($this -> _testObject);
		
		$this->assertTrue($this -> arrays_are_similar($output, $this -> _testArray));
	}
	
	public function testArrayToObject() {
		
		$output = Conversions::arrayToObject($this -> _testArray);
		
		$this->assertEquals($output, $this -> _testObject);
	}
	
	public function testArrayToXML() {
		
		$output = Conversions::arrayToXml($this -> _testArray);
		
		$this->assertEquals($output, $this -> _testXML);
		
	}
	
	public function testXmlToArray() {
		
		$output = Conversions::xmlToArray($this -> _testXML);
		
		Tools::replaceKeyNamesInArray($output, 'item_', '');
		
		$this->assertEquals($output, $this -> _testArray);
	}
	
	public function testEncodingSameArray() {
		
		$output = $this -> _testArray;
		Conversions::encodeRecursive($output);
		
		$this->assertEquals($output, $this -> _testArray);
	}
	
	public function testEncodingDifferentArray() {
		
		$output = $this -> _testArray;
		Conversions::encodeRecursive($output,'EUC-JP');
		
		$this->assertEquals($output, $this -> _testArray);
	}
	
	
	
	/**
	 * Determine if two associative arrays are similar
	 *
	 * Both arrays must have the same indexes with identical values
	 * without respect to key ordering
	 *
	 * @param array $a
	 * @param array $b
	 * @return bool
	 * @author https://stackoverflow.com/questions/3838288/phpunit-assert-two-arrays-are-equal-but-order-of-elements-not-important
	 */
	private function arrays_are_similar($a, $b) {
		// if the indexes don't match, return immediately
		if (count(array_diff_assoc($a, $b))) {
			return false;
		}
		// we know that the indexes, but maybe not values, match.
		// compare the values between the two arrays
		foreach ($a as $k => $v) {
			if ($v !== $b[$k]) {
				return false;
			}
		}
		// we have identical indexes, and no unequal values
		return true;
	}
	
}

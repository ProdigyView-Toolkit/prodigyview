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
		
		$this->assertEquals($output, $this -> _testArray);
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
	
}

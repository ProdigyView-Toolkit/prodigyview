<?php

use prodigyview\system\Configuration;

use PHPUnit\Framework\TestCase;

class ConfigurationTests extends TestCase {
	
	protected function setUp() {
		Configuration::init();
	}
	
	public function testAddRetrieveConfiguration() {
		
		$key = 'config1';
		
		$value = 'item1';
		
		Configuration::addConfiguration($key, $value);
		
		$this-> assertEquals($value, Configuration::getConfiguration($key));
	}
	
	public function testAddRetrieveArrayConfiguration() {
		
		$key = 'config2';
		
		$value = array(
			'item1' => 'Apples',
			'item2' => 'Bears',
		);
		
		Configuration::addConfiguration($key, $value);
		
		$this-> assertEquals('Bears', Configuration::getConfiguration($key) -> item2);
	}
		
}
	
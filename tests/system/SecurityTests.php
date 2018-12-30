<?php

use prodigyview\system\Security;

use PHPUnit\Framework\TestCase;

class SecurityTests extends TestCase {
	
	protected function setUp() {
		Security::init();
	}
	
	public function testEncyption() {
		
		$string = 'String to encrypt';
		
		$result = Security::encrypt($string);
		
		$this->assertEquals($string, Security::decrypt($result));
		
	}
	
	public function testPasswordHash() {
		
		$result = Security::hash('abc123');
		
		$this -> assertTrue(true);
	}
	
	public function testToken() {
		
		$length = 10;
		
		$token = Security::generateToken($length);
		
		$this -> assertEquals(strlen($token), $length*2);
	}
		
}
	
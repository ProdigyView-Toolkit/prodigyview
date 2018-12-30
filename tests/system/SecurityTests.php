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
	
	public function testHMacSignature() {
		
		$public_key = 'public_key';
		
		$private_key = 'private_key';
		
		$signature_1 = Security::encodeHmacSignature($public_key, $private_key);
		
		$signature_2 = Security::encodeHmacSignature($public_key, $private_key);
		
		$this->assertTrue(hash_equals($signature_1, $signature_2));
		
	}
		
}
	
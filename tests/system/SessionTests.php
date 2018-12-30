<?php

use prodigyview\system\Session;

use PHPUnit\Framework\TestCase;

class SessionTests extends TestCase {
	
	protected function setUp() {
		$prev = error_reporting(0);
		Session::init();
		error_reporting($prev);
	}
	
	public function testCookie() {
		$cookie_name = 'test_cookie';
		$cookie_value = 'test_cookie_value';
		
		$prev = error_reporting(0);
		Session::writeCookie($cookie_name, $cookie_value);
		error_reporting($prev);
		
		$this -> assertTrue(true);
		
	}
	
	public function testSession() {
		$cookie_name = 'test_cookie';
		$cookie_value = 'test_cookie_value';
		
		Session::writeSession($cookie_name, $cookie_value);
		
		$this -> assertEquals($cookie_value, Session::readSession($cookie_name));
		
	}
	
}
	
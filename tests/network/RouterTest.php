<?php

use prodigyview\network\Router;

use PHPUnit\Framework\TestCase;

class RouterTests extends TestCase {
	
	protected function setUp() {
		Router::init();
	}
	
	public function testGetCurrentUrlPort80() {

		$_SERVER['SERVER_PORT'] = 80;

		$_SERVER['HTTP_HOST'] = 'www.example.com';

		$_SERVER['REQUEST_URI'] = '?item=1';

		$url = Router::getCurrentUrl();

		$this->assertEquals('http://www.example.com?item=1', $url);
	}

	public function testGetCurrentUrlNotPort80() {

		$_SERVER['SERVER_PORT'] = 8080;

		$_SERVER['HTTP_HOST'] = 'www.example.com';

		$_SERVER['REQUEST_URI'] = '?item=1';

		$url = Router::getCurrentUrl();

		$this->assertEquals('http://www.example.com:8080?item=1', $url);
	}
}

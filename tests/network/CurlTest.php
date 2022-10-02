<?php
use prodigyview\network\Curl;

use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {
	
	public function testGet() {
		
		$url = 'https://developer.uspto.gov/ptab-api/v3/api-docs';
		
		$curl = new Curl($url);
		$curl->send('get');
		$data = json_decode($curl->getResponse(), true);
		
		$this->assertTrue(is_array($data));
	}
	
}

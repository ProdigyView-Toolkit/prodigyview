<?php
use prodigyview\network\Curl;

use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {
	
	public function testGet() {
		
		$url = 'file://' . realpath(__DIR__ . '/fixtures/curl_get.json');
		
		$curl = new Curl($url);
		$curl->send('get');
		$data = json_decode($curl->getResponse(), true);
		
		$this->assertTrue(is_array($data));
	}
	
}

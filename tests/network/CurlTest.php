<?php
use prodigyview\network\Curl;

use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase {
	
	public function testGet() {
		
		$url = 'https://ptabdata.uspto.gov/ptab-api/swagger/swagger.json';
		
		$curl = new Curl($url);
		$curl->send('get');
		$data = json_decode($curl->getResponse(), true);
		
		$this->assertTrue(is_array($data));
	}
	
}

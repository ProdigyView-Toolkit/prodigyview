<?php
use prodigyview\util\Cache;

use PHPUnit\Framework\TestCase;

class CacheTests extends TestCase {
		
	private $cacheKey = 'testcache';
	
	private $cacheContent = 'ABCDEFGHIJKLMNOP';

	protected function setUp() {
		Cache::init(array(
			'cache_location' => sys_get_temp_dir ()
		));
	}

	protected function tearDown() {
		Cache::deleteCache($this->cacheKey);
	}

	public function testCacheNotExist() {
		$result = Cache::hasExpired($this->cacheKey);
		
		$this->assertTrue($result);
	}
	
	public function testCacheNotReadable() {
		$result = Cache::readCache($this->cacheKey);
		
		$this->assertEquals($result, '');
	}
	
	public function testCacheWrite() {
		Cache::writeCache($this->cacheKey, $this->cacheContent);
		
		$result = Cache::readCache($this->cacheKey);
		$this->assertEquals($result,$this->cacheContent);
	}
	
	public function testCacheDelete() {
		Cache::deleteCache($this->cacheKey);
		
		$result = Cache::readCache($this->cacheKey);
		$this->assertEquals($result, '');
	}

}

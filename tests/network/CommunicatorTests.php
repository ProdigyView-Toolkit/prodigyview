<?php
include ('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class CommunicatorTests extends TestCase {
	private $cacheKey = 'testcache';
	
	private $cacheContent = 'ABCDEFGHIJKLMNOP';

	protected function setUp() {
		PVCache::init(array(
			'cache_location' => sys_get_temp_dir ()
		));
	}

	protected function tearDown() {
		PVCache::deleteCache($this->cacheKey);
	}

	public function testCacheNotExist() {
		$result = PVCache::hasExpired($this->cacheKey);
		$this->assertTrue($result);
	}
	
	public function testCacheNotReadable() {
		$result = PVCache::readCache($this->cacheKey);
		$this->assertEquals($result, '');
	}
	
	public function testCacheWrite() {
		PVCache::writeCache($this->cacheKey, $this->cacheContent);
		$result = PVCache::readCache($this->cacheKey);
		$this->assertEquals($result,$this->cacheContent);
	}
	
	public function testCacheDelete() {
		PVCache::deleteCache($this->cacheKey);
		$result = PVCache::readCache($this->cacheKey);
		$this->assertEquals($result, '');
	}

}

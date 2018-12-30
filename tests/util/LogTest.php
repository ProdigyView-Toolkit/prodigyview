<?php
use prodigyview\util\Log;
use prodigyview\util\FileManager;

use PHPUnit\Framework\TestCase;

class LogTests extends TestCase {
	
	private $_test_dir = './log_tests/';
	
	protected function setUp() {
		if(!file_exists($this -> _test_dir)) {
			mkdir($this -> _test_dir);
		}
		
		Log::init(array('directory' => $this -> _test_dir));
	}
	
	protected function tearDown() {
		FileManager::deleteDirectory($this -> _test_dir);
	}
	
	public function testWriteLog() {
		$result = Log::writeLog('Low', 'Testing Low Log Message');
		
		$this -> assertTrue($result);
	} 
	
	public function testReadLog() {
		$priority = 'High';
		$message = 'Testing High Log Message';
		
		Log::writeLog($priority, $message);
		
		$log = Log::readLog($priority);
		
		$this -> assertTrue((strpos($log, $priority) !== false && strpos($log, $message) !== false));
	} 
	
}

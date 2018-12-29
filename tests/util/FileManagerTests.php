<?php
use prodigyview\util\FileManager;
use prodigyview\util\Tools;

use PHPUnit\Framework\TestCase;

/**
 * Test the file manager for manipulating the file system.
 * 
 * @todo Finish writing tests
 */
class FileManagerTests extends TestCase {
	
	private $_test_dir = './file_manager_tests/';
	
	private $_random_files = array(
		'file1.txt',
		'file2.txt',
		'file3.txt',
		'file4.txt',
	);
	
	protected function setUp() {
		if(!file_exists($this -> _test_dir)) {
			mkdir($this -> _test_dir);
		}
	}
	
	protected function tearDown() {
		FileManager::deleteDirectory($this -> _test_dir);
	}
	
	public function testFileSizePerl() {
		
		$file_name = $this -> _test_dir.'perl_file_test.txt';
		$size = 500;
		
		if(!file_exists($file_name)) {
			$fp = fopen($file_name, 'w');
			fseek($fp, $size-1,SEEK_CUR);
			fwrite($fp,'a');
			fclose($fp);
		}
		
		$perl_size = FileManager::getFileSize_PERL($file_name);
		
		$this->assertEquals($perl_size, $size);
	}
	
	public function testFileCount() {
		$folder = $this -> _test_dir.'file_count/';
		
		if(!file_exists($folder)) {
			mkdir($folder);
		}
		
		foreach($this -> _random_files as $file) {
			$tmp_file = $folder.$file;
			
			if(!file_exists($tmp_file)) {
				$fp = fopen($tmp_file, "w");
				fclose($fp);
				
			}
		}//endforeach
		
		$found_files = FileManager::getFilesInDirectory($folder);
		
		
		$this->assertEquals(count($found_files), count($this -> _random_files));
	}
	
	public function testWriteFile() {
		$stored_file = $this -> _test_dir.'stored_file.txt';
		
		FileManager::writeFile($stored_file, 'PHP Unit Tests');
		
		$this->assertTrue(file_exists($stored_file));
	}
	
	public function testWillNotWriteOverExisting() {
		
		$stored_file = $this -> _test_dir.'stored_file.txt';
		
		FileManager::writeFile($stored_file, 'PHP Unit Tests');
		
		$result = FileManager::writeNewFile($stored_file, 'PHP Unit Tests');
		
		$this->assertFalse($result);
		
	}
	
	public function testCreateRandomNewFile() {
		
		$stored_file = $this -> _test_dir. Tools::generateRandomString(30) .'.txt';
		
		$result = FileManager::writeNewFile($stored_file, 'PHP Unit Tests');
		
		$this->assertTrue(file_exists($stored_file));
		
	}
	
	public function testFileContent() {
		
		$stored_file = $this -> _test_dir. 'test_contents.txt';
		
		$content = 'ProdigyView is awesome!';
		
		FileManager::writeNewFile($stored_file, $content);
		
		$read_contents = FileManager::readFile($stored_file);
		
		$this->assertEquals($read_contents, $content);
		
	}
	
	
}

<?php

use prodigyview\system\Libraries;

use PHPUnit\Framework\TestCase;

class LibrariesTests extends TestCase {
	
	protected function setUp() {
		Libraries::init();
	}
	
	public function testJavascript() {
		
		$scripts = array(
			'https://code.jquery.com/jquery-3.3.1.min.js' => 'https://code.jquery.com/jquery-3.3.1.min.js',
			'https://unpkg.com/tooltip.js' => 'https://unpkg.com/tooltip.js',
		);
		
		foreach($scripts as $script) {
			Libraries::enqueueJavascript($script);
		}//endforeach
		
		$queued_scripts = Libraries::getJavascriptQueue();
		
		$this->assertEquals($scripts, $queued_scripts);
	}
	
	public function testCss() {
		
		$scripts = array(
			'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' => 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css',
			'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css',
		);
		
		foreach($scripts as $script) {
			Libraries::enqueueCss($script);
		}//endforeach
		
		$queued_scripts = Libraries::getCssQueue();
		
		$this->assertEquals($scripts, $queued_scripts);
	}
	
	public function testLibraries() {
		
		$libraries = array(
			'library1' => array(),
		);
		
		foreach($libraries as $key => $options) {
			Libraries::addLibrary($key, $options);	
		}
		
		$this->assertTrue(true);
	}
	
}
	
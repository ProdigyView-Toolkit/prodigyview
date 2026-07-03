<?php
use prodigyview\template\Template;
use prodigyview\system\Libraries;

use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase {
	
	private $_flashMessages = array(
		array('type' => 'success', 'message' => 'First message'),
		array('type' => 'success', 'message' => 'Second message'),
		array('type' => 'danger', 'message' => 'Third message'),
		array('type' => 'danger', 'message' => 'Fourth message'),
	);
	
	public function testSiteTitle() {
		
		$string = 'Site Title';
		
		Template::setSiteTitle($string);
		
		$this -> assertEquals($string, Template::getSiteTitle());	
	}
	
	public function testSiteKeywords() {
		
		$string = 'Site Keywords';
		
		Template::setSiteKeywords($string);
		
		$this -> assertEquals($string, Template::getSiteKeywords());	
	}
	
	public function testSiteMetaDescription() {
		
		$string = 'Site Meta Description';
		
		Template::setSiteMetaDescription($string);
		
		$this -> assertEquals($string, Template::getSiteMetaDescription());	
	}
	
	public function testSiteMetaTags() {
		
		$string = 'Site Meta Tags';
		
		Template::setSiteMetaTags($string);
		
		$this -> assertEquals($string, Template::getSiteMetaTags());	
	}
	
	public function testFlashMessages() {
		
		$successes = array();
		
		foreach($this -> _flashMessages as $message) {
			Template::addFlashMessage($message['type'], $message['message']);
			
			if($message['type'] == 'success') {
				$successes[] = $message['message'];
			}
			
		}
		
		$messages = Template::getFlashMessages('success');
		
		$this->assertEquals($messages, $successes);
	}

	public function testHeaderHandlesNullLibraryQueues() {
		$this->setLibraryQueue('css_files_array', null);
		$this->setLibraryQueue('javascript_libraries_array', null);
		$this->setLibraryQueue('open_javascript', null);

		try {
			$header = Template::getHeader(array('append_url' => false));

			$this->assertSame('', $header);
		} finally {
			$this->setLibraryQueue('css_files_array', array());
			$this->setLibraryQueue('javascript_libraries_array', array());
			$this->setLibraryQueue('open_javascript', '');
		}
	}

	private function setLibraryQueue($name, $value) {
		$property = new ReflectionProperty(Libraries::class, $name);
		if (PHP_VERSION_ID < 80100) {
			$property->setAccessible(true);
		}
		$property->setValue(null, $value);
	}
		
		
}
	

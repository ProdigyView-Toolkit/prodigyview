<?php
use prodigyview\template\Template;

use PHPUnit\Framework\TestCase;

class TemplateTests extends TestCase {
	
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
		
		
}
	
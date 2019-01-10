<?php

use prodigyview\template\Html;
use prodigyview\util\Tools;

use PHPUnit\Framework\TestCase;

class HtmlTests extends TestCase {
	
	public function testStandardAttributesEmpty() {
		
		$result = Html::getStandardAttributes();
		
		$this -> assertEquals('', $result);
	}
	
	public function testStandardAttributes() {
		
		$options = array(
			'class' => 'bootstrap',
			'id' => 'div1',
			'style' => 'width:300',
			'xml:lang' => 'html5',
			'spellcheck' => 'false',
			'title' => 'Title'
		);
		
		$result = Html::getStandardAttributes($options);
		
		$this -> assertEquals('class="bootstrap" id="div1" style="width:300" xml:lang="html5" spellcheck="false" title="Title"', trim($result));
	}
	
	public function testImageFullUrl() {
		$image_url = 'https://avatars2.githubusercontent.com/u/1185218?s=200&v=4';
		
		$result = Html::image($image_url);
		
		$this -> assertEquals('<img src="'.$image_url.'" alt="" />', trim($result));
	}
	
	public function testImageLocal() {
		$image_url = '/img/test.jpeg';
		
		$result = Html::image($image_url);
		
		$this -> assertEquals('<img src="'.$image_url.'" alt="" />', trim($result));
	}
	
	public function testTimeTag() {
		$time = '10:00';
		
		$result = Html::time($time);
		
		$this -> assertEquals('<time>'.$time.'</time>', Tools::removeWhiteSpace($result));
	}
	
	public function testIframe() {
		$src = 'https://github.com/ProdigyView-Toolkit/prodigyview';
		$data = 'HeloWorld';
		
		$result = Html::iframe($src, $data);
		
		$this -> assertEquals('<iframe src="'.$src.'" >'.$data.'</iframe>', $result);
	}
	
	
	
}

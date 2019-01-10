<?php

use prodigyview\util\Tools;

use PHPUnit\Framework\TestCase;

class ToolsTests extends TestCase {

	private $_largeArray = array(
		'breakfast' => array(
			'food' => array(
				'eggs',
				'bacon'
			),
			'drinks' => array(
				'coffee',
				'tea'
			)
		),
		'dinner' => array(
			'food' => array(
				'steak',
				'bread'
			),
			'drinks' => array(
				'wine',
				'juice'
			)
		),
	);

	public function testRandomString() {

		$length = 20;

		$result = Tools::generateRandomString($length);

		$this->assertEquals(strlen($result), $length);
	}

	public function testTruncateText() {
		$length = 15;

		$text = 'Everything should be like a Tweet. No one likes long paragraphs';

		$text = Tools::truncateText($text, $length, $trailing = '---');

		$this->assertEquals('Everything shou---', $text);
	}

	public function testTruncateTextWithAllHtml() {
		$length = 15;

		$text = '<p><strong>Everything should be like a Tweet. No one likes long paragraphs</strong><p>';

		$text = Tools::truncateText($text, $length, $trailing = '-.-', false);

		$this->assertEquals('<p><strong>Ever-.-', $text);
	}

	public function testTruncateTextWithLimitedHtml() {
		$length = 15;

		$text = '<p><strong>Everything should be like a Tweet. No one likes long paragraphs</strong><p>';

		$text = Tools::truncateText($text, $length, $trailing = '...', true, '<p>');

		$this->assertEquals('<p>Everything s...', $text);
	}

	public function testArrayRecursiveFound() {

		$result = Tools::arraySearchRecursive('wine',$this -> _largeArray);
		
		$this -> assertNotEmpty($result);
	}
	
	public function testArrayRecursiveNotFound() {

		$result = Tools::arraySearchRecursive('chicken',$this -> _largeArray);
		
		$this -> assertFalse($result);
	}
	
	public function testRemoveWhiteSpace() {
		$string = "<p \t\n\r\x0B> Hello \t\n\r\x0B World</p>";
		
		$result = Tools::removeWhiteSpace($string);
		
		$this -> assertEquals("<p>HelloWorld</p>", $result);
	}
	
	public function testRemoveNonAscii() {
		$string = "<p\0’>Hello’\0World</p>";
		
		$result = Tools::removeNonAsciiCharacters($string);
		
		$this -> assertEquals("<p>HelloWorld</p>", $result);
	}
	
	public function testArrayKeyReplace() {
		
		$data = $this -> _largeArray;
		
		Tools::replaceKeyNamesInArray($data, 'breakfast', 'desayuna');
		Tools::replaceKeyNamesInArray($data, 'drinks', 'bebidas');
		
		$this->assertEquals($this -> _largeArray['breakfast']['drinks'], $data['desayuna']['bebidas']);
	}

}

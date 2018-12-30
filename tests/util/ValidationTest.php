<?php
use prodigyview\util\Validator;

use PHPUnit\Framework\TestCase;

class ValidatorTests extends TestCase {

	protected function setUp() {
		
		Validator::init();
	}

	protected function tearDown() {

	}

	/**
	 * Test Integer
	 */
	public function testIntegerText() {
		$result = Validator::check('integer', '1');
		$this->assertTrue($result);
	}

	public function testIntegerNumber() {
		$result = Validator::check('integer', 1);
		$this->assertTrue($result);
	}
	
	public function testIntegerLetter() {
		$result = Validator::check('integer', 'r');
		$this->assertFalse($result);
	}
	
	public function testIntegerDouble() {
		$result = Validator::check('integer', 3.5);
		$this->assertFalse($result);
	}
	
	/**
	 * Test Double
	 */
	public function testDoubleInteger() {
		$result = Validator::check('double', '1');
		$this->assertTrue($result);
	}
	
	public function testDoubleText() {
		$result = Validator::check('double', '3.5');
		$this->assertTrue($result);
	}

	public function testDoubleNumber() {
		$result = Validator::check('double', 3.5);
		$this->assertTrue($result);
	}
	
	public function testDoubleLetter() {
		$result = Validator::check('double', 'Me');
		$this->assertFalse($result);
	}
	
	/**
	 * Test Audio File
	 */
	public function testAudioBasic() {
		$result = Validator::check('audio_file', 'audio/basic');
		$this->assertTrue($result);
	}
	
	public function testAudioMidi() {
		$result = Validator::check('audio_file', 'audio/midi');
		$this->assertTrue($result);
	}
	
	public function testAudioMPEG() {
		$result = Validator::check('audio_file', 'audio/mpeg');
		$this->assertTrue($result);
	}
	
	public function testAudioRealAudio1() {
		$result = Validator::check('audio_file', 'audio/x-pn-realaudio');
		$this->assertTrue($result);
	}
	
	public function testAudioRealAudio2() {
		$result = Validator::check('audio_file', 'audio/x-realaudio');
		$this->assertTrue($result);
	}
	
	public function testAudioWav() {
		$result = Validator::check('audio_file', 'audio/x-wav');
		$this->assertTrue($result);
	}
	
	public function testIsMidi() {
		$result = Validator::check('midi_file', 'audio/midi');
		$this->assertTrue($result);
	}
	
	public function testNotMidi() {
		$result = Validator::check('midi_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsMP3() {
		$result = Validator::check('mp3_file', 'audio/mp3');
		$this->assertTrue($result);
	}
	
	public function testNotMP3() {
		$result = Validator::check('mp3_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsWav() {
		$result = Validator::check('wav_file', 'audio/x-wav');
		$this->assertTrue($result);
	}
	
	public function testNotWav() {
		$result = Validator::check('wav_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsaAIFF() {
		$result = Validator::check('aiff_file', 'audio/x-aiff');
		$this->assertTrue($result);
	}
	
	public function testNotAIFF() {
		$result = Validator::check('aiff_file', 'audio/mp3');
		$this->assertFalse($result);
	}
	
	public function testIsRA() {
		$result = Validator::check('ra_file', 'audio/x-realaudio');
		$this->assertTrue($result);
	}
	
	public function testNotRA() {
		$result = Validator::check('ra_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsOGA() {
		$result = Validator::check('oga_file', 'audio/ogg');
		$this->assertTrue($result);
	}
	
	public function testNotOGA() {
		$result = Validator::check('oga_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	/**
	 * Test Image Files
	 */
	 
	public function testImageBmp() {
		$result = Validator::check('image_file', 'image/bmp');
		$this->assertTrue($result);
	}
	
	public function testImageGif() {
		$result = Validator::check('image_file', 'image/gif');
		$this->assertTrue($result);
	}
	
	public function testImageJpeg() {
		$result = Validator::check('image_file', 'image/jpeg');
		$this->assertTrue($result);
	}
	
	public function testImagePng() {
		$result = Validator::check('image_file', 'image/png');
		$this->assertTrue($result);
	}

}

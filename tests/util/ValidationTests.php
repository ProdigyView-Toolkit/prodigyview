<?php
include ('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class ValidatorTests extends TestCase {

	protected function setUp() {
		
		PVValidator::init();
	}

	protected function tearDown() {

	}

	/**
	 * Test Integer
	 */
	public function testIntegerText() {
		$result = PVValidator::check('integer', '1');
		$this->assertTrue($result);
	}

	public function testIntegerNumber() {
		$result = PVValidator::check('integer', 1);
		$this->assertTrue($result);
	}
	
	public function testIntegerLetter() {
		$result = PVValidator::check('integer', 'r');
		$this->assertFalse($result);
	}
	
	public function testIntegerDouble() {
		$result = PVValidator::check('integer', 3.5);
		$this->assertFalse($result);
	}
	
	/**
	 * Test Double
	 */
	public function testDoubleInteger() {
		$result = PVValidator::check('double', '1');
		$this->assertTrue($result);
	}
	
	public function testDoubleText() {
		$result = PVValidator::check('double', '3.5');
		$this->assertTrue($result);
	}

	public function testDoubleNumber() {
		$result = PVValidator::check('double', 3.5);
		$this->assertTrue($result);
	}
	
	public function testDoubleLetter() {
		$result = PVValidator::check('double', 'Me');
		$this->assertFalse($result);
	}
	
	/**
	 * Test Audio File
	 */
	public function testAudioBasic() {
		$result = PVValidator::check('audio_file', 'audio/basic');
		$this->assertTrue($result);
	}
	
	public function testAudioMidi() {
		$result = PVValidator::check('audio_file', 'audio/midi');
		$this->assertTrue($result);
	}
	
	public function testAudioMPEG() {
		$result = PVValidator::check('audio_file', 'audio/mpeg');
		$this->assertTrue($result);
	}
	
	public function testAudioRealAudio1() {
		$result = PVValidator::check('audio_file', 'audio/x-pn-realaudio');
		$this->assertTrue($result);
	}
	
	public function testAudioRealAudio2() {
		$result = PVValidator::check('audio_file', 'audio/x-realaudio');
		$this->assertTrue($result);
	}
	
	public function testAudioWav() {
		$result = PVValidator::check('audio_file', 'audio/x-wav');
		$this->assertTrue($result);
	}
	
	public function testIsMidi() {
		$result = PVValidator::check('midi_file', 'audio/midi');
		$this->assertTrue($result);
	}
	
	public function testNotMidi() {
		$result = PVValidator::check('midi_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsMP3() {
		$result = PVValidator::check('mp3_file', 'audio/mp3');
		$this->assertTrue($result);
	}
	
	public function testNotMP3() {
		$result = PVValidator::check('mp3_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsWav() {
		$result = PVValidator::check('wav_file', 'audio/x-wav');
		$this->assertTrue($result);
	}
	
	public function testNotWav() {
		$result = PVValidator::check('wav_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsaAIFF() {
		$result = PVValidator::check('aiff_file', 'audio/x-aiff');
		$this->assertTrue($result);
	}
	
	public function testNotAIFF() {
		$result = PVValidator::check('aiff_file', 'audio/mp3');
		$this->assertFalse($result);
	}
	
	public function testIsRA() {
		$result = PVValidator::check('ra_file', 'audio/x-realaudio');
		$this->assertTrue($result);
	}
	
	public function testNotRA() {
		$result = PVValidator::check('ra_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	public function testIsOGA() {
		$result = PVValidator::check('oga_file', 'audio/ogg');
		$this->assertTrue($result);
	}
	
	public function testNotOGA() {
		$result = PVValidator::check('oga_file', 'audio/x-aiff');
		$this->assertFalse($result);
	}
	
	/**
	 * Test Image Files
	 */
	 
	public function testImageBmp() {
		$result = PVValidator::check('image_file', 'image/bmp');
		$this->assertTrue($result);
	}
	
	public function testImageGif() {
		$result = PVValidator::check('image_file', 'image/gif');
		$this->assertTrue($result);
	}
	
	public function testImageJpeg() {
		$result = PVValidator::check('image_file', 'image/jpeg');
		$this->assertTrue($result);
	}
	
	public function testImagePng() {
		$result = PVValidator::check('image_file', 'image/png');
		$this->assertTrue($result);
	}

}

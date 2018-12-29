<?php
use prodigyview\util\Cli;

use PHPUnit\Framework\TestCase;

class CliTests extends TestCase {
		
	private $_command_line_1 = array(
		'input' => '',
		'output' => '',
	);
	
	private function convertStringToArgv ($string) {
    		preg_match_all ('/(?<=^|\s)([\'"]?)(.+?)(?<!\\\\)\1(?=$|\s)/', $string, $ms);
    		return $ms;
	}
	
	public function testCommandLine1() {
		
		Cli::parse($this ->convertStringToArgv ($this -> _command_line_1['input']));
	}
}

	
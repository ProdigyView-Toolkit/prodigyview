<?php
use prodigyview\util\Cli;

use PHPUnit\Framework\TestCase;

/**
 * Run test against the CLI
 * 
 * @todo Create more tests
 */
class CliTests extends TestCase {

	private $_command_line_1 = array(
		'input' => 'file.php --file=test.text --path=none',
		'output' => array(
			'file' => 'test.text',
			'path' => 'none'
		),
	);

	private function convertStringToArgv($string) {
		preg_match_all('/(?<=^|\s)([\'"]?)(.+?)(?<!\\\\)\1(?=$|\s)/', $string, $ms);
		return $ms[2];
	}

	public function testCommandLine1() {

		$output = Cli::parse($this->convertStringToArgv($this->_command_line_1['input']));

		$this->assertTrue($this -> arrays_are_similar($output, $this->_command_line_1['output']));
	}

	/**
	 * Determine if two associative arrays are similar
	 *
	 * Both arrays must have the same indexes with identical values
	 * without respect to key ordering
	 *
	 * @param array $a
	 * @param array $b
	 * @return bool
	 * @author https://stackoverflow.com/questions/3838288/phpunit-assert-two-arrays-are-equal-but-order-of-elements-not-important
	 */
	private function arrays_are_similar($a, $b) {
		// if the indexes don't match, return immediately
		if (count(array_diff_assoc($a, $b))) {
			return false;
		}
		// we know that the indexes, but maybe not values, match.
		// compare the values between the two arrays
		foreach ($a as $k => $v) {
			if ($v !== $b[$k]) {
				return false;
			}
		}
		// we have identical indexes, and no unequal values
		return true;
	}

}

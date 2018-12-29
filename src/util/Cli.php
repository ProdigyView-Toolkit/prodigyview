<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

/**
 * Command Line Interface (CLI) utility class.
 *
 * This is an adaptation of Patrick Fisher command line parser for PHP. The class has been modified
 * to utilizes adapters, filter and observers natively present in ProdigyView.
 *
 * @author              Patrick Fisher <patrick@pwfisher.com>
 * @since               August 21, 2009
 * @see                 https://github.com/pwfisher/CommandLine.php
 *
 * This work is licensed under the Creative Commons Attribution License.
 * http://creativecommons.org/licenses/by/3.0/
 *
 * @package util
 */
class Cli {
	
	use StaticObject;

	/**
	 * Args parsed from command linke entry
	 */
	public static $args;

	/**
	 * Parse the command line arguements
	 *
	 * @param string $argv Arguements from teh command line
	 *
	 * @return array An array of items to output
	 */
	public static function parse($argv = null) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $argv);

		$argv = self::_applyFilter(get_class(), __FUNCTION__, $argv, array('event' => 'args'));

		$argv = ($argv) ? : $_SERVER['argv'];

		array_shift($argv);
		
		$out = array();

		for ($i = 0, $j = count($argv); $i < $j; $i++) {
			$arg = $argv[$i];

			// --foo --bar=baz
			if (substr($arg, 0, 2) === '--') {
				$eqPos = strpos($arg, '=');

				// --foo
				if ($eqPos === false) {
					$key = substr($arg, 2);

					// --foo value
					if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
						$value = $argv[$i + 1];
						$i++;
					} else {
						$value = isset($out[$key]) ? $out[$key] : true;
					}
					$out[$key] = $value;
				}

				// --bar=baz
				else {
					$key = substr($arg, 2, $eqPos - 2);
					$value = substr($arg, $eqPos + 1);
					$out[$key] = $value;
				}
			}

			// -k=value -abc
			else if (substr($arg, 0, 1) === '-') {
				// -k=value
				if (substr($arg, 2, 1) === '=') {
					$key = substr($arg, 1, 1);
					$value = substr($arg, 3);
					$out[$key] = $value;
				}
				// -abc
				else {
					$chars = str_split(substr($arg, 1));
					foreach ($chars as $char) {
						$key = $char;
						$value = isset($out[$key]) ? $out[$key] : true;
						$out[$key] = $value;
					}
					// -a value1 -abc value2
					if ($i + 1 < $j && $argv[$i + 1][0] !== '-') {
						$out[$key] = $argv[$i + 1];
						$i++;
					}
				}
			}

			// plain-arg
			else {
				$value = $arg;
				$out[] = $value;
			}
		}

		self::$args = $out;

		$out = self::_applyFilter(get_class(), __FUNCTION__, $out, array('event' => 'return'));

		return $out;
	}

	/**
	 * GET BOOLEAN
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @todo Revist for figure out what this function was for.
	 */
	public static function getBoolean($key, $default = false) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $key, $default);

		if (!isset(self::$args[$key])) {
			return $default;
		}
		$value = self::$args[$key];

		if (is_bool($value)) {
			return $value;
		}

		if (is_int($value)) {
			return (bool)$value;
		}

		if (is_string($value)) {
			$value = strtolower($value);
			$map = array(
				'y' => true,
				'n' => false,
				'yes' => true,
				'no' => false,
				'true' => true,
				'false' => false,
				'1' => true,
				'0' => false,
				'on' => true,
				'off' => false,
			);
			if (isset($map[$value])) {
				return $map[$value];
			}
		}

		return $default;
	}

}

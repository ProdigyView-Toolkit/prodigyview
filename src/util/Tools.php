<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

/**
 * Tools is a class that has random tools to be utilized in an application.
 *
 * The tools in this class do not have a direct affiliation with any other class and can be
 * considered more of general tools.
 *
 * Example:
 * ```php
 * //Create random string of capital letters A -F that is 10 letters long
 * $string = Tools::generateRandomString( 10, $chars = 'ABCDEF');
 *
 * //Search a recursive array
 * $data = array(
 * 	'fruits' => array('Strawberries', 'Oranges')
 * 	'vegetables' => array('celery', 'salad'),
 * 	'meat' => array(
 * 		'white' => array('chicken', 'turkey'),
 * 		'red' => array('beef', 'goat')
 * 	)
 * );
 *
 * $item = Tools::arraySearchRecursive('turkey', $data);
 * ```
 * @package util
 */
class Tools {
	
	use StaticObject;

	/**
	 * Generates a random string of lettters and numbers. String can be customized on the length
	 * and the characters used to generate the string.
	 *
	 * @param int $char_count The length of characters the string will be. Default is 15 chars
	 * @param string $chars The characters that will be used to make up the string. Default is
	 * 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'
	 *
	 * @return string $string The auto generated string
	 * @access public
	 */
	public static function generateRandomString(int $char_count = 15, string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') : string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $char_count, $chars);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'char_count' => $char_count,
			'chars' => $chars
		), array('event' => 'args'));
		
		$char_count = $filtered['char_count'];
		$chars = $filtered['chars'];

		$charLength = (strlen($chars) - 1);
		$returnString = $chars{rand(0, $charLength)};

		for ($i = 1; $i < $char_count; $i = strlen($returnString)) {

			$newchar = $chars{rand(0, $charLength)};

			if ($newchar != $returnString{$i - 1}) {
				$returnString .= $newchar;
			}
		}//end for

		self::_notify(get_class() . '::' . __FUNCTION__, $returnString, $char_count, $chars);
		$returnString = self::_applyFilter(get_class(), __FUNCTION__, $returnString, array('event' => 'return'));

		return $returnString;
	}

	/**
	 * Truncates a strings of text to a certain length and applies trailing characters. Generally used
	 * for
	 * creating 'Read More...' text descrptions.
	 *
	 * @param string $string The string to truncate
	 * @param int $length The length to truncate the string too. Default is 10 characters.
	 * @param string $trailing Trailing text to add at the end of string once it is truncated. Default
	 * text is '...'
	 * @param boolean $strip_tags Strips out any html tags. Default is true.
	 * @param string $allowed_tags Tags to allow if strip_tags is set to true.
	 *
	 * @return string $truncated A The string when truncated
	 * @access public
	 */
	public static function truncateText(string $string, int $length = 10, string $trailing = '...', bool $strip_tags = TRUE, string $allowed_tags = '') : string {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $string, $length, $trailing, $strip_tags, $allowed_tags);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'string' => $string,
			'length' => $length,
			'trailing' => $trailing,
			'strip_tags' => $strip_tags,
			'allowed_tags' => $allowed_tags
		), array('event' => 'args'));
		
		$string = $filtered['string'];
		$length = $filtered['length'];
		$trailing = $filtered['trailing'];
		$strip_tags = $filtered['strip_tags'];
		$allowed_tags = $filtered['allowed_tags'];

		if ($strip_tags === TRUE && !empty($string)) {
			$string = strip_tags($string, $allowed_tags);
		}

		$truncated = '';

		if (mb_strlen($string) > $length) {
			$truncated = mb_substr($string, 0, $length) . $trailing;
		} else {
			$truncated = $string . $trailing;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $truncated, $string, $length, $trailing, $strip_tags, $allowed_tags);
		$truncated = self::_applyFilter(get_class(), __FUNCTION__, $truncated, array('event' => 'return'));

		return $truncated;
	}//end truncateText
	
	
	/**
	 * Removes whitespace from a string. Whitespace include tabs, newlines, blank space, etc.
	 * 
	 * @param string $string The string to remove whitespace
	 *
	 * @return string 
	 */
	public static function removeWhiteSpace(string $string) : string {
		
		$string = preg_replace('/\s+/', '', $string);
		
		return $string;
	}

	/**
	 * Removes non-ASCII (characters that cannot be printed) from the string
	 * 
	 * @param string $string The string to remove characters from
	 *
	 * @return string 
	 */
	public static function removeNonAsciiCharacters(string $string) : string {
		
		$string = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string);
		
		return $string;
	}

	/**
	 * Searched for an value within another array recursively.
	 *
	 * @param mixed $needle The needle can either be a value or an array of values to be searched for
	 * @param array $haystack The array to be search in
	 * @param boolean $strict Sets if comparison is performed loosely or tightly
	 * @param array $path The path in the array in which the needle was found
	 *
	 * @return mixed $path Returns a path if the array was found, otherwise returns false
	 * @access public
	 */
	public static function arraySearchRecursive(string $needle, array $haystack, bool $strict = false, array $path = array()){

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $needle, $haystack, $strict, $path);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'needle' => $needle,
			'haystack' => $haystack,
			'strict' => $strict,
			'path' => $path
		), array('event' => 'args'));
		
		$needle = $filtered['needle'];
		$haystack = $filtered['haystack'];
		$strict = $filtered['strict'];
		$path = $filtered['path'];

		if (!is_array($haystack)) {
			return false;
		}

		if (is_array($needle)) {
			foreach ($needle as $point) {
				$return = self::arraySearchRecursive($point, $haystack, $strict, $path);
				if (!empty($return))
					$path[] = $return;
			}//dnc vpfdz h

			if (!empty($path))
				return $path;

			return false;
		}

		foreach ($haystack as $key => $value) {
			if (is_array($value) && $sub_path = self::arraySearchRecursive($needle, $value, $strict, $path)) {
				$path = array_merge($path, array($key), $sub_path);
				return $path;
			} else if ((!$strict && $value == $needle) || ($strict && $value === $needle)) {
				$path[] = $key;
				return $path;
			}
		}
		return false;
	}

	/**
	 * Searches through an arrays keys and changes the names of certian keys when found. The function
	 * uses str_replace, meaning partial keys are searchable
	 * 
	 * @param array $array The array to search through
	 * @param string $search The partial key name to search for.
	 * @param string $replace The text to replace the key name with
	 * 
	 * @return $array This function is pass by reference, return is not needed
	 */
	public static function replaceKeyNamesInArray(array &$array, string $search, string $replace) {
		
		foreach($array as $key => $value) {
			
			if (strpos($key, $search) !== false) {
				$new_key = str_replace($search, $replace, $key);
				$array[$new_key] = $value;
				unset($array[$key]);
			}
			
			if(isset($array[$key]) && is_array($value)) {
				self::replaceKeyNamesInArray($array[$key], $search, $replace);
			}
			
		}//endforeach
		
		return $array;
	}

}//end tools
?>

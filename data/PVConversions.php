<?php
/**
 *PVConversions is a class used to convert one data type to another.
 * 
 * Often there will be requirements for converting data such as array to objects, json to xml, etc. This class is designed to have built-in functions to make those conversations easy.
 * 
 * Example:
 * //Create an array
 * $data = $array('Apple', 'Bananna', 'Orange');
 * 
 * //Convert the array to object
 * $data = PVConversions::arrayToObject($data);
 * 
 * //Will display an stdObject
 * print_r($data);
 * 
 * @package data
 */
class PVConversions {

	/**
	 * Converts an array to an object using the stdClass,
	 *
	 * @param array $data An array of data
	 *
	 * @return stdClass $data The return array in an object format
	 * @access public
	 */
	public static function arrayToObject($data) {
		if (!is_array($data)) {
			return $data;
		}

		$object = new stdClass();

		if (is_array($data) && count($data) > 0) {

			foreach ($data as $name => $value)
				$object -> $name = self::arrayToObject($value);

			return $object;
		} else if (is_array($data) && count($data) === 0) {
			return (object) $data;
		} else {
			
			return FALSE;
		}
	}

	/**
	 * Converts an object to type array. Keep in mind that that private and protected
	 * variables may not be returned
	 *
	 * @param object $object An object
	 *
	 * @return array $array, The passed object in array formart/
	 * @access public
	 */
	public static function objectToArray($object) {
		if (!is_object($object) && !is_array($object)) {
			return $object;
		}

		if (is_object($object)) {
			$object = get_object_vars($object);
		}
		return array_map('PVConversions::objectToArray', $object);
	}

	/**
	 * Converts an xml document into an array.
	 *
	 * @param @string xml A string of xml to convert to an array
	 *
	 * @return array $array The xml documented converted into an array
	 * @access public
	 */
	public static function xmlToArray($xml) {
		if (empty($xml))
			return false;
		$object = simplexml_load_string($xml);
		return get_object_vars($object);
	}

}//end class

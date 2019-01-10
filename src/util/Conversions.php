<?php
namespace prodigyview\util;

/**
 * Conversions is a class used to convert one data type to another.
 *
 * Often there will be requirements for converting data such as array to objects, json to xml, etc.
 * This class is designed to have built-in functions to make those conversations easy.
 *
 * Example:
 * 
 * ```php
 * //Create an array
 * $data = $array('Apple', 'Bananna', 'Orange');
 *
 * //Convert the array to object
 * $data = Conversions::arrayToObject($data);
 *
 * //Will display an stdObject
 * print_r($data);
 * ```
 * @package data
 */
class Conversions {

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

		$object = new \stdClass();

		if (is_array($data) && count($data) > 0) {

			foreach ($data as $name => $value)
				$object->$name = self::arrayToObject($value);

			return $object;
		} else if (is_array($data) && count($data) === 0) {
			return (object)$data;
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
		return array_map('prodigyview\util\Conversions::objectToArray', $object);
	}

	/**
	 * Converts an xml document into an array.
	 *
	 * @param @string xml A string of xml to convert to an array
	 *
	 * @return array $array The xml documented converted into an array
	 * @access public
	 */
	public static function xmlToArray(string $xml) : array {
		
		if (empty($xml))
			return false;
		
		$object = simplexml_load_string($xml);
		
		$data = get_object_vars($object);
		
		foreach($data as $key => $value) {
			if($value instanceof \SimpleXMLElement) {
				$data[$key] = self::xmlToArray($value -> asXML());
			}
		}//end foreach
		
		return $data;
	}
	
	/**
	 * Converts an array to a XML object
	 * 
	 * @param array $data The array to be converted to XML
	 * @param SimpleXMLElement $xml_data Example data to be mmodified
	 * @param string $numeric_key_name Given the numeric values are not allowed in XML, select a prefix for numeric values
	 * @param string $simple_xml_data Parent root element for new xml data
	 * 
	 * @return string $xml
	 */
	public static function arrayToXml(array $data, \SimpleXMLElement &$xml_data = null, string $numeric_key_name = 'item_' , string $simple_xml_data = '<root/>') : string {
		
		if(!$xml_data) {
			$xml_data = new \SimpleXMLElement($simple_xml_data);
		}
		
		foreach($data as $key => $value ) {
	        if( is_numeric($key) ){
	            $key = $numeric_key_name.$key; //dealing with <0/>..<n/> issues
	        }
	        if( is_array($value) ) {
	            $xml_data->addChild($key);
	            self::arrayToXml($value, $xml_data -> $key, $numeric_key_name);
	        } else {
	            $xml_data->addChild("$key",htmlspecialchars("$value"));
	        }
	     }//end foreach
		
		return trim(str_replace("\n", '',$xml_data->asXML()));
	}
	
	/**
	 * Encodes a string, array or object to a specified encoding. Uses pass by
	 * reference, so no return value.
	 * 
	 * @param mixed $input The string, array or object that is passed by reference
	 * @param string $encoding The encoding to use, default is UTF-8
	 * 
	 * @return void
	 */
	public static function encodeRecursive(&$input, string $encoding = 'UTF-8') : void {
		
		 if (is_string($input)) {
	        $input = mb_convert_encoding ($input, $encoding);
	    } else if (is_array($input)) {
	        foreach ($input as &$value) {
	            self::encodeRecursive($value, $encoding);
	        }
			
	        unset($value);
	    } else if (is_object($input)) {
	        $vars = array_keys(get_object_vars($input));
	
	        foreach ($vars as $var) {
	            self::encodeRecursive($input->$var, $encoding);
	        }
	    }
	}

	/**
	 * Converts a boolean that is passed a string to the boolean type true or false.
	 *
	 * @param string $boolean The boolean as a string
	 *
	 * @return boolean
	 */
	public static function convertTextBoolean(string $boolean) : bool {
		
		if ($boolean === 'true') {
			return true;
		} else if ($boolean === 'false') {
			return false;
		}

		return $boolean;
	}//end convertTextBoolean
	
	

}//end class

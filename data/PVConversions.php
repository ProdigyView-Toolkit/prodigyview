<?php
/*
*Copyright 2011 ProdigyView LLC. All rights reserved.
*
*Redistribution and use in source and binary forms, with or without modification, are
*permitted provided that the following conditions are met:
*
*   1. Redistributions of source code must retain the above copyright notice, this list of
*      conditions and the following disclaimer.
*
*   2. Redistributions in binary form must reproduce the above copyright notice, this list
*      of conditions and the following disclaimer in the documentation and/or other materials
*      provided with the distribution.
*
*THIS SOFTWARE IS PROVIDED BY My-Lan AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL My-Lan OR
*CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
*CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
*SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
*ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
*NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
*ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*The views and conclusions contained in the software and documentation are those of the
*authors and should not be interpreted as representing official policies, either expressed
*or implied, of ProdigyView LLC.
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
    	if(!is_array($data)) {
        	return $data;
   		 }
    
	    $object = new stdClass();
	    
		if(is_array($data) && count($data) > 0) {
				
			foreach ($data as $name=>$value) {
				$name = strtolower(trim($name));
					if (!empty($name)) {
						$object->$name = self::arrayToObject($value);
					}
			}//end foreach
			
			return $object;
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
	 public static function objectToArray( $object ) {
        if( !is_object( $object ) && !is_array( $object ) ) {
            return $object;
        }
		
        if( is_object( $object ) ) {
            $object = get_object_vars( $object );
        }
        return array_map( 'PVConversions::objectToArray', $object );
    }
}//end class

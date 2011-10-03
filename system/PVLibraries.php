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

class PVLibraries extends PVStaticObject{
	
	private static $javascript_libraries_array;
	private static $jquery_libraries_array;
	private static $prototype_libraries_array;
	private static $motools_libraries_array;
	private static $css_files_array;
	private static $open_javascript;
	
	function __construct(){
		
	}
	
	function init(){
		$config=PVConfiguration::getSiteCompleteConfiguration();
		
		self::$javascript_libraries_array=array();
		self::$jquery_libraries_array=array();
		self::$prototype_libraries_array=array();
		self::$motools_libraries_array=array();
		self::$css_files_array=array();
	}
	
	/**
	 * Adds javascript files to a queue of javascript files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 * 
	 * @param string script
	 */
	public static function enqueue_javascript($script){
		self::$javascript_libraries_array[$script]=$script;
	}
	
	/**
	 * Adds jquery files to a queue of jquery files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 * 
	 * @param string script
	 */
	public static function enqueue_jquery($script){
		self::$jquery_libraries_array[$script]=$script;
	}
	
	/**
	 * Adds prototype files to a queue of prototype files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 * 
	 * @param string script
	 */
	public static function enqueue_prototype($script){
		self::$prototype_libraries_array[$script]=$script;
	}
	
	/**
	 * Adds mootools files to a queue of mootools files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 * 
	 * @param string script
	 */
	public static function enqueue_mootools($script){
		self::$motools_libraries_array[$script]=$script;
	}
	
	/**
	 * Adds css files to a queue of css files. The name of the
	 * file should be unique and set the path of the file or the url of the file.
	 * 
	 * @param string script
	 */
	public static function enqueue_css($script){
		self::$css_files_array[$script]=$script;
	}
	
	/**
	 * Adds a script directly into a buffer to be outputted later.
	 * The script should be input with opening and closing tags.
	 * 
	 * Example
	 * $string='<script type="text/javascript">alert("Test");</script>';
	 * PVLibraries::enqueue_openscript($string);
	 * 
	 * @param string $script
	 */
	public static function enqueue_openscript($script){
		self::$open_javascript.=$script;
	}
	
	/**
	 * Returns javascript file locations that have been inserted
	 * into the queue.
	 * 
	 * @return array script_array
	 */
	public static function get_enqueue_javascript(){
		return self::$javascript_libraries_array;
	}
	
	/**
	 * Returns jquery file locations that have been inserted
	 * into the queue.
	 * 
	 * @return array script_array
	 */
	public static function get_enqueue_jquery(){
		return self::$jquery_libraries_array;
	}
	
	/**
	 * Returns prototype file locations that have been inserted
	 * into the queue.
	 * 
	 * @return array script_array
	 */
	public static function get_enqueue_prototype(){
		return self::$prototype_libraries_array;
	}
	
	/**
	 * Returns mootools file locations that have been inserted
	 * into the queue.
	 * 
	 * @return array script_array
	 */
	public static function get_enqueue_mootools(){
		return self::$motools_libraries_array;
	}
	
	/**
	 * Returns css file locations that have been inserted
	 * into the queue.
	 * 
	 * @return array script_array
	 */
	public static function get_enqueue_css(){
		return self::$css_files_array;
	}
	
	
	public static function get_enqueue_openscript(){
		return self::$open_javascript;
	}
	
	
	
}//end class
	
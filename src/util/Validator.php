<?php

namespace prodigyview\util;

use prodigyview\design\StaticObject;

/**
 * Validator is a dynamically extendable class used to validate inputs.
 *
 * The class can be used to check for a variety of inputs to validate data from mime types to correct
 * syntax for a URL. The class is also extendable to add more validation rules.
 *
 * Examples:
 * ```php
 * //Check if a file is an integer
 *
 * if(Validator::check('integer', '3.4')) {
 * 	echo 'I am an integer';
 * }
 *
 * if(Validator::check('url', 'http://www.google.com')) {
 *     echo 'I am a valid url';
 * }
 *
 * //Add custom validation rule
 * Validator::addRule('is_currency', array('function' => function($number) {
 *     return preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $number);
 * }));
 *
 * //Check against custom rule
 * if(Validator::check(‘is_currecny’, '$10.00')) {
 * 	echo 'I am currency';
 * }
 * ```
 *
 * @package util
 */
class Validator {
	
	use StaticObject;

	/**
	 * An array of stored rules and the functions to validate those rules.
	 */
	private static $rules;
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initializes Validators and sets the default for checking rules. The defaults allows the function
	 * Validator::check() to function propery.
	 *
	 * @param array $config The configuration for init the class
	 *
	 * @return void
	 * @access public
	 * @todo consider extending the config optiont actually have it doing something
	 */
	public static function init(array $config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);

		if(!self::$_initialized) {
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$rules = array();
	
			self::$rules['id'] = array(
				'type' => 'validator',
				'function' => 'isID'
			);
			
			self::$rules['integer'] = array(
				'type' => 'validator',
				'function' => 'isInteger'
			);
			
			self::$rules['double'] = array(
				'type' => 'validator',
				'function' => 'isDouble'
			);
	
			//Audio File Validation
			self::$rules['audio_file'] = array(
				'type' => 'validator',
				'function' => 'isAudioFile'
			);
			
			self::$rules['midi_file'] = array(
				'type' => 'validator',
				'function' => 'isMidiFile'
			);
			
			self::$rules['mp3_file'] = array(
				'type' => 'validator',
				'function' => 'isMpegAudioFile'
			);
			
			self::$rules['wav_file'] = array(
				'type' => 'validator',
				'function' => 'isWavFile'
			);
			
			self::$rules['aiff_file'] = array(
				'type' => 'validator',
				'function' => 'isAiffFile'
			);
			
			self::$rules['ra_file'] = array(
				'type' => 'validator',
				'function' => 'isRealAudioFile'
			);
			
			self::$rules['oga_file'] = array(
				'type' => 'validator',
				'function' => 'isOGGAudioFile'
			);
	
			//Image File Validation
			self::$rules['image_file'] = array(
				'type' => 'validator',
				'function' => 'isImageFile'
			);
			
			self::$rules['bmp_file'] = array(
				'type' => 'validator',
				'function' => 'isBmpFile'
			);
			
			self::$rules['jpg_file'] = array(
				'type' => 'validator',
				'function' => 'isJpegFile'
			);
			
			self::$rules['png_file'] = array(
				'type' => 'validator',
				'function' => 'isPngFile'
			);
			
			self::$rules['gif_file'] = array(
				'type' => 'validator',
				'function' => 'isGifFile'
			);
	
			//Video File Validation
			self::$rules['video_file'] = array(
				'type' => 'validator',
				'function' => 'isVideoFile'
			);
			self::$rules['mpeg_file'] = array(
				'type' => 'validator',
				'function' => 'isMpegVideoFile'
			);
			
			self::$rules['quicktime_file'] = array(
				'type' => 'validator',
				'function' => 'isQuickTimeFile'
			);
			
			self::$rules['mov_file'] = array(
				'type' => 'validator',
				'function' => 'isMovFile'
			);
			
			self::$rules['avi_file'] = array(
				'type' => 'validator',
				'function' => 'isAviFile'
			);
			
			self::$rules['ogv_file'] = array(
				'type' => 'validator',
				'function' => 'isOGGVideoFile'
			);
	
			//Compressed File
			self::$rules['compressed_file'] = array(
				'type' => 'validator',
				'function' => 'isCompressedFile'
			);
			
			self::$rules['zip_file'] = array(
				'type' => 'validator',
				'function' => 'isZipFile'
			);
			self::$rules['tar_file'] = array(
				'type' => 'validator',
				'function' => 'isTarFile'
			);
			
			self::$rules['gtar_file'] = array(
				'type' => 'validator',
				'function' => 'isGTarFile'
			);
	
			//Other Validators
			self::$rules['url'] = array(
				'type' => 'validator',
				'function' => 'isValidUrl'
			);
			
			self::$rules['active_url'] = array(
				'type' => 'validator',
				'function' => 'isActiveUrl'
			);
			
			self::$rules['email'] = array(
				'type' => 'validator',
				'function' => 'isValidEmail'
			);
			
			self::$rules['notempty'] = array(
				'type' => 'preg_match',
				'rule' => '/[^\s]+/m'
			);
	
			//Other File Validiation
			self::$rules['css_file'] = array(
				'type' => 'validator',
				'function' => 'isCssFile'
			);
			
			self::$rules['html_file'] = array(
				'type' => 'validator',
				'function' => 'isHtmlFile'
			);
			
			self::$rules['htm_file'] = array(
				'type' => 'validator',
				'function' => 'isHtmFile'
			);
			
			self::$rules['asc_file'] = array(
				'type' => 'validator',
				'function' => 'isAscFile'
			);
			
			self::$rules['text_file'] = array(
				'type' => 'validator',
				'function' => 'isTxtFile'
			);
			
			self::$rules['richtext_file'] = array(
				'type' => 'validator',
				'function' => 'isRtxFile'
			);
	
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
		
			self::$_initialized = true;
		}
	}

	/**
	 * Add a rule to the validator or modify a current one with the name name. Checks can either be
	 * closures(PHP 5.3)
	 * or preg_match, or calls to other function.
	 *
	 * @param string $rule The name of the rule
	 * @param array $options Options that define the rule
	 * 			-'type' _string_: The type of validation to perform. There are currently 4 supported types.
	 * 			1. 'closures' If you are in php 5.3, a closure function can be passed and validated against
	 * 			2. 'preg_match' Validation will be peformoned using a preg_match. Rule must be passed in.
	 * 			3. 'function' A php function that is stores in a string and called. Create the function using
	 * 'create_function' method
	 * 			4. 'validator' Calls a function in the validator to be exectued
	 * 			-'rule' _string_: A rule to be checked against if the type is a preg_match
	 * 			-'function' _mixed_: Either a string that is a function or an annoymous function.
	 *
	 * @return void
	 * @access public
	 */
	public static function addRule(string $rule, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $rule, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'rule' => $rule,
			'options' => $options
		), array('event' => 'args'));
		
		$rule = $filtered['rule'];
		$options = $filtered['options'];

		$defaults = array(
			'type' => 'closure',
			'function' => '',
			'rule' => '',
		);

		$options += $defaults;
		self::$rules[$rule] = $options;

		self::_notify(get_class() . '::' . __FUNCTION__, $rule, $options);
	}

	/**
	 * Checks a value passed to a rule if the rule exist. If there is no rule, true will be returned.
	 *
	 * @param string $rule The name of the rule to check against
	 * @param array $value The value to check against the rule
	 *
	 * @return mixed $validate Validates is generally a boolean and returns true or false
	 * @access public
	 */
	public static function check(string $rule) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $rule);

		$args = func_get_args();
		array_shift($args);

		$passable_args = array();
		foreach ($args as $key => &$arg) {
			$passable_args[$key] = &$arg;
		}

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'rule' => $rule,
			'passasbe_args' => $passable_args
		), array('event' => 'args'));
		
		$rule = $filtered['rule'];
		$passable_args = $filtered['passasbe_args'];
		$validation = false;

		if (!isset(self::$rules[$rule])) {
			$validation = false;
		}

		if (self::$rules[$rule]['type'] == 'validator') {
			$validation = self::_invokeStaticMethod('prodigyview\util\Validator', self::$rules[$rule]['function'], $passable_args);
		} else if (self::$rules[$rule]['type'] == 'preg_match') {
			$validation = preg_match(self::$rules[$rule]['rule'], $passable_args[0]);
		} else if (self::$rules[$rule]['type'] == 'function') {
			$function = self::$rules[$rule]['function'];
			$validation = call_user_func_array($function, $passable_args);
		} else if (self::$rules[$rule]['type'] == 'closure') {
			$function = self::$rules[$rule]['function'];
			$validation = call_user_func_array($function, $passable_args);
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $rule);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end check

	/**
	 * Checks if a value passed is of type int or an integer.
	 *
	 * @param mixed $int The value to check if it is an integer
	 *
	 * @return boolean $valid Returns true if the value is an integer, otherwise false
	 * @access public
	 */
	public static function isInteger($int) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $int);

		$int = self::_applyFilter(get_class(), __FUNCTION__, $int, array('event' => 'args'));
		$validation = false;

		if (is_numeric($int) === TRUE) {
			if ((int)$int == $int) {
				$validation = true;
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $int);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isInteger

	/**
	 * Checks if a value passed is of type int or an integer.
	 *
	 * @param mixed $double The value to check if it is an double
	 *
	 * @return boolean $valid Returns true if the value is an integer, otherwise false
	 * @access public
	 */
	public static function isDouble($double) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $double);

		$double = self::_applyFilter(get_class(), __FUNCTION__, $double, array('event' => 'args'));
		$validation = false;

		if (is_numeric($double) === TRUE) {
			if ((double)$double == $double) {
				$validation = true;
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $double);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isInteger

	/**
	 * Checks if a value passed is of type an ID. ID's are numeric are in the format of a MongoID
	 *
	 * @param mixed $id The value to check if it is an id
	 *
	 * @return boolean $valid Returns true if the value is an id, otherwise false
	 * @access public
	 */
	public static function isID($id) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $id);

		$id = self::_applyFilter(get_class(), __FUNCTION__, $id, array('event' => 'args'));
		$validation = false;

		if (self::isInteger($id) || preg_match('{[0-9a-f]{24}}', $id)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $id);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}

	/**
	 * Checks if a value passed has an audio mime type.
	 *
	 * @param mixed $mimetype The value to check if it is an audio mime type
	 *
	 * @return boolean $valid Returns true if the value is an audio mime type, otherwise false
	 * @access public
	 */
	public static function isAudioFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));

		$audio_types = array(
			'audio/basic',
			'audio/midi',
			'audio/mpeg',
			'audio/x-aiff',
			'audio/x-mpegurl',
			'audio/x-pn-realaudio',
			'audio/x-realaudio',
			'audio/x-wav'
		);
		
		$validation = false;

		if (in_array(strtolower($mimetype), $audio_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isAudioFile

	/**
	 * Checks if a value passed has a midi mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a midi mime type
	 *
	 * @return boolean $valid Returns true if the value is a midi mime type, otherwise false
	 * @access public
	 */
	public static function isMidiFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'audio/midi') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a mpeg audio mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a mpeg audio mime type
	 *
	 * @return boolean $valid Returns true if the value is a mpeg audio mime type, otherwise false
	 * @access public
	 */
	public static function isMpegAudioFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'audio/mpeg' || $mimetype == 'audio/mp3') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a aiff audio mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a aiff audio mime type
	 *
	 * @return boolean $valid Returns true if the value is a aiff audio mime type, otherwise false
	 * @access public
	 */
	public static function isAiffFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'audio/x-aiff') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a wav audio mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a wav audio mime type
	 *
	 * @return boolean $valid Returns true if the value is a wav audio mime type, otherwise false
	 * @access public
	 */
	public static function isWavFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'audio/x-wav') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a real audio mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a real audio mime type
	 *
	 * @return boolean $valid Returns true if the value is a real audio mime type, otherwise false
	 * @access public
	 */
	public static function isRealAudioFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'audio/x-realaudio') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a ogg audio mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a ogg audio mime type
	 *
	 * @return boolean $valid Returns true if the value is a ogg audio mime type, otherwise false
	 * @access public
	 */
	public static function isOGGAudioFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'audio/ogg' || $mimetype == 'application/ogg') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has an imagemime type.
	 *
	 * @param mixed $mimetype The value to check if it is an image mime type
	 *
	 * @return boolean $valid Returns true if the value is an image mime type, otherwise false
	 * @access public
	 */
	public static function isImageFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		$image_types = array(
			'image/bmp',
			'image/gif',
			'image/ief',
			'image/jpeg',
			'image/png',
			'image/tiff',
			'image/pjpeg',
			'image/x-png'
		);

		if (in_array(strtolower($mimetype), $image_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a bmp image mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a bmp image mime type
	 *
	 * @return boolean $valid Returns true if the value is a bmp image mime type, otherwise false
	 * @access public
	 */
	public static function isBmpFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'image/bmp') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a gif image mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a gif image mime type
	 *
	 * @return boolean $valid Returns true if the value is a gif image mime type, otherwise false
	 * @access public
	 */
	public static function isGifFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'image/gif') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a ief image mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a ief image mime type
	 *
	 * @return boolean $valid Returns true if the value is a ief image mime type, otherwise false
	 * @access public
	 */
	public static function isIefFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'image/ief') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a jpeg image mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a jpeg image mime type
	 *
	 * @return boolean $valid Returns true if the value is a jpeg image mime type, otherwise false
	 * @access public
	 */
	public static function isJpegFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'image/jpeg' || $mimetype == 'image/pjpeg') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a png image mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a png image mime type
	 *
	 * @return boolean $valid Returns true if the value is a png image mime type, otherwise false
	 * @access public
	 */
	public static function isPngFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'image/png' || $mimetype == 'image/x-png') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a tiff image mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a tiff image mime type
	 *
	 * @return boolean $valid Returns true if the value is a tiff image mime type, otherwise false
	 * @access public
	 */
	public static function isTiffFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'image/tiff') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a video mime type
	 *
	 * @return boolean $valid Returns true if the value is a video mime type, otherwise false
	 * @access public
	 */
	public static function isVideoFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		$video_types = array(
			'video/mpeg',
			'video/quicktime',
			'video/vnd.mpegurl',
			'video/x-msvideo',
			'video/x-sgi-movie',
			'video/mp4',
			'video/ogg',
			'video/webm',
			'video/x-ms-wmv',
			'application/x-troff-msvideo',
			'video/avi',
			'video/msvideo',
			'video/mp4',
			'application/mp4',
			'application/vnd.rn-realmedia',
			'video/x-ms-asf',
			'video/ogg',
			'application/ogg',
			'video/webm',
			'video/x-flv'
		);

		if (in_array(strtolower($mimetype), $video_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a mpeg video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a mpeg video mime type
	 *
	 * @return boolean $valid Returns true if the value is a mpeg video mime type, otherwise false
	 * @access public
	 */
	public static function isMpegVideoFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/mpeg') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a wmv video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a wmv video mime type
	 *
	 * @return boolean $valid Returns true if the value is a wmv video mime type, otherwise false
	 * @access public
	 */
	public static function isWmvFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/x-ms-wmv') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a mp4 video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a mp4 video mime type
	 *
	 * @return boolean $valid Returns true if the value is a mp4 video mime type, otherwise false
	 * @access public
	 */
	public static function isMp4File($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/mp4' || $mimetype == 'application/mp4') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a flv video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a flv video mime type
	 *
	 * @return boolean $valid Returns true if the value is a flv video mime type, otherwise false
	 * @access public
	 */
	public static function isFlvFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/x-flv') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a quick time video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a quick time video mime type
	 *
	 * @return boolean $valid Returns true if the value is a quick time video mime type, otherwise false
	 * @access public
	 */
	public static function isQuickTimeFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/quicktime') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a mov video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a mov video mime type
	 *
	 * @return boolean $valid Returns true if the value is a mov video mime type, otherwise false
	 * @access public
	 */
	public static function isMovFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/quicktime') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a mux video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a mux video mime type
	 *
	 * @return boolean $valid Returns true if the value is a mux video mime type, otherwise false
	 * @access public
	 */
	public static function isMxuFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/vnd.mpegurl') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a avi video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a avi video mime type
	 *
	 * @return boolean $valid Returns true if the value is a avi video mime type, otherwise false
	 * @access public
	 */
	public static function isAviFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/x-msvideo' || $mimetype == 'video/avi' || $mimetype == 'video/msvideo' || $mimetype == 'application/x-troff-msvideo') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has an ogg video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is an ogg video mime type
	 *
	 * @return boolean $valid Returns true if the value is an ogg video mime type, otherwise false
	 * @access public
	 */
	public static function isOGGVideoFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/ogg' || $mimetype == 'application/ogg') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a real media video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a real media video mime type
	 *
	 * @return boolean $valid Returns true if the value is a real media video mime type, otherwise false
	 * @access public
	 */
	public static function isRealMediaFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'application/vnd.rn-realmedia') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a asf video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a asf video mime type
	 *
	 * @return boolean $valid Returns true if the value is a asf video mime type, otherwise false
	 * @access public
	 */
	public static function isAsfFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		
		$validation = false;

		if ($mimetype == 'video/x-ms-asf') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a webm video mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a webm video mime type
	 *
	 * @return boolean $valid Returns true if the value is a webm video mime type, otherwise false
	 * @access public
	 */
	public static function isWebMFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'video/webm') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a compressed file(zip, tar, gtar) mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a compressed file mime type
	 *
	 * @return boolean $valid Returns true if the value is a compressed file mime type, otherwise false
	 * @access public
	 */
	public static function isCompressedFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		$file_types = array(
			'application/zip',
			'application/x-gtar',
			'application/x-tar',
			'application/x-zip'
		);

		if (in_array(strtolower($mimetype), $file_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a zip file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a zip file mime type
	 *
	 * @return boolean $valid Returns true if the value is a zip file mime type, otherwise false
	 * @access public
	 */
	public static function isZipFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/zip' || $mimetype == 'application/x-zip') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a gtar file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a gtar file mime type
	 *
	 * @return boolean $valid Returns true if the value is a gtar file mime type, otherwise false
	 * @access public
	 */
	public static function isGTarFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/x-gtar') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a tar file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a tar file mime type
	 *
	 * @return boolean $valid Returns true if the value is a tar file mime type, otherwise false
	 * @access public
	 */
	public static function isTarFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/x-tar') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a css (Cascading Style Sheet) file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a css file mime type
	 *
	 * @return boolean $valid Returns true if the value is a css file mime type, otherwise false
	 * @access public
	 */
	public static function isCssFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'text/css') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a html file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a html file mime type
	 *
	 * @return boolean $valid Returns true if the value is a html file mime type, otherwise false
	 * @access public
	 */
	public static function isHtmlFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'text/html') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a htm file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a htm file mime type
	 *
	 * @return boolean $valid Returns true if the value is a htm file mime type, otherwise false
	 * @access public
	 */
	public static function isHtmFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'text/html') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a gtar file mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a gtar file mime type
	 *
	 * @return boolean $valid Returns true if the value is a gtar file mime type, otherwise false
	 * @access public
	 */
	public static function isAscFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'text/plain') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a text/.txt mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a text/.txt file mime type
	 *
	 * @return boolean $valid Returns true if the value is a text/.txt file mime type, otherwise false
	 * @access public
	 */
	public static function isTxtFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'text/plain') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a rich text mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a rich text file mime type
	 *
	 * @return boolean $valid Returns true if the value is a rich textfile mime type, otherwise false
	 * @access public
	 */
	public static function isRtxFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'text/richtext') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a MS Word mime type. This will check for both doc and docx files.
	 *
	 * @param mixed $mimetype The value to check if it is a MS Word file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Word file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftWordFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		$file_types = array(
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		);

		if (in_array(strtolower($mimetype), $file_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMicrosoftWordFile

	/**
	 * Checks if a value passed has a MS Word mime type. This will only check for .doc files.
	 *
	 * @param mixed $mimetype The value to check if it is a MS Word file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Word file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftWordDocFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/msword') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a MS Word mime type. This will check for only .docx files
	 *
	 * @param mixed $mimetype The value to check if it is a MS Word file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Word file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftWordDocxFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a MS Excel mime type. Checks for .xsl and .xsls files
	 *
	 * @param mixed $mimetype The value to check if it is a MS Excel file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Excel file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftExcelFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		$file_types = array(
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		);

		if (in_array(strtolower($mimetype), $file_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMicrosoftWordFile

	/**
	 * Checks if a value passed has a MS Excel mime type. Checks only for .xsl file
	 *
	 * @param mixed $mimetype The value to check if it is a MS Excel file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Excel file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftExcelXLSFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/vnd.ms-excel') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a MS Excel mime type. Checks only for .xslx files
	 *
	 * @param mixed $mimetype The value to check if it is a MS Excel file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Excel file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftExcelXLSXFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a MS PowerPoint mime type. Check for both .ppt and .pptx files
	 *
	 * @param mixed $mimetype The value to check if it is a MS Powerpoint file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Pwerpoint file mime type, otherwise false
	 * @access public
	 */
	public static function isMicrosoftPowerPointFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		$file_types = array(
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation'
		);

		if (in_array(strtolower($mimetype), $file_types)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMicrosoftWordFile

	/**
	 * Checks if a value passed has a MS PowerPoint mime type. Checks only for .ppt files
	 *
	 * @param mixed $mimetype The value to check if it is a MS Powerpoint file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS Powerpoint file mime type, otherwise
	 * false
	 * @access public
	 */
	public static function isMicrosoftPPTFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/vnd.ms-powerpoint') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a MS PowerPoint mime type. Checks only for .pptx files
	 *
	 * @param mixed $mimetype The value to check if it is a MS PowerPoint file mime type
	 *
	 * @return boolean $valid Returns true if the value is a MS PowerPoint file mime type, otherwise
	 * false
	 * @access public
	 */
	public static function isMicrosoftPPTXFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a PDF mime type.
	 *
	 * @param mixed $mimetype The value to check if it is a PDF file mime type
	 *
	 * @return boolean $valid Returns true if the value is a PDF file mime type, otherwise false
	 * @access public
	 */
	public static function isPdfFile($mimetype) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $mimetype);

		$mimetype = self::_applyFilter(get_class(), __FUNCTION__, $mimetype, array('event' => 'args'));
		$validation = false;

		if ($mimetype == 'application/pdf') {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $mimetype);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isMidiFile

	/**
	 * Checks if a value passed has a valid email.
	 *
	 * @param mixed $email The value to check if it is a valid email.
	 *
	 * @return boolean $valid Returns true if the value is a valid email, otherwise false
	 * @access public
	 */
	public static function isValidEmail($email) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $email);

		$email = self::_applyFilter(get_class(), __FUNCTION__, $email, array('event' => 'args'));
		$validation = false;

		if (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $email);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isValidEmail

	/**
	 * Checks if a value passed has a valid url.
	 *
	 * @param mixed $url The value to check if it is a valid url.
	 *
	 * @return boolean $valid Returns true if the value is a valid url, otherwise false
	 * @access public
	 */
	public static function isValidUrl($url) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));
		$validation = false;

		if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $url);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}

	/**
	 * Attempts to check if the url is an active url. Response should be 200.
	 *
	 * @param string $url The url to check if active
	 *
	 * @return boolean
	 * @todo rewrite with Communicator
	 */
	public static function isActiveUrl($url) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));
		$valid_url = @fsockopen($url, 80, $errno, $errstr, 30);
		$validation = false;

		if ($valid_url) {
			$validation = true;
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $url);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end isActiveUrl

	/**
	 * Checks a files mime type and returns true if the mime type is found, otherwise false.
	 *
	 * @param string $file The path to the file to be be checked
	 * @param string $mime_text Some form of text that describes the mime type.
	 * @param array $options Options that can customize how the mime type is to be found.
	 * 			-'search_method' _string_: The search method can either be found using strpos or preg_match.
	 * 			The default is STRING_POSITION as the method, change to PREG_MATCH to use PREG_MATCH
	 * 			-'magic_file' _string_: If you have phpinfo installed, it will be used for finding the
	 * mime_type. The
	 * 			default magic file is not set and will use the default in the FileManager.
	 *
	 * @return boolean $found Returns true if the mime type was match, otherwise false
	 * @access public
	 */
	public static function checkFileMimeType($file, $mime_text, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file, $mime_text, $options);

		$defaults = array('search_method' => 'STRING_POSITION');
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'file' => $file,
			'options' => $options,
			'mime_text' => $mime_text
		), array('event' => 'args'));
		$file = $filtered['file'];
		$options = $filtered['options'];
		$mime_text = $filtered['mime_text'];
		$validation = false;

		extract($options);
		$mime_type = FileManager::getFileMimeType($file, $options);

		if ($search_method == 'STRING_POSITION') {
			$pos = strpos($mime_type, $mime_text);

			if ($pos === false) {
				$validation = false;
			}

			$validation = true;
		} else if ($search_method == 'PREG_MATCH') {

			if (preg_match($mime_text, $mime_type)) {
				$validation = true;
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $validation, $file, $mime_text, $options);
		$validation = self::_applyFilter(get_class(), __FUNCTION__, $validation, array('event' => 'return'));

		return $validation;
	}//end

}//end class
?>

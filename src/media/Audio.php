<?php
namespace prodigyview\media;

use prodigyview\design\StaticObject;

/**
 * Audio is a class designed to manipulate audio files and transcoding to various formats.
 *
 * Audio works with all kinds of audio files: mp3, wave, real audio, etc. It utilizes command tools
 * like FFMPEG to do the transcoding and will return the results from the command line.
 *
 * Example:
 * ```php
 * //Set the file to be converted
 * $old_file = '/path/to/file/audio.wav';
 *
 * //Set the path of the new file
 * $new_file =  '/path/to/file/audio.mp3';
 *
 * //Options to pass to the FFmpeg or other conversion tools
 * //The following will place a -f infront of the input
 * $options = array('input_f' => '');
 *
 * //Run the conversion
 * Audio::init();
 * Audio::convertAudioFile($old_file, $new_file , $options );
 * ```
 *
 * @package media
 */
class Audio {
	
	use StaticObject;

	/**
	 * The type of convet to use, default is ffmpeg
	 */
	protected static $converter = 'ffmpeg';
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initialize the static class. Currently can be used for modifying the default converter
	 * tool and its location, which is simply ffmpeg.
	 *
	 * @param array $config An array of configurations
	 *			-'converter' _string_: The converter tool and its location. Default is ffmpeg
	 *
	 * @return void
	 * @access public
	 */
	public static function init($config = array()) {
			
		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);
		
		if(!self::$_initialized) {
	
			$defaults = array('converter' => 'ffmpeg');
	
			$config += $defaults;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$converter = $config['converter'];
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}

	/**
	 * Converts a sound file from one format to a different one or one with different attribute. The
	 * convert is
	 * executed on the command line and by default is set to use ffmpeg.
	 *
	 * @param string $current_file_location The location of the current file to be converted.
	 * @param string $new_file_location The location to output the new file once converted.
	 * @param array $options Options that can control how the conversion takes place.
	 * 			'conveter' _string_: The convert to be used and the location. Default is ffmpeg. To further
	 * define
	 * 			either added the path to the converter +ffmpeg or path to another converter besides ffmpeg.
	 * 			'input_' array: Should be an array that of options for how to treat the input file. The options
	 * 			should be the same options passed through the setEncodingOptions except the prefix should have
	 * 'input_'.
	 * 			For example if the option is 'ar' as in setEncodingOptions, add 'input_ar' as the option key.
	 * 			'output_' array: Should be an array that of options for how to treat the output file. The
	 * options
	 * 			should be the same options passed through the setEncodingOptions except the prefix should have
	 * 'output_'.
	 * 			For example if the option is 'ar' as in setEncodingOptions, add 'input_ar' as the option key.
	 *
	 * @return void The output is not returned but a new file will be created if the conversion succeeded
	 * @access public
	 */
	public static function convertAudioFile($current_file_location, $new_file_location, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $current_file_location, $new_file_location, $options);

		if (!is_array($options)) {
			$options = array();
		}

		$defaults = array('converter' => self::$converter);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'current_file_location' => $current_file_location,
			'new_file_location' => $new_file_location,
			'options' => $options
		), array('event' => 'args'));
		
		$current_file_location = $filtered['current_file_location'];
		$new_file_location = $filtered['new_file_location'];
		$options = $filtered['options'];

		$converter = $options['converter'];

		$input_options = self::setEncodingOptions($options, 'input_');
		$output_options = self::setEncodingOptions($options, 'output_');

		exec("$converter $input_options -i $current_file_location -y $output_options $new_file_location ");
		self::_notify(get_class() . '::' . __FUNCTION__, $current_file_location, $new_file_location, $options, $input_options, $output_options);
	}//end convertAudioFile

	/**
	 * The encoding options on how to encode a file using FFMPPEG. The options should be run in a command
	 * line
	 * formated.
	 * @see http://www.ffmpeg.org/ffmpeg.html
	 * @see http://www.ffmpeg.org/ffmpeg.html#Audio-Options
	 * @see http://www.ffmpeg.org/ffmpeg.html#Advanced-Audio-options_003a
	 *
	 * @param array $options Defined options to be used in the conversion. Options relate to those passed
	 * in a normal
	 * 		  FFMPEG command line fashion.The key of the array corresponds the command and the value
	 * responds to the command
	 * 		  value.
	 * @param string $input_type If the options have a prefix in front of the key, the prefix should be
	 * defined either.
	 *
	 * @return string $options A string of options that should be used on the command line with ffmpeg
	 * @access public
	 *
	 * @todo find ffmpeg documentation and use isset to remove notices
	 */
	public static function setEncodingOptions($options = array(), $input_type = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options, $input_type);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'input_type' => $input_type,
			'options' => $options
		), array('event' => 'args'));
		
		$input_type = $filtered['input_type'];
		$options = $filtered['options'];

		$input_options = '';

		if (isset($options[$input_type . 'f'])) {
			$input_options .= ' -f ' . $options[$input_type . 'f'];
		}

		if (isset($options[$input_type . 't'])) {
			$input_options .= ' -t ' . $options[$input_type . 't'];
		}

		if (isset($options[$input_type . 'fs'])) {
			$input_options .= ' -fs ' . $options[$input_type . 'fs'];
		}

		if (isset($options[$input_type . 'ss'])) {
			$input_options .= ' -ss ' . $options[$input_type . 'ss'];
		}

		if (isset($options[$input_type . 'aframes'])) {
			$input_options .= ' -aframes ' . $options[$input_type . 'aframes'];
		}

		if (isset($options[$input_type . 'ar'])) {
			$input_options .= ' -ar ' . $options[$input_type . 'ar'];
		}

		if (isset($options[$input_type . 'ab'])) {
			$input_options .= ' -ab ' . $options[$input_type . 'ab'];
		}

		if (isset($options[$input_type . 'aq'])) {
			$input_options .= ' -aq ' . $options[$input_type . 'aq'];
		}

		if (isset($options[$input_type . 'ac'])) {
			$input_options .= ' -ac ' . $options[$input_type . 'ac'];
		}

		if (isset($options[$input_type . 'an'])) {
			$input_options .= ' -an ';
		}

		if (isset($options[$input_type . 'acodec'])) {
			$input_options .= ' -acodec ' . $options[$input_type . 'acodec'];
		}

		if (isset($options[$input_type . 'codec'])) {
			$input_options .= ' -codec ' . $options[$input_type . 'codec'];
		}

		if (isset($options[$input_type . 'newaudio'])) {
			$input_options .= ' -newaudio ';
		}

		if (isset($options[$input_type . 'alang'])) {
			$input_options .= ' -alang ' . $options[$input_type . 'alang'];
		}

		if (isset($options[$input_type . 'atag'])) {
			$input_options .= ' -atag ' . $options[$input_type . 'atag'];
		}

		if (isset($options[$input_type . 'audio_service_type'])) {
			$input_options .= ' -audio_service_type ' . $options[$input_type . 'audio_service_type'];
		}

		if (isset($options[$input_type . 'absf'])) {
			$input_options .= ' -absf ' . $options[$input_type . 'absf'];
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $input_options, $options, $input_type);
		$input_options = self::_applyFilter(get_class(), __FUNCTION__, $input_options, array('event' => 'return'));

		return $input_options;
	}//end setEncodingOptions

	/**
	 * Get the duration of an audio file.
	 *
	 * @param string $file The location of the audio file to calculate the duration of
	 *
	 * @return string $duration The duration of the audio file
	 * @access public
	 * @todo Add in options of choosing the converter
	 */
	public static function getDuration($file) {

		ob_start();
		passthru("ffmpeg -i \"{$file}\" 2>&1");
		$duration = ob_get_contents();
		ob_end_clean();

		$search = '/Duration: (.*?),/';
		$duration = preg_match($search, $duration, $matches, PREG_OFFSET_CAPTURE, 3);

		return $matches[1][0];
	}

}//end class AudioRenderer
?>
